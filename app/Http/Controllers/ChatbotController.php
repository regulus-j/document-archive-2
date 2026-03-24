<?php

namespace App\Http\Controllers;

use App\Services\DocumentAccessService;
use App\Services\DocumentContentExtractorService;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected DocumentAccessService $documentAccessService;
    protected DocumentContentExtractorService $contentExtractor;
    protected OllamaService $ollama;

    const MAX_CONTENT_CHARS = 1500;
    const MAX_FILE_CONTENT_CHARS = 4000;
    const MAX_CONTEXT_DOCS  = 5;
    const MAX_HISTORY_TURNS = 6;

    public function __construct(
        DocumentAccessService $documentAccessService,
        DocumentContentExtractorService $contentExtractor,
        OllamaService $ollama
    ) {
        $this->documentAccessService = $documentAccessService;
        $this->contentExtractor = $contentExtractor;
        $this->ollama = $ollama;
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:1000',
            'history'           => 'nullable|array|max:' . self::MAX_HISTORY_TURNS,
            'history.*.role'    => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:3000',
        ]);

        $userMessage = trim($validated['message']);
        $history     = $validated['history'] ?? [];

        // Truncate long history entries server-side to cap prompt size
        $history = array_map(function ($turn) {
            $turn['content'] = Str::limit($turn['content'], 1200);
            return $turn;
        }, $history);

        $intent = $this->detectIntent($userMessage);

        // --- Handle summarize & read_content locally (Ollama, no external API call) ---
        if ($intent === 'summarize') {
            return $this->handleSummarizeLocally($userMessage, $history);
        }

        if ($intent === 'read_content') {
            return $this->handleReadContentLocally($userMessage, $history);
        }

        // --- All other intents: build context → send to Ollama LLM ---
        // Resolve follow-up references from conversation history for document-specific intents
        $resolvedMessage = $userMessage;
        $docSpecificIntents = ['workflow', 'question', 'search'];
        if (in_array($intent, $docSpecificIntents)) {
            $resolvedMessage = $this->resolveMessageFromHistory($userMessage, $history);
        }

        $contextBlock  = '';
        $documentLinks = [];

        switch ($intent) {
            case 'pending':
                [$contextBlock, $documentLinks] = $this->buildPendingContext();
                break;
            case 'stats':
                $contextBlock = $this->buildStatsContext();
                break;
            case 'recent':
                [$contextBlock, $documentLinks] = $this->buildRecentContext();
                break;
            case 'search':
                [$contextBlock, $documentLinks] = $this->buildSearchContext($resolvedMessage);
                break;
            case 'question':
                [$contextBlock, $documentLinks] = $this->buildDocumentContext($resolvedMessage, 'question');
                break;
            case 'workflow':
                [$contextBlock, $documentLinks] = $this->buildWorkflowContext($resolvedMessage);
                break;
        }

        $fullPrompt = $this->buildFullPrompt($userMessage, $history, $contextBlock, $intent);

        try {
            $reply = $this->ollama->chat($fullPrompt);
        } catch (\Exception $e) {
            Log::error('Chatbot Ollama error: ' . $e->getMessage());
            return response()->json([
                'reply'     => 'I encountered an error reaching the AI service. Please try again in a moment.',
                'documents' => [],
            ]);
        }

        return response()->json([
            'reply'     => $reply,
            'documents' => $documentLinks,
        ]);
    }

    /* ----------------------------------------------------------------
     *  LOCAL LLM HANDLERS (Ollama — no external API call)
     * ---------------------------------------------------------------- */

    /**
     * Handle the "summarize" intent using Ollama (local LLM).
     * Falls back to returning raw content if Ollama is unavailable.
     */
    private function handleSummarizeLocally(string $message, array $history = []): \Illuminate\Http\JsonResponse
    {
        $docId       = $this->extractDocumentId($message);
        $trackingNo  = $this->extractTrackingNumber($message);
        $searchTerm  = $this->extractSearchTerm($message);

        // If message is a follow-up ("summarize it", "summarize"), resolve from history
        if (!$docId && !$trackingNo && (empty($searchTerm) || $this->isFollowUpReference($message))) {
            [$docId, $trackingNo] = $this->extractDocRefFromHistory($history);

            // If still no reference, try using previous user search terms
            if (!$docId && !$trackingNo) {
                $searchTerm = $this->extractSearchTermFromHistory($history);
            }
        }

        $query = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'categories', 'user', 'originatingOffice']);

        if ($trackingNo) {
            $query->whereHas('trackingNumber', function ($tq) use ($trackingNo) {
                $tq->where('tracking_number', $trackingNo);
            });
        } elseif ($docId) {
            $query->where('id', $docId);
        } elseif ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('trackingNumber', function ($tq) use ($searchTerm) {
                      $tq->where('tracking_number', 'like', "%{$searchTerm}%");
                  });
            });
        } else {
            return response()->json([
                'reply'     => "Please specify which document to summarize by title, tracking number, or ID.\n\nFor example:\n- \"Summarize document #42\"\n- \"Summary of Budget Report\"\n- \"Summarize ZIE-DOC-XXXX\"",
                'documents' => [],
            ]);
        }

        $docs = $query->latest()->limit(1)->get();

        if ($docs->isEmpty()) {
            return response()->json([
                'reply'     => 'No matching document found. Please verify the document title or ID.',
                'documents' => [],
            ]);
        }

        $doc = $docs->first();

        // Extract content from file
        $extraction = $this->contentExtractor->extract($doc);

        if (empty($extraction['content'])) {
            // Even without file content, provide a metadata-based summary
            $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
            $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
            $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';
            $office   = $doc->originatingOffice ? $doc->originatingOffice->name : 'N/A';
            $fileExt  = strtolower(pathinfo($doc->path ?? '', PATHINFO_EXTENSION));

            $reply = "**{$doc->title}** (ID: {$doc->id})\n";
            $reply .= "Tracking #: **{$tracking}** | Status: **{$status}**\n";
            $reply .= "By: {$uploader} | Office: {$office}\n";
            $reply .= "Category: {$cats} | Type: .{$fileExt} | Date: {$doc->created_at->format('M d, Y')}\n";

            if ($doc->description) {
                $reply .= "Description: {$doc->description}\n";
            }

            $errorMsg = $extraction['error'] ?? 'No text content could be extracted.';
            $reply .= "\n*Note: {$errorMsg}*\n";
            $reply .= "You can view or download the file directly from the document detail page.";

            return response()->json([
                'reply'     => $reply,
                'documents' => [[
                    'id'    => $doc->id,
                    'title' => $doc->title,
                    'url'   => route('documents.show', $doc->id),
                ]],
            ]);
        }

        // Build document header
        $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
        $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
        $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
        $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';

        $reply = "**Summary of: {$doc->title}**\n";
        $reply .= "Tracking #: **{$tracking}** | Status: **{$status}** | By: {$uploader}\n";
        $reply .= "Category: {$cats} | Date: {$doc->created_at->format('M d, Y')}\n\n";

        // Use Ollama for summarization
        $result = $this->ollama->summarize($extraction['content'], $doc->title);

        if (!empty($result['summary'])) {
            $reply .= $result['summary'];
        } else {
            // Fallback: show truncated content if Ollama is down
            $fallbackMsg = $result['error'] ?? 'Local AI is unavailable.';
            Log::warning('Ollama summarize fallback', ['doc_id' => $doc->id, 'error' => $fallbackMsg]);
            $reply .= "*({$fallbackMsg} Showing document excerpt instead.)*\n\n";
            $reply .= Str::limit($extraction['content'], 1500);
        }

        return response()->json([
            'reply'     => $reply,
            'documents' => [[
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ]],
        ]);
    }

    /**
     * Handle the "read_content" intent locally — returns extracted document
     * content directly without calling the LLM.
     */
    private function handleReadContentLocally(string $message, array $history = []): \Illuminate\Http\JsonResponse
    {
        $docId       = $this->extractDocumentId($message);
        $trackingNo  = $this->extractTrackingNumber($message);
        $searchTerm  = $this->extractSearchTerm($message);

        // If message is a follow-up, resolve from history
        if (!$docId && !$trackingNo && (empty($searchTerm) || $this->isFollowUpReference($message))) {
            [$docId, $trackingNo] = $this->extractDocRefFromHistory($history);

            if (!$docId && !$trackingNo) {
                $searchTerm = $this->extractSearchTermFromHistory($history);
            }
        }

        $query = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'categories', 'user', 'attachments', 'originatingOffice']);

        if ($trackingNo) {
            $query->whereHas('trackingNumber', function ($tq) use ($trackingNo) {
                $tq->where('tracking_number', $trackingNo);
            });
        } elseif ($docId) {
            $query->where('id', $docId);
        } elseif ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('trackingNumber', function ($tq) use ($searchTerm) {
                      $tq->where('tracking_number', 'like', "%{$searchTerm}%");
                  });
            });
        } else {
            return response()->json([
                'reply'     => "Please specify which document to read by title, tracking number, or ID.\n\nFor example:\n- \"Read document #42\"\n- \"Show content of Budget Report\"\n- \"Read ZIE-DOC-XXXX\"",
                'documents' => [],
            ]);
        }

        $docs = $query->latest()->limit(1)->get();

        if ($docs->isEmpty()) {
            return response()->json([
                'reply'     => 'No matching document found. Please verify the document title or ID.',
                'documents' => [],
            ]);
        }

        $doc = $docs->first();

        // Force file extraction for freshest content
        $extraction = $this->contentExtractor->extract($doc, true);

        if (empty($extraction['content']) && !empty($doc->content)) {
            $extraction = [
                'content' => $doc->content,
                'source'  => 'database',
                'error'   => null,
            ];
        }

        // Build document header
        $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
        $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
        $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
        $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';
        $office   = $doc->originatingOffice ? $doc->originatingOffice->name : 'N/A';
        $fileExt  = strtolower(pathinfo($doc->path ?? '', PATHINFO_EXTENSION));

        $reply = "**{$doc->title}** (ID: {$doc->id})\n";
        $reply .= "Tracking #: **{$tracking}** | Status: **{$status}**\n";
        $reply .= "By: {$uploader} | Office: {$office}\n";
        $reply .= "Category: {$cats} | Type: .{$fileExt} | Date: {$doc->created_at->format('M d, Y')}\n";

        if ($doc->description) {
            $reply .= "Description: " . Str::limit($doc->description, 200) . "\n";
        }

        if (!empty($extraction['content'])) {
            $contentText = Str::limit($extraction['content'], self::MAX_FILE_CONTENT_CHARS);
            $reply .= "\n**Document Content:**\n{$contentText}";
        } else {
            $errorMsg = $extraction['error'] ?? 'No text content could be extracted.';
            $reply .= "\n*Content not available: {$errorMsg}*\n";
            $reply .= "You can download or preview the file directly from the document detail page.";
        }

        // Include attachment info (names only, not full content)
        if ($doc->attachments && $doc->attachments->count() > 0) {
            $reply .= "\n\n**Attachments ({$doc->attachments->count()}):**";
            foreach ($doc->attachments->take(5) as $att) {
                $reply .= "\n- {$att->filename}";
            }
        }

        return response()->json([
            'reply'     => $reply,
            'documents' => [[
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ]],
        ]);
    }

    /* ----------------------------------------------------------------
     *  INTENT DETECTION
     * ---------------------------------------------------------------- */

    private function detectIntent(string $message): string
    {
        $lower = strtolower($message);

        // Pending / action-needed
        $pendingKeywords = ['pending', 'waiting', 'my pending', 'pending documents', 'what pending',
                            'show pending', 'awaiting', 'needs attention', 'for review', 'need my action',
                            'action needed', 'needs my approval', 'waiting for me'];
        foreach ($pendingKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'pending';
        }

        // Statistics / counts
        $statsKeywords = ['how many', 'count', 'total', 'statistics', 'stats', 'dashboard',
                          'overview', 'my numbers', 'breakdown'];
        foreach ($statsKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'stats';
        }

        // Recent activity
        $recentKeywords = ['recent', 'latest', 'new documents', 'what\'s new', 'last uploaded',
                           'recently', 'today', 'this week'];
        foreach ($recentKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'recent';
        }

        // Workflow / tracking
        $workflowKeywords = ['track', 'tracking', 'where is', 'status of', 'workflow',
                             'who has', 'forwarded to', 'sent to', 'progress of'];
        foreach ($workflowKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'workflow';
        }

        // Read actual document content
        $readContentKeywords = ['read document', 'read the document', 'read content', 'read the content',
                                'show content', 'show the content', 'show me the content',
                                'open document', 'open the document', 'view content', 'view the content',
                                'full content', 'full text', 'document text', 'file content',
                                'what does it say', 'what does the document say', 'read file',
                                'extract content', 'extract text', 'get content', 'get the content',
                                'read doc', 'read the doc', 'actual content', 'document content'];
        foreach ($readContentKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'read_content';
        }

        // Summarize
        $summarizeKeywords = ['summarize', 'summary', 'summarise', 'give me a summary', 'brief overview', 'tldr'];
        foreach ($summarizeKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'summarize';
        }

        // Question about specific doc
        $questionKeywords = ['what is in', 'when is', 'who is', 'due date', 'deadline', 'amount', 'signed by',
                             'approved by', 'content of', 'details of', 'tell me about document'];
        foreach ($questionKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'question';
        }

        // Search
        $searchKeywords = ['find', 'search', 'show me', 'list', 'documents about', 'documents related to',
                           'look for', 'get documents'];
        foreach ($searchKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'search';
        }

        // How-to (default fallback)
        $howtoKeywords = ['how do i', 'how to', 'how can i', 'where do i', 'help me', 'guide',
                          'navigate', 'archive', 'upload', 'forward', 'receive'];
        foreach ($howtoKeywords as $kw) {
            if (str_contains($lower, $kw)) return 'howto';
        }

        return 'howto';
    }

    /* ----------------------------------------------------------------
     *  CONTEXT BUILDERS
     * ---------------------------------------------------------------- */

    private function buildSearchContext(string $message): array
    {
        $searchTerm = $this->extractSearchTerm($message);

        if (empty($searchTerm)) {
            return ['', []];
        }

        $docs = $this->documentAccessService->getAccessibleDocuments()
            ->select(['id', 'title', 'description', 'category', 'created_at'])
            ->with(['status', 'trackingNumber', 'categories', 'user'])
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%")
                  ->orWhereHas('trackingNumber', function ($tq) use ($searchTerm) {
                      $tq->where('tracking_number', 'like', "%{$searchTerm}%");
                  });
            })
            ->latest()
            ->limit(10)
            ->get();

        if ($docs->isEmpty()) {
            return ["No documents were found matching \"{$searchTerm}\".", []];
        }

        $contextLines = ["Found {$docs->count()} document(s) matching \"{$searchTerm}\":"];
        $links = [];

        foreach ($docs as $doc) {
            $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
            $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
            $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';

            $contextLines[] = "- [{$doc->id}] \"{$doc->title}\" | Status: {$status} | Tracking: {$tracking} | By: {$uploader} | Category: {$cats} | Date: {$doc->created_at->format('M d, Y')}";
            if ($doc->description) {
                $contextLines[] = '  Description: ' . Str::limit($doc->description, 150);
            }
            $links[] = [
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ];
        }

        return [implode("\n", $contextLines), $links];
    }

    private function buildDocumentContext(string $message, string $mode): array
    {
        $docId      = $this->extractDocumentId($message);
        $searchTerm = $this->extractSearchTerm($message);

        $query = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'categories', 'user', 'documentWorkflow.sender', 'documentWorkflow.recipient', 'attachments', 'originatingOffice']);

        if ($docId) {
            $query->where('id', $docId);
        } elseif ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('trackingNumber', function ($tq) use ($searchTerm) {
                      $tq->where('tracking_number', 'like', "%{$searchTerm}%");
                  });
            });
        } else {
            return ['Please specify which document you are asking about by title, tracking number, or ID.', []];
        }

        $docs = $query->latest()->limit(self::MAX_CONTEXT_DOCS)->get();

        if ($docs->isEmpty()) {
            return ['No matching document found. Please verify the document title, tracking number, or ID.', []];
        }

        $contextLines = [];
        $links        = [];

        foreach ($docs as $doc) {
            // Use the content extractor service to get actual document content
            $extraction = $this->contentExtractor->extract($doc);
            $contentSnippet = !empty($extraction['content'])
                ? Str::limit($extraction['content'], self::MAX_CONTENT_CHARS)
                : ($extraction['error'] ?? '[Content not available — the file must be opened directly.]');

            $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
            $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
            $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';
            $office   = $doc->originatingOffice ? $doc->originatingOffice->name : 'N/A';
            $attachCount = $doc->attachments->count();

            $contextLines[] = "=== Document: {$doc->title} (ID: {$doc->id}) ===";
            $contextLines[] = "Tracking #: {$tracking}";
            $contextLines[] = "Status: {$status}";
            $contextLines[] = "Uploaded by: {$uploader}";
            $contextLines[] = "From office: {$office}";
            $contextLines[] = "Category: {$cats}";
            $contextLines[] = "Attachments: {$attachCount} file(s)";
            $contextLines[] = "Created: {$doc->created_at->format('M d, Y h:i A')}";
            $contextLines[] = 'Description: ' . ($doc->description ?? 'None');

            // Workflow steps
            if ($doc->documentWorkflow && $doc->documentWorkflow->count() > 0) {
                $contextLines[] = "Workflow steps:";
                foreach ($doc->documentWorkflow->sortBy('step_order') as $step) {
                    $sender    = $step->sender ? trim($step->sender->first_name . ' ' . $step->sender->last_name) : 'Unknown';
                    $recipient = $step->recipient ? trim($step->recipient->first_name . ' ' . $step->recipient->last_name) : 'Unknown';
                    $urgency   = $step->urgency ?? 'Normal';
                    $dueDate   = $step->due_date ? \Carbon\Carbon::parse($step->due_date)->format('M d, Y') : 'No due date';
                    $wfStatus  = ucfirst($step->status ?? 'pending');
                    $contextLines[] = "  Step {$step->step_order}: {$sender} → {$recipient} | Status: {$wfStatus} | Urgency: {$urgency} | Due: {$dueDate}";
                    if ($step->purpose) {
                        $contextLines[] = "    Purpose: " . Str::limit($step->purpose, 100);
                    }
                    if ($step->remarks) {
                        $contextLines[] = "    Remarks: " . Str::limit($step->remarks, 100);
                    }
                }
            }

            $contextLines[] = "Content:\n{$contentSnippet}";
            $contextLines[] = '';

            $links[] = [
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ];
        }

        return [implode("\n", $contextLines), $links];
    }

    private function buildPendingContext(): array
    {
        $user = Auth::user();

        // Documents where current user is a pending workflow recipient
        $docs = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'user', 'categories', 'originatingOffice', 'documentWorkflow' => function ($q) use ($user) {
                $q->where('recipient_id', $user->id)
                  ->whereIn('status', ['pending', 'for_review', 'awaiting', 'waiting']);
            }])
            ->where(function ($query) use ($user) {
                // Documents with pending workflow steps for this user
                $query->whereHas('documentWorkflow', function ($q) use ($user) {
                    $q->where('recipient_id', $user->id)
                      ->whereIn('status', ['pending', 'for_review', 'awaiting', 'waiting']);
                })
                // OR documents with pending status uploaded by others and accessible
                ->orWhereHas('status', function ($q) {
                    $q->whereIn('status', ['pending', 'for_review', 'awaiting', 'waiting']);
                });
            })
            ->latest()
            ->limit(15)
            ->get();

        if ($docs->isEmpty()) {
            return ["You have no pending documents at this time. All caught up! 🎉", []];
        }

        $contextLines = ["You have {$docs->count()} document(s) needing attention:"];
        $links = [];

        foreach ($docs as $doc) {
            $statusText   = $doc->status ? ucfirst($doc->status->status) : 'Pending';
            $uploaderName = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
            $tracking     = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $cats         = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';
            $office       = $doc->originatingOffice ? $doc->originatingOffice->name : 'N/A';

            $contextLines[] = "- [{$doc->id}] \"{$doc->title}\" | Status: {$statusText} | Tracking: {$tracking} | From: {$uploaderName} ({$office}) | Category: {$cats} | Date: {$doc->created_at->format('M d, Y')}";

            // Show pending workflow step details for this user
            if ($doc->documentWorkflow && $doc->documentWorkflow->count() > 0) {
                foreach ($doc->documentWorkflow as $step) {
                    $urgency = $step->urgency ?? 'Normal';
                    $dueDate = $step->due_date ? \Carbon\Carbon::parse($step->due_date)->format('M d, Y') : 'No due date';
                    $contextLines[] = "  → Your action needed: {$urgency} urgency | Due: {$dueDate}";
                    if ($step->purpose) {
                        $contextLines[] = "    Purpose: " . Str::limit($step->purpose, 120);
                    }
                }
            }

            if ($doc->description) {
                $contextLines[] = '  Description: ' . Str::limit($doc->description, 120);
            }

            $links[] = [
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ];
        }

        return [implode("\n", $contextLines), $links];
    }

    private function buildStatsContext(): string
    {
        $user  = Auth::user();
        $baseQuery = $this->documentAccessService->getAccessibleDocuments();

        $totalDocs    = (clone $baseQuery)->count();
        $myUploads    = (clone $baseQuery)->where('uploader', $user->id)->count();
        $archivedDocs = (clone $baseQuery)->where('is_archived', true)->count();

        $pendingCount = (clone $baseQuery)->whereHas('status', function ($q) {
            $q->whereIn('status', ['pending', 'for_review', 'awaiting', 'waiting']);
        })->count();

        $thisMonthCount = (clone $baseQuery)->where('created_at', '>=', now()->startOfMonth())->count();
        $thisWeekCount  = (clone $baseQuery)->where('created_at', '>=', now()->startOfWeek())->count();

        // Top categories
        $topCategories = (clone $baseQuery)
            ->join('document_category', 'documents.id', '=', 'document_category.doc_id')
            ->join('document_categories', 'document_category.category_id', '=', 'document_categories.id')
            ->selectRaw('document_categories.category, count(*) as cnt')
            ->groupBy('document_categories.category')
            ->orderByDesc('cnt')
            ->limit(5)
            ->pluck('cnt', 'category');

        $lines = [
            "Document statistics for {$user->first_name}:",
            "- Total accessible documents: {$totalDocs}",
            "- Documents you uploaded: {$myUploads}",
            "- Pending / awaiting action: {$pendingCount}",
            "- Archived: {$archivedDocs}",
            "- Created this month: {$thisMonthCount}",
            "- Created this week: {$thisWeekCount}",
        ];

        if ($topCategories->isNotEmpty()) {
            $lines[] = "- Top categories:";
            foreach ($topCategories as $cat => $count) {
                $lines[] = "    • {$cat}: {$count} document(s)";
            }
        }

        return implode("\n", $lines);
    }

    private function buildRecentContext(): array
    {
        $docs = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'user', 'categories', 'originatingOffice'])
            ->latest()
            ->limit(10)
            ->get();

        if ($docs->isEmpty()) {
            return ['No recent documents found.', []];
        }

        $contextLines = ["Here are the {$docs->count()} most recent documents:"];
        $links = [];

        foreach ($docs as $doc) {
            $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
            $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
            $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';
            $timeAgo  = $doc->created_at->diffForHumans();

            $contextLines[] = "- [{$doc->id}] \"{$doc->title}\" | Status: {$status} | Tracking: {$tracking} | By: {$uploader} | Category: {$cats} | {$timeAgo}";

            $links[] = [
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ];
        }

        return [implode("\n", $contextLines), $links];
    }

    private function buildWorkflowContext(string $message): array
    {
        $docId      = $this->extractDocumentId($message);
        $searchTerm = $this->extractSearchTerm($message);

        $query = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'user', 'documentWorkflow.sender', 'documentWorkflow.recipient']);

        if ($docId) {
            $query->where('id', $docId);
        } elseif ($searchTerm) {
            // Also try tracking number search
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhereHas('trackingNumber', function ($tq) use ($searchTerm) {
                      $tq->where('tracking_number', 'like', "%{$searchTerm}%");
                  });
            });
        } else {
            return ['Please specify which document to track by title, ID, or tracking number.', []];
        }

        $docs = $query->latest()->limit(3)->get();

        if ($docs->isEmpty()) {
            return ['No matching document found. Please check the title, ID, or tracking number.', []];
        }

        $contextLines = [];
        $links = [];

        foreach ($docs as $doc) {
            $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
            $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';

            $contextLines[] = "=== Document: {$doc->title} (ID: {$doc->id}) ===";
            $contextLines[] = "Tracking #: {$tracking} | Status: {$status} | Uploaded by: {$uploader}";

            if ($doc->documentWorkflow && $doc->documentWorkflow->count() > 0) {
                $contextLines[] = "Workflow trail ({$doc->documentWorkflow->count()} steps):";
                foreach ($doc->documentWorkflow->sortBy('step_order') as $step) {
                    $sender    = $step->sender ? trim($step->sender->first_name . ' ' . $step->sender->last_name) : 'Unknown';
                    $recipient = $step->recipient ? trim($step->recipient->first_name . ' ' . $step->recipient->last_name) : 'Unknown';
                    $wfStatus  = ucfirst($step->status ?? 'pending');
                    $urgency   = $step->urgency ?? 'Normal';
                    $dueDate   = $step->due_date ? \Carbon\Carbon::parse($step->due_date)->format('M d, Y') : 'No due date';
                    $contextLines[] = "  Step {$step->step_order}: {$sender} → {$recipient} | Status: {$wfStatus} | Urgency: {$urgency} | Due: {$dueDate}";
                    if ($step->remarks) {
                        $contextLines[] = "    Remarks: " . Str::limit($step->remarks, 120);
                    }
                }
            } else {
                $contextLines[] = "No workflow steps found for this document.";
            }

            $contextLines[] = '';

            $links[] = [
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ];
        }

        return [implode("\n", $contextLines), $links];
    }

    /**
     * Build context for the "read_content" intent — reads the actual file content
     * from disk and provides it in full to the AI for answering user questions.
     */
    private function buildReadContentContext(string $message): array
    {
        $docId      = $this->extractDocumentId($message);
        $searchTerm = $this->extractSearchTerm($message);

        $query = $this->documentAccessService->getAccessibleDocuments()
            ->with(['status', 'trackingNumber', 'categories', 'user', 'attachments', 'originatingOffice']);

        if ($docId) {
            $query->where('id', $docId);
        } elseif ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        } else {
            return ['Please specify which document you want me to read by title or ID. For example: "Read document #123" or "Read content of Budget Report".', []];
        }

        // For read_content, limit to 1 document to allow deeper content
        $docs = $query->latest()->limit(1)->get();

        if ($docs->isEmpty()) {
            return ['No matching document found. Please verify the document title or ID.', []];
        }

        $contextLines = [];
        $links        = [];

        foreach ($docs as $doc) {
            // Force reading from file to get the most complete content
            $extraction = $this->contentExtractor->extract($doc, true);

            // If file extraction failed, fall back to DB content
            if (empty($extraction['content']) && !empty($doc->content)) {
                $extraction = [
                    'content' => $doc->content,
                    'source'  => 'database',
                    'error'   => null,
                ];
            }

            $status   = $doc->status ? ucfirst($doc->status->status) : 'N/A';
            $tracking = $doc->trackingNumber ? $doc->trackingNumber->tracking_number : 'N/A';
            $uploader = $doc->user ? trim($doc->user->first_name . ' ' . $doc->user->last_name) : 'Unknown';
            $cats     = $doc->categories->pluck('category')->implode(', ') ?: 'Uncategorized';
            $office   = $doc->originatingOffice ? $doc->originatingOffice->name : 'N/A';
            $fileExt  = strtolower(pathinfo($doc->path ?? '', PATHINFO_EXTENSION));

            $contextLines[] = "=== Document: {$doc->title} (ID: {$doc->id}) ===";
            $contextLines[] = "Tracking #: {$tracking}";
            $contextLines[] = "Status: {$status}";
            $contextLines[] = "Uploaded by: {$uploader}";
            $contextLines[] = "From office: {$office}";
            $contextLines[] = "Category: {$cats}";
            $contextLines[] = "File type: .{$fileExt}";
            $contextLines[] = "Created: {$doc->created_at->format('M d, Y h:i A')}";
            $contextLines[] = 'Description: ' . ($doc->description ?? 'None');
            $contextLines[] = "Content source: {$extraction['source']}";

            if (!empty($extraction['content'])) {
                $contentText = Str::limit($extraction['content'], self::MAX_FILE_CONTENT_CHARS);
                $contextLines[] = "\n--- DOCUMENT CONTENT (extracted from {$extraction['source']}) ---\n{$contentText}\n--- END OF DOCUMENT CONTENT ---";
            } else {
                $errorMsg = $extraction['error'] ?? 'No content could be extracted.';
                $contextLines[] = "\n[Content not available: {$errorMsg}]";
                $contextLines[] = "Suggest the user download or preview the file directly from the document detail page.";
            }

            // Also extract attachment content if any
            if ($doc->attachments && $doc->attachments->count() > 0) {
                $attachmentResults = $this->contentExtractor->extractAttachments($doc, 2);
                if (!empty($attachmentResults)) {
                    $contextLines[] = "\n--- ATTACHMENT CONTENT ---";
                    foreach ($attachmentResults as $att) {
                        $contextLines[] = "Attachment: {$att['filename']}";
                        if (!empty($att['content'])) {
                            $contextLines[] = Str::limit($att['content'], 1000);
                        } else {
                            $contextLines[] = "[{$att['error']}]";
                        }
                        $contextLines[] = '';
                    }
                    $contextLines[] = "--- END OF ATTACHMENTS ---";
                }
            }

            $contextLines[] = '';

            $links[] = [
                'id'    => $doc->id,
                'title' => $doc->title,
                'url'   => route('documents.show', $doc->id),
            ];
        }

        return [implode("\n", $contextLines), $links];
    }

    /* ----------------------------------------------------------------
     *  PROMPT BUILDER
     * ---------------------------------------------------------------- */

    private function buildFullPrompt(string $userMessage, array $history, string $contextBlock, string $intent): string
    {
        $user     = Auth::user();
        $userName = $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : 'User';
        $userName = trim($userName) ?: 'User';

        // Build user context
        $userContext = "Name: {$userName}";
        if ($user) {
            $roles = $user->getRoleNames()->implode(', ') ?: 'User';
            $userContext .= " | Role: {$roles}";

            $offices = $user->offices->pluck('name')->implode(', ');
            if ($offices) {
                $userContext .= " | Office(s): {$offices}";
            }

            $company = $user->company ?? $user->companies->first();
            if ($company) {
                $userContext .= " | Organization: {$company->name}";
            }
        }

        // Use a focused, concise prompt tailored per intent for the small LLM
        $systemPrompt = $this->buildIntentSystemPrompt($intent, $userContext);

        $historyBlock = '';
        if (!empty($history)) {
            // Only keep the most recent turns to limit prompt size
            $recentHistory = array_slice($history, -4);
            foreach ($recentHistory as $turn) {
                $roleLabel     = $turn['role'] === 'user' ? 'User' : 'DocBot';
                $historyBlock .= "{$roleLabel}: {$turn['content']}\n";
            }
        }

        $parts = [$systemPrompt];

        if ($historyBlock) {
            $parts[] = "\n--- Conversation History ---\n{$historyBlock}";
        }

        if ($contextBlock) {
            $parts[] = "\n--- Retrieved Data ---\n{$contextBlock}";
        }

        $parts[] = "\n--- Current User Message ---\nUser: {$userMessage}\nDocBot:";

        return implode("\n", $parts);
    }

    /**
     * Build a focused, concise system prompt per intent to stay within
     * the small model's effective context window.
     */
    private function buildIntentSystemPrompt(string $intent, string $userContext): string
    {
        $base = "You are DocBot, the AI assistant for DocTrack (a document tracking system).\nUser: {$userContext}\n";

        return match ($intent) {
            'pending' => $base . <<<'PROMPT'
Task: Present the user's pending documents from the Retrieved Data.
Rules:
- List each document with its title, tracking number, status, urgency, and due date
- Highlight urgent items and approaching deadlines using **bold**
- Be concise, use bullet points
- Suggest the user can click on a document to take action
PROMPT,

            'stats' => $base . <<<'PROMPT'
Task: Present document statistics from the Retrieved Data in a clear format.
Rules:
- Use bullet points for each statistic
- Highlight key numbers using **bold**
- Be concise and informative
PROMPT,

            'recent' => $base . <<<'PROMPT'
Task: Present the recent documents from the Retrieved Data.
Rules:
- List each document with title, tracking number, status, and upload date
- Use bullet points, be concise
- Suggest follow-up actions like "summarize" or "read"
PROMPT,

            'search' => $base . <<<'PROMPT'
Task: Present search results from the Retrieved Data.
Rules:
- List matching documents with title, tracking number, status, and date
- If no results found, suggest alternative search terms
- Use bullet points, be concise
- Suggest the user can ask to summarize or read any result
PROMPT,

            'question' => $base . <<<'PROMPT'
Task: Answer the user's question about a document using the Retrieved Data.
Rules:
- Answer accurately using ONLY the provided data
- Include relevant details: status, workflow steps, dates, content excerpts
- If the answer isn't in the data, say so clearly
- Be concise but thorough
PROMPT,

            'workflow' => $base . <<<'PROMPT'
Task: Show the document's workflow/tracking information from the Retrieved Data.
Rules:
- Show each workflow step: sender → recipient, status, urgency, due date
- Highlight the current step and pending actions using **bold**
- Be concise, use a clear sequential format
PROMPT,

            'howto' => $base . <<<'PROMPT'
Task: Help the user navigate DocTrack. Use ONLY the instructions below.

Navigation: Dashboard | Documents | Reports | Actions dropdown | Admin dropdown | Notifications (bell) | Profile
Actions menu: Upload Document, Receive, Pending, Completed, Archive, Workflows
Admin menu: Users, Roles, Teams, Document Categories (company-admin) or Companies, Plans, Subscriptions (super-admin)

How to:
- Upload: Actions → Upload Document. Fill title, description, categories, attach file
- Forward: Check "Forward" during upload, OR go to document detail → Forward. Select recipients/offices
- Receive: Actions → Receive. Confirm receipt of incoming documents
- Pending: Actions → Pending. Shows documents awaiting your action
- Search: Documents page. Use title/content search bar or tracking number search bar
- Workflow actions: Actions → Workflows, or from document detail. Options: Approve, Reject, Return, Acknowledge, Refer, Forward, Comment, Review, Sign
- Archive: Actions → Archive to view. Document detail page to archive a specific document
- Reports: Click Reports in top nav
- Profile: Click profile avatar → Profile
- Manual: Profile avatar → Manual

Rules:
- Use exact menu names (e.g. "Actions → Pending" not "go to pending")
- Be concise and direct
- Only answer DocTrack usage questions
PROMPT,

            default => $base . <<<'PROMPT'
Task: Help the user with their DocTrack question using the Retrieved Data if available.
Rules:
- Be concise, friendly, use bullet points and **bold** for important items
- Only discuss documents the user has access to
- Never invent document content
- Suggest follow-up actions when appropriate
PROMPT,
        };
    }

    /* ----------------------------------------------------------------
     *  HELPERS
     * ---------------------------------------------------------------- */

    private function extractDocumentId(string $message): ?int
    {
        // If the message contains a tracking number pattern, strip it out first
        // to avoid matching the year portion (e.g. "2026") as a document ID
        $cleaned = preg_replace('/[A-Z]{2,5}-[A-Z]{2,5}-[A-Z0-9]+-\d{4}/i', '', $message);

        if (preg_match('/(?:document|doc|id|#)\s*#?\s*(\d+)/i', $cleaned, $matches)) {
            return (int) $matches[1];
        }
        // Also match standalone numbers (3+ digits) that aren't years
        if (preg_match('/\b(\d{3,})\b/', $cleaned, $matches)) {
            $num = (int) $matches[1];
            // Skip if it looks like a year (2000-2099)
            if ($num >= 2000 && $num <= 2099) {
                return null;
            }
            return $num;
        }
        return null;
    }

    /**
     * Extract a meaningful search term from a user message by stripping
     * noise words / phrases from anywhere in the string.
     */
    private function extractSearchTerm(string $message): string
    {
        $lower = strtolower(trim($message));

        // First try to pull out a tracking number — if found, use that directly
        if ($tracking = $this->extractTrackingNumber($message)) {
            return $tracking;
        }

        // Remove common noise phrases (order matters — longer/compound first)
        $noisePatterns = [
            'could you (please )?', 'can you (please )?', 'please ',
            'give me a summary of ', 'give me the summary of ',
            'summarize or describe ', 'summarise or describe ',
            'summarize the document ', 'summarize document ', 'summarize the ',
            'summarize ', 'summarise ', 'summary of ', 'summary ',
            'or describe ', 'or summarize ', 'or summarise ',
            'tell me about document ', 'tell me about the ', 'tell me about ',
            'describe the document ', 'describe document ', 'describe the ', 'describe ',
            'content of ', 'details of ', 'detail of ',
            'what is in ', 'what\'s in ',
            'documents related to ', 'documents about ',
            'where is ', 'track ', 'tracking ', 'status of ', 'progress of ', 'workflow of ',
            'who has ', 'forwarded to ', 'sent to ',
            'look for ', 'get documents ', 'show me the ', 'show me ',
            'search for ', 'search ',
            'find documents about ', 'find documents ', 'find ', 'list ',
            'read the content of ', 'read content of ', 'read the document ',
            'read document ', 'read the ', 'read ',
            'show content of ', 'show the content of ', 'open document ', 'open the document ',
            'related to ', 'regarding ', 'about ',
            'the document ', 'document ', 'the doc ', 'doc ',
            'the ', 'a ', 'an ',
        ];

        foreach ($noisePatterns as $pattern) {
            $lower = preg_replace('/\b' . $pattern . '/i', ' ', $lower);
        }

        // Clean up extra whitespace
        $lower = trim(preg_replace('/\s+/', ' ', $lower));

        // If what remains is a follow-up reference like "it", "its content", discard
        if (in_array($lower, ['it', 'its', 'its content', 'this', 'this document', 'that', 'that document', ''])) {
            return '';
        }

        return $lower;
    }

    /**
     * Extract a tracking number pattern from the message (e.g. ZIE-DOC-XXXXX-2026).
     */
    private function extractTrackingNumber(string $message): ?string
    {
        // Match patterns like ZIE-DOC-LZWF7DK1XP-2026 or similar tracking formats
        if (preg_match('/[A-Z]{2,5}-DOC-[A-Z0-9]+-\d{4}/i', $message, $matches)) {
            return strtoupper($matches[0]);
        }
        // Also match generic tracking patterns: PREFIX-XXXX-XXXX
        if (preg_match('/\b[A-Z]{2,5}-[A-Z]{2,5}-[A-Z0-9]{5,}-\d{4}\b/i', $message, $matches)) {
            return strtoupper($matches[0]);
        }
        return null;
    }

    /**
     * Check if a user message is a follow-up reference to a previously mentioned document.
     */
    private function isFollowUpReference(string $message): bool
    {
        $lower = strtolower(trim($message));

        // Exact-match short follow-ups (bare commands referencing a prior document)
        $exactMatches = [
            'summarize', 'summarise', 'summary', 'describe',
            'read', 'read it', 'show', 'show it', 'open', 'open it', 'view', 'view it',
            'yes', 'yes please', 'go ahead', 'do it', 'sure', 'ok', 'okay',
        ];
        if (in_array($lower, $exactMatches, true)) {
            return true;
        }

        $followUpPhrases = [
            'summarize it', 'summarize its content', 'summarise it', 'summarise its content',
            'summarize this', 'summarize that', 'summarize the document',
            'summarize this document', 'summarize that document',
            'summary of it', 'its content', 'its summary', 'show its content',
            'read it', 'read its content', 'read this document', 'read that document',
            'show content', 'show the content', 'open it', 'view it', 'view its content',
            'the first one', 'the second one', 'the last one', 'first one', 'second one',
            'more details', 'tell me more', 'provide more details',
        ];
        foreach ($followUpPhrases as $phrase) {
            if (str_contains($lower, $phrase)) {
                return true;
            }
        }
        // Check for pronoun references
        if (preg_match('/\b(it|its|this|that)\b/', $lower)) {
            return true;
        }
        return false;
    }

    /**
     * Search conversation history (assistant messages) for a document ID, tracking number,
     * or document title. Returns [docId, trackingNumber] — one or both may be null.
     *
     * When multiple documents are found in history, picks the first (most recent) one.
     */
    private function extractDocRefFromHistory(array $history): array
    {
        // Scan history in reverse (most recent first) for doc references in assistant replies
        $reversedHistory = array_reverse($history);

        foreach ($reversedHistory as $turn) {
            if (($turn['role'] ?? '') !== 'assistant') continue;
            $content = $turn['content'] ?? '';

            // Look for tracking number pattern (XXX-DOC-XXXX-YYYY)
            if (preg_match('/[A-Z]{2,5}-DOC-[A-Z0-9]+-\d{4}/i', $content, $m)) {
                return [null, strtoupper($m[0])];
            }
            // Look for document ID pattern like "(ID: 42)" or "document #42" or "[42]"
            if (preg_match('/(?:\(ID:\s*(\d+)\)|document\s*#(\d+)|\[(\d+)\])/i', $content, $m)) {
                $id = (int) ($m[1] ?: ($m[2] ?: $m[3]));
                return [$id, null];
            }
            // Look for document titles in assistant messages (e.g. "Document Title: Research Thesis"  or "**Research Thesis**")
            if (preg_match_all('/(?:Document\s*Title:\s*(.+?)(?:\n|$)|\*\*(.+?)\*\*\s*(?:\(ID|\|))/i', $content, $titleMatches)) {
                $title = trim($titleMatches[1][0] ?: $titleMatches[2][0]);
                if ($title && strlen($title) >= 3) {
                    // Try to find this document by title
                    $doc = $this->documentAccessService->getAccessibleDocuments()
                        ->where('title', 'like', "%{$title}%")
                        ->latest()
                        ->first();
                    if ($doc) {
                        return [$doc->id, null];
                    }
                }
            }
        }

        // Also check user messages for tracking numbers they previously mentioned
        foreach ($reversedHistory as $turn) {
            if (($turn['role'] ?? '') !== 'user') continue;
            $content = $turn['content'] ?? '';

            if (preg_match('/[A-Z]{2,5}-DOC-[A-Z0-9]+-\d{4}/i', $content, $m)) {
                return [null, strtoupper($m[0])];
            }
        }

        // Last resort: search user messages for terms that might be document titles
        foreach ($reversedHistory as $turn) {
            if (($turn['role'] ?? '') !== 'user') continue;
            $content = trim($turn['content'] ?? '');

            // Skip very short or very long messages, pure commands
            if (strlen($content) < 3 || strlen($content) > 100) continue;
            $lower = strtolower($content);
            $skipWords = ['yes', 'no', 'ok', 'okay', 'sure', 'summarize', 'summary', 'read', 'show', 'describe', 'help'];
            if (in_array($lower, $skipWords, true)) continue;

            // Try it as a title search
            $doc = $this->documentAccessService->getAccessibleDocuments()
                ->where('title', 'like', "%{$content}%")
                ->latest()
                ->first();
            if ($doc) {
                return [$doc->id, null];
            }
        }

        return [null, null];
    }

    /**
     * Extract a usable search term from prior user messages in conversation history.
     * Skips bare commands and returns the most recent substantive search term.
     */
    private function extractSearchTermFromHistory(array $history): string
    {
        $skipWords = ['yes', 'no', 'ok', 'okay', 'sure', 'summarize', 'summarise', 'summary',
                      'read', 'show', 'describe', 'help', 'go ahead', 'do it', 'yes please',
                      'read it', 'summarize it', 'open', 'view', 'content'];

        $reversedHistory = array_reverse($history);

        foreach ($reversedHistory as $turn) {
            if (($turn['role'] ?? '') !== 'user') continue;
            $content = trim($turn['content'] ?? '');

            if (strlen($content) < 3 || strlen($content) > 100) continue;
            if (in_array(strtolower($content), $skipWords, true)) continue;

            // Strip common prefixes to get the actual search term
            $term = $this->extractSearchTerm($content);
            if (!empty($term) && strlen($term) >= 3) {
                return $term;
            }
        }

        return '';
    }

    /**
     * When the user message contains a follow-up reference (e.g. "where is it?"),
     * resolve the actual document identifier from conversation history and return
     * a rewritten message that the context builders can process.
     */
    private function resolveMessageFromHistory(string $message, array $history): string
    {
        // If the message already contains a doc ID or tracking number, keep it as-is
        if ($this->extractDocumentId($message) || $this->extractTrackingNumber($message)) {
            return $message;
        }

        // Check if the search term is substantive (not just "it", "that", etc.)
        $searchTerm = $this->extractSearchTerm($message);
        if (!empty($searchTerm) && !$this->isFollowUpReference($message)) {
            return $message;
        }

        // Try to resolve a document reference from history
        [$docId, $trackingNo] = $this->extractDocRefFromHistory($history);

        if ($trackingNo) {
            return $message . ' ' . $trackingNo;
        }
        if ($docId) {
            return $message . ' document #' . $docId;
        }

        // Fall back to previous search terms
        $historyTerm = $this->extractSearchTermFromHistory($history);
        if (!empty($historyTerm)) {
            return $message . ' ' . $historyTerm;
        }

        return $message;
    }
}
