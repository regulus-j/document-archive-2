<?php

namespace App\Http\Controllers;

use App\Services\DocumentAccessService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected DocumentAccessService $documentAccessService;
    protected Client $httpClient;

    const MAX_CONTENT_CHARS = 3000;
    const MAX_CONTEXT_DOCS  = 5;
    const MAX_HISTORY_TURNS = 8;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
        $this->httpClient = new Client(['timeout' => 45.0]);
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:1000',
            'history'           => 'nullable|array|max:' . self::MAX_HISTORY_TURNS,
            'history.*.role'    => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:2000',
        ]);

        $userMessage = trim($validated['message']);
        $history     = $validated['history'] ?? [];

        $intent = $this->detectIntent($userMessage);

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
                [$contextBlock, $documentLinks] = $this->buildSearchContext($userMessage);
                break;
            case 'summarize':
                [$contextBlock, $documentLinks] = $this->buildDocumentContext($userMessage, 'summarize');
                break;
            case 'question':
                [$contextBlock, $documentLinks] = $this->buildDocumentContext($userMessage, 'question');
                break;
            case 'workflow':
                [$contextBlock, $documentLinks] = $this->buildWorkflowContext($userMessage);
                break;
        }

        $fullPrompt = $this->buildFullPrompt($userMessage, $history, $contextBlock, $intent);

        try {
            $reply = $this->callGemini($fullPrompt);
        } catch (\Exception $e) {
            Log::error('Chatbot Gemini error: ' . $e->getMessage());
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
                  ->orWhere('content', 'like', "%{$searchTerm}%");
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
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        } else {
            return ['Please specify which document you are asking about by title or ID.', []];
        }

        $docs = $query->latest()->limit(self::MAX_CONTEXT_DOCS)->get();

        if ($docs->isEmpty()) {
            return ['No matching document found. Please verify the document title or ID.', []];
        }

        $contextLines = [];
        $links        = [];

        foreach ($docs as $doc) {
            $contentSnippet = $doc->content
                ? Str::limit($doc->content, self::MAX_CONTENT_CHARS)
                : '[Content not extracted — the file must be opened directly.]';

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
            ->join('document_category', 'documents.id', '=', 'document_category.document_id')
            ->join('document_categories', 'document_category.document_category_id', '=', 'document_categories.id')
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

        $systemPrompt = <<<SYSTEM
You are DocBot, the AI assistant for DocTrack — a document tracking and archiving system.
You are speaking with: {$userContext}

Your capabilities:
1. **Search** documents by keyword, category, or tracking number.
2. **Summarize** document content and provide key details.
3. **Answer questions** about specific documents (content, status, workflow, due dates, signatories).
4. **Show pending documents** that need the user's attention or action.
5. **Provide statistics** — document counts, activity breakdowns, top categories.
6. **Show recent activity** — latest documents uploaded or received.
7. **Track document workflow** — show who has a document, what step it's on, and its progress.
8. **Guide users** on how to use DocTrack features based on the accurate instructions below.

=== ACCURATE NAVIGATION STRUCTURE ===

Top navigation bar has these items:
- **Dashboard** — the home page (shows stats: total documents, users, teams, incoming documents)
- **Documents** — the main document list page with search bars for title/content and tracking number
- **Reports** — analytics and report generation
- **Actions** dropdown menu with:
  • "Upload Document" — opens the upload form
  • "Receive" — view and confirm receipt of incoming documents forwarded to you
  • "Pending" — view all pending documents awaiting action
  • "Completed" — view completed documents
  • "Archive" — view archived documents
  • "Workflows" — workflow management page showing all your workflow items
- **Admin** dropdown (visible only to admins):
  • For Company Admins: "Users", "Roles", "Teams", "Document Categories"
  • For Super Admins: "Users", "Roles", "Companies", "Plans", "Subscriptions"
- **Notifications** (bell icon) — view notifications
- **User Profile** dropdown: "Profile", "Company Account" (if owner), "Subscription" (if company-admin), "Manual" (user guide), "Log Out"

=== HOW TO USE DOCTRACK (ACCURATE INSTRUCTIONS) ===

**Uploading a Document:**
Go to the **Actions** dropdown in the top navigation bar and click **"Upload Document"**. Fill in the title, description, select categories/purpose, and attach your file(s). You can optionally check "Forward" during upload to immediately forward to recipients.

**Forwarding a Document:**
There are two ways: (1) Check "Forward" during upload to forward immediately, or (2) After upload, go to the document's detail page and click "Forward". Then select the recipients (users) and/or offices, set urgency, due date, and purpose.

**Receiving a Document:**
Go to **Actions → Receive** in the top nav. You will see documents forwarded to you. Click to confirm receipt of each document.

**Viewing Pending Documents:**
Go to **Actions → Pending** in the top nav. This shows all documents awaiting your action.

**Viewing Completed Documents:**
Go to **Actions → Completed** in the top nav.

**Archiving a Document:**
Go to **Actions → Archive** to view archived documents. To archive a specific document, use the document actions on the document detail page.

**Searching for Documents:**
Go to **Documents** in the top nav. The page has two search methods:
1. A general search bar — search by title, content, or description
2. A tracking number search bar — search by the unique tracking number

**Workflow Actions:**
Go to **Actions → Workflows** to see all workflow items. From there or from a document's detail page, you can:
- **Approve** — approve the document
- **Reject** — reject with remarks
- **Return** — return the document to the sender
- **Acknowledge** — acknowledge receipt
- **Refer** — refer the document to another user
- **Forward** — forward to another recipient
- **Comment** — add a comment
- **Review** — submit a formal review
- **Sign** — add your electronic signature

**Recalling / Cancelling a Document:**
From the document detail page, you can "Recall" a forwarded document to pull it back, or "Cancel" the workflow.

**Document Details Page:**
Click any document title to view its detail page. Here you can see full content, attachments, workflow history, status, tracking number, and perform actions like forward, archive, download, preview, or edit.

**Downloading / Previewing:**
From the document detail page, click "Download" to download the file or "Preview" to view it in the browser.

**Managing Teams/Offices (Company Admin):**
Go to **Admin → Teams**. Create teams, assign users to teams, and set team leads. Teams represent offices or departments.

**Managing Document Categories (Company Admin):**
Go to **Admin → Document Categories**. Create, edit, or delete categories used to classify documents.

**Managing Users (Admin):**
Go to **Admin → Users** to view, create, edit, or remove users. Assign roles and teams.

**Managing Roles (Admin):**
Go to **Admin → Roles** to create and manage permission roles.

**Reports:**
Click **Reports** in the top nav. You can view analytics dashboards, generate reports by date range, and download reports.

**Notifications:**
Click the **bell icon** in the top nav to see notifications about documents forwarded to you, workflow actions, and other updates.

**User Manual:**
Click your **profile avatar** in the top-right, then **"Manual"** to view the built-in user guide.

**Profile & Account:**
Click your **profile avatar** → **"Profile"** to update your name, email, or password. Company owners can also access **"Company Account"** settings.

=== RESPONSE GUIDELINES ===
- Only discuss documents the current user has access to. NEVER invent document content.
- If document content isn't extracted (e.g. Excel, scanned images), tell the user the file must be downloaded or previewed directly.
- Be concise and friendly. Use bullet points and numbered lists where helpful.
- Use **bold** for important items like document titles, statuses, and due dates.
- When showing document lists, include tracking numbers and status when available.
- For pending documents, highlight urgency and due dates to help the user prioritize.
- Always refer to exact menu names as documented above (e.g. say "Actions → Pending" not "Documents list → Pending").
- If a question is outside your knowledge, say so clearly rather than guessing.
- Suggest follow-up actions when appropriate (e.g. "Would you like me to summarize any of these?").
SYSTEM;

        $historyBlock = '';
        if (!empty($history)) {
            $recentHistory = array_slice($history, -self::MAX_HISTORY_TURNS);
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

    /* ----------------------------------------------------------------
     *  GEMINI API CALL
     * ---------------------------------------------------------------- */

    private function callGemini(string $prompt): string
    {
        $apiKey    = config('services.gemini.api_key');
        $baseUrl   = config('services.gemini.endpoint');
        $maxTokens = config('services.gemini.max_output_tokens', 2048);

        if (!$apiKey) {
            throw new \RuntimeException('Gemini API key is not configured. Add GEMINI_API_KEY to your .env file.');
        }

        $url = $baseUrl . '?key=' . $apiKey;

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'maxOutputTokens' => (int) $maxTokens,
                'temperature'     => 0.4,
                'topP'            => 0.85,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ],
        ];

        $response = $this->httpClient->post($url, [
            'json'    => $payload,
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            $finishReason = $body['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            if ($finishReason === 'SAFETY') {
                return 'I cannot respond to that request due to content safety restrictions.';
            }
            throw new \RuntimeException('Gemini returned no text content. Finish reason: ' . $finishReason);
        }

        return trim($text);
    }

    /* ----------------------------------------------------------------
     *  HELPERS
     * ---------------------------------------------------------------- */

    private function extractDocumentId(string $message): ?int
    {
        if (preg_match('/(?:document|doc|id|#)\s*#?\s*(\d+)/i', $message, $matches)) {
            return (int) $matches[1];
        }
        // Also match standalone numbers preceded by tracking context
        if (preg_match('/\b(\d{3,})\b/', $message, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function extractSearchTerm(string $message): string
    {
        $lower = strtolower(trim($message));

        $prefixes = [
            'give me a summary of', 'summarize the document', 'summarize document',
            'summarize the', 'summarize', 'summarise', 'summary of', 'summary',
            'tell me about document', 'tell me about', 'content of', 'details of',
            'what is in', 'documents related to', 'documents about',
            'where is', 'track', 'tracking', 'status of', 'progress of', 'workflow of',
            'who has', 'forwarded to', 'sent to',
            'look for', 'get documents', 'show me', 'search for', 'search',
            'find documents about', 'find documents', 'find', 'list',
            'related to', 'regarding', 'about', 'on', 'the', 'for', 'document', 'doc',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($lower, $prefix . ' ')) {
                $lower = substr($lower, strlen($prefix) + 1);
            }
        }

        return trim($lower);
    }
}
