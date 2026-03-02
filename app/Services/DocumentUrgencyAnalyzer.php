<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentWorkflow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * DocumentUrgencyAnalyzer
 *
 * Analyzes document content using the local Ollama LLM and metadata heuristics
 * to automatically determine the urgency/criticality level of a document.
 *
 * ┌─────────────────────────────────────────────────────────────────────────────┐
 * │                     DOCUMENT URGENCY MATRIX FRAMEWORK                      │
 * ├─────────────┬─────────────────────────────────────────────────────────────┤
 * │  LEVEL      │  DESCRIPTION                                                │
 * ├─────────────┼─────────────────────────────────────────────────────────────┤
 * │  CRITICAL   │  Requires immediate action within 4 hours.                  │
 * │             │  Legal deadlines, emergencies, compliance violations,        │
 * │             │  executive orders, safety hazards, court orders.            │
 * ├─────────────┼─────────────────────────────────────────────────────────────┤
 * │  HIGH       │  Must be processed within 24 hours (1 business day).       │
 * │             │  Government submissions, financial deadlines, audit         │
 * │             │  responses, time-sensitive contracts, escalations.          │
 * ├─────────────┼─────────────────────────────────────────────────────────────┤
 * │  MEDIUM     │  Should be processed within 3 business days.               │
 * │             │  Standard business communications, reports, approvals,      │
 * │             │  routine requests, inter-office memos.                     │
 * ├─────────────┼─────────────────────────────────────────────────────────────┤
 * │  LOW        │  Can be processed within 7 business days.                  │
 * │             │  Informational documents, newsletters, FYI notices,         │
 * │             │  reference materials, dissemination documents.             │
 * └─────────────┴─────────────────────────────────────────────────────────────┘
 *
 * ESCALATION TIMELINE:
 *   - Critical: Warning at 2hr, escalate at 4hr
 *   - High:     Warning at 12hr, escalate at 24hr
 *   - Medium:   Warning at 2 days, escalate at 3 days
 *   - Low:      Warning at 5 days, escalate at 7 days
 *
 * ANALYSIS SOURCES (weighted):
 *   1. LLM content analysis (40%) - Semantic understanding of document text
 *   2. Keyword matching (25%)     - Pattern matching against urgency lexicons
 *   3. Document metadata (20%)    - Title, category, purpose, classification
 *   4. Temporal signals (15%)     - Date references, deadline mentions in content
 */
class DocumentUrgencyAnalyzer
{
    protected OllamaService $ollama;
    protected DocumentContentExtractorService $extractor;

    /**
     * Keyword lexicons for each urgency level.
     */
    protected array $urgencyKeywords = [
        'critical' => [
            'urgent', 'emergency', 'immediately', 'asap', 'critical',
            'life-threatening', 'court order', 'subpoena', 'cease and desist',
            'lawsuit', 'legal action', 'violation notice', 'safety hazard',
            'recall', 'injunction', 'restraining order', 'compliance violation',
            'data breach', 'security incident', 'termination', 'suspension',
            'eviction', 'foreclosure', 'final notice', 'last warning',
            'mandatory', 'executive order',
        ],
        'high' => [
            'deadline', 'time-sensitive', 'priority', 'expedite', 'rush',
            'action required', 'response needed', 'overdue', 'past due',
            'audit', 'regulatory', 'government submission', 'tax filing',
            'contract expiration', 'renewal deadline', 'bid submission',
            'procurement', 'tender', 'grant application', 'accreditation',
            'inspection', 'penalty', 'fine', 'sanction', 'escalation',
            'board resolution', 'financial report',
        ],
        'medium' => [
            'request', 'approval', 'review', 'memo', 'memorandum',
            'report', 'proposal', 'recommendation', 'evaluation',
            'assessment', 'plan', 'budget', 'schedule', 'meeting',
            'minutes', 'resolution', 'policy', 'procedure',
            'endorsement', 'clearance', 'referral', 'coordination',
        ],
        'low' => [
            'information', 'fyi', 'for your information', 'newsletter',
            'announcement', 'bulletin', 'circular', 'advisory',
            'dissemination', 'reference', 'archive', 'record',
            'acknowledgment', 'receipt', 'confirmation', 'update',
            'status report', 'progress report', 'routine',
        ],
    ];

    /** Response time thresholds per urgency level (in hours). */
    public const RESPONSE_THRESHOLDS = [
        'critical' => ['warning' => 2,   'escalation' => 4],
        'high'     => ['warning' => 12,  'escalation' => 24],
        'medium'   => ['warning' => 48,  'escalation' => 72],
        'low'      => ['warning' => 120, 'escalation' => 168],
    ];

    /** Due date offsets per urgency level (in business days). */
    public const DUE_DATE_OFFSETS = [
        'critical' => 0,
        'high'     => 1,
        'medium'   => 3,
        'low'      => 7,
    ];

    public function __construct(OllamaService $ollama, DocumentContentExtractorService $extractor)
    {
        $this->ollama = $ollama;
        $this->extractor = $extractor;
    }

    /**
     * Analyze a document and determine its urgency level.
     *
     * @return array{level: string, confidence: int, reasoning: string, keywords: array, suggested_due_date: \Carbon\Carbon, source: string}
     */
    public function analyze(Document $document): array
    {
        $content = $this->getDocumentContent($document);
        $title = $document->title ?? '';
        $description = $document->description ?? '';
        $combinedText = $title . ' ' . $description . ' ' . $content;

        // 1. Keyword analysis (25% weight)
        $keywordResult  = $this->analyzeKeywords($combinedText);

        // 2. Metadata analysis (20% weight)
        $metadataResult = $this->analyzeMetadata($document);

        // 3. Temporal signal analysis (15% weight)
        $temporalResult = $this->analyzeTemporalSignals($combinedText);

        // 4. LLM analysis (40% weight) — only if Ollama is available
        $llmResult      = $this->analyzeLlm($content, $title, $description);

        // Combine all scores with weights
        $combinedScores = $this->combineScores($keywordResult, $metadataResult, $temporalResult, $llmResult);

        $level      = $combinedScores['level'];
        $confidence = $combinedScores['confidence'];
        $source     = $llmResult['available'] ? 'combined' : 'heuristic';
        $reasoning  = $this->buildReasoning($keywordResult, $metadataResult, $temporalResult, $llmResult, $level);
        $dueDate    = $this->calculateDueDate($level);

        // Persist to document
        $document->update([
            'urgency_level'       => $level,
            'urgency_reasoning'   => $reasoning,
            'urgency_keywords'    => json_encode($keywordResult['matched']),
            'urgency_analyzed_at' => now(),
            'urgency_confidence'  => $confidence,
        ]);

        // Also update any active workflows with the urgency and due date
        DocumentWorkflow::where('document_id', $document->id)
            ->whereIn('status', ['pending', 'waiting', 'received'])
            ->update([
                'urgency'  => $level,
                'due_date' => $dueDate,
            ]);

        Log::info('Document urgency analyzed', [
            'document_id' => $document->id,
            'level'       => $level,
            'confidence'  => $confidence,
            'source'      => $source,
        ]);

        return [
            'level'              => $level,
            'confidence'         => $confidence,
            'reasoning'          => $reasoning,
            'keywords'           => $keywordResult['matched'],
            'suggested_due_date' => $dueDate,
            'source'             => $source,
        ];
    }

    /**
     * Extract readable text content from the document.
     */
    protected function getDocumentContent(Document $document): string
    {
        if (!empty($document->content)) {
            return Str::limit($document->content, 3000);
        }

        $result = $this->extractor->extract($document);
        return Str::limit($result['content'] ?? '', 3000);
    }

    /**
     * Analyze text against the urgency keyword lexicons.
     */
    protected function analyzeKeywords(string $text): array
    {
        $text = strtolower($text);
        $matched = [];
        $scores = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];

        foreach ($this->urgencyKeywords as $level => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, strtolower($keyword))) {
                    $matched[] = $keyword;
                    $scores[$level]++;
                }
            }
        }

        // Weighted scoring: critical matches weigh more
        $weightedScores = [
            'critical' => $scores['critical'] * 4,
            'high'     => $scores['high'] * 3,
            'medium'   => $scores['medium'] * 2,
            'low'      => $scores['low'] * 1,
        ];

        arsort($weightedScores);
        $topLevel = array_key_first($weightedScores);
        $totalMatches = array_sum($scores);
        $confidence = min(100, $totalMatches * 15);

        if ($totalMatches === 0) {
            $topLevel = 'medium';
            $confidence = 20;
        }

        return ['level' => $topLevel, 'confidence' => $confidence, 'matched' => $matched, 'scores' => $scores];
    }

    /**
     * Analyze document metadata for urgency signals.
     */
    protected function analyzeMetadata(Document $document): array
    {
        $level = 'medium';
        $confidence = 30;
        $signals = [];

        // Check purpose from workflows
        $workflows = $document->documentWorkflow;
        if ($workflows && $workflows->isNotEmpty()) {
            $purposes = $workflows->pluck('purpose')->unique()->filter();
            foreach ($purposes as $purpose) {
                if ($purpose === 'appropriate_action') {
                    $level = 'high';
                    $signals[] = 'Requires action/approval';
                } elseif ($purpose === 'dissemination') {
                    $level = 'low';
                    $signals[] = 'Dissemination/informational';
                } elseif ($purpose === 'for_comment') {
                    $level = 'medium';
                    $signals[] = 'Requires comment/feedback';
                }
            }
        }

        // Check classification — Private docs may indicate sensitivity
        if ($document->classification === 'Private') {
            if ($level === 'medium') {
                $level = 'high';
            }
            $signals[] = 'Private classification (sensitive)';
            $confidence += 10;
        }

        // Check categories for urgency signals
        $categories = $document->categories->pluck('category')->map(fn($c) => strtolower($c));
        foreach (['legal', 'compliance', 'emergency', 'executive', 'financial'] as $cat) {
            if ($categories->contains(fn($c) => str_contains($c, $cat))) {
                $level = in_array($level, ['low', 'medium']) ? 'high' : $level;
                $signals[] = "Category: {$cat}";
                $confidence += 10;
            }
        }

        return ['level' => $level, 'confidence' => min(100, $confidence), 'signals' => $signals];
    }

    /**
     * Detect temporal/deadline references in text.
     */
    protected function analyzeTemporalSignals(string $text): array
    {
        $signals = [];
        $urgencyBoost = 0;
        $patterns = [
            '/within\s+\d+\s+hours?/i'                     => 3,
            '/due\s+(today|tomorrow|immediately)/i'         => 3,
            '/deadline[:\s]+/i'                              => 2,
            '/expires?\s+(on|by|within)/i'                   => 2,
            '/not\s+later\s+than/i'                          => 2,
            '/on\s+or\s+before/i'                            => 2,
            '/must\s+be\s+(submitted|filed|completed)/i'     => 2,
            '/within\s+\d+\s+(business\s+)?days?/i'          => 1,
            '/as\s+soon\s+as\s+possible/i'                   => 3,
            '/time[- ]?sensitive/i'                           => 2,
        ];

        foreach ($patterns as $pattern => $weight) {
            if (preg_match($pattern, $text)) {
                $signals[] = trim($pattern, '/i');
                $urgencyBoost += $weight;
            }
        }

        $level = match (true) {
            $urgencyBoost >= 6 => 'critical',
            $urgencyBoost >= 4 => 'high',
            $urgencyBoost >= 2 => 'medium',
            default            => 'low',
        };

        return ['level' => $level, 'confidence' => min(100, $urgencyBoost * 15), 'signals' => $signals];
    }

    /**
     * Use the Ollama LLM to semantically analyze document content for urgency.
     */
    protected function analyzeLlm(string $content, string $title, string $description): array
    {
        if (!$this->ollama->isAvailable() || empty(trim($content . $title . $description))) {
            return ['available' => false, 'level' => null, 'confidence' => 0, 'reasoning' => ''];
        }

        $prompt = <<<PROMPT
You are a document urgency classifier for an office document management system. Analyze the following document and classify its urgency level.

URGENCY LEVELS:
- CRITICAL: Requires immediate action (legal deadlines, emergencies, safety issues, court orders, compliance violations). Response within 4 hours.
- HIGH: Time-sensitive, must be processed within 1 business day (government submissions, financial deadlines, audit responses, expiring contracts).
- MEDIUM: Standard processing within 3 business days (routine approvals, reports, memos, proposals, inter-office requests).
- LOW: Informational, can wait up to 7 business days (newsletters, FYI notices, reference materials, routine acknowledgments).

Document Title: {$title}
Document Description: {$description}
Document Content:
{$content}

Respond ONLY in this exact format (no other text):
LEVEL: [critical|high|medium|low]
CONFIDENCE: [0-100]
REASON: [one sentence explaining why]
PROMPT;

        try {
            $result = $this->ollama->generate($prompt, [
                'temperature' => 0.1,
                'top_p'       => 0.8,
                'num_predict' => 128,
            ]);

            if (!empty($result['error'])) {
                Log::warning('LLM urgency analysis failed', ['error' => $result['error']]);
                return ['available' => false, 'level' => null, 'confidence' => 0, 'reasoning' => ''];
            }

            return $this->parseLlmResponse($result['summary']);
        } catch (\Throwable $e) {
            Log::warning('LLM urgency analysis exception', ['error' => $e->getMessage()]);
            return ['available' => false, 'level' => null, 'confidence' => 0, 'reasoning' => ''];
        }
    }

    /**
     * Parse the structured LLM response.
     */
    protected function parseLlmResponse(string $response): array
    {
        $level = null;
        $confidence = 50;
        $reasoning = '';

        if (preg_match('/LEVEL:\s*(critical|high|medium|low)/i', $response, $m)) {
            $level = strtolower($m[1]);
        }
        if (preg_match('/CONFIDENCE:\s*(\d+)/i', $response, $m)) {
            $confidence = min(100, max(0, (int) $m[1]));
        }
        if (preg_match('/REASON:\s*(.+)/i', $response, $m)) {
            $reasoning = trim($m[1]);
        }

        if (!in_array($level, ['critical', 'high', 'medium', 'low'])) {
            $level = null;
        }

        return ['available' => $level !== null, 'level' => $level, 'confidence' => $confidence, 'reasoning' => $reasoning];
    }

    /**
     * Combine scores from all analysis sources with weights.
     */
    protected function combineScores(array $keyword, array $metadata, array $temporal, array $llm): array
    {
        $levelValues = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        $reverseLevels = array_flip($levelValues);

        $totalWeight = 0.60; // keyword(0.25) + metadata(0.20) + temporal(0.15)
        $weightedScore = ($levelValues[$keyword['level']] ?? 2) * 0.25
                       + ($levelValues[$metadata['level']] ?? 2) * 0.20
                       + ($levelValues[$temporal['level']] ?? 2) * 0.15;
        $weightedConfidence = $keyword['confidence'] * 0.25
                            + $metadata['confidence'] * 0.20
                            + $temporal['confidence'] * 0.15;

        if ($llm['available'] && $llm['level']) {
            $weightedScore += ($levelValues[$llm['level']] ?? 2) * 0.40;
            $weightedConfidence += $llm['confidence'] * 0.40;
            $totalWeight += 0.40;
        }

        $normalizedScore = $totalWeight > 0 ? $weightedScore / $totalWeight : 2;
        $normalizedConfidence = $totalWeight > 0 ? $weightedConfidence / $totalWeight : 30;

        $roundedScore = max(1, min(4, round($normalizedScore)));
        $level = $reverseLevels[$roundedScore] ?? 'medium';

        // Force critical if any high-confidence source says critical
        if (($keyword['level'] === 'critical' && $keyword['confidence'] >= 60) ||
            ($llm['available'] && $llm['level'] === 'critical' && $llm['confidence'] >= 70)) {
            $level = 'critical';
        }

        return ['level' => $level, 'confidence' => (int) round($normalizedConfidence)];
    }

    /**
     * Build a human-readable reasoning string.
     */
    protected function buildReasoning(array $keyword, array $metadata, array $temporal, array $llm, string $finalLevel): string
    {
        $parts = [];
        if (!empty($keyword['matched'])) {
            $parts[] = 'Keywords: ' . implode(', ', array_slice($keyword['matched'], 0, 5));
        }
        if (!empty($metadata['signals'])) {
            $parts[] = 'Metadata: ' . implode('; ', $metadata['signals']);
        }
        if (!empty($temporal['signals'])) {
            $parts[] = 'Time-sensitive language detected';
        }
        if ($llm['available'] && $llm['reasoning']) {
            $parts[] = 'AI: ' . $llm['reasoning'];
        }
        if (empty($parts)) {
            $parts[] = 'Classified as ' . $finalLevel . ' based on general document profile';
        }
        return implode('. ', $parts) . '.';
    }

    /**
     * Calculate a due date based on urgency level (business days).
     */
    public function calculateDueDate(string $level): \Carbon\Carbon
    {
        $days = self::DUE_DATE_OFFSETS[$level] ?? 3;
        $date = now();
        $added = 0;
        while ($added < $days) {
            $date->addDay();
            if (!$date->isWeekend()) {
                $added++;
            }
        }
        if ($days === 0) {
            $date = now()->endOfDay();
        }
        return $date;
    }

    /**
     * Re-analyze a document (e.g., after content update).
     */
    public function reanalyze(Document $document): array
    {
        return $this->analyze($document);
    }

    /**
     * Get the response threshold config for a given urgency level.
     */
    public static function getThresholds(string $level): array
    {
        return self::RESPONSE_THRESHOLDS[$level] ?? self::RESPONSE_THRESHOLDS['medium'];
    }

    /**
     * Check if a workflow is overdue based on its document's urgency level.
     */
    public static function isWorkflowOverdue(DocumentWorkflow $workflow): bool
    {
        $document = $workflow->document;
        if (!$document || !$document->urgency_level) {
            return $workflow->isOverdue();
        }
        $thresholds = self::getThresholds($document->urgency_level);
        return $workflow->created_at->diffInHours(now()) >= $thresholds['escalation'];
    }

    /**
     * Check if a workflow needs a warning notification.
     */
    public static function needsWarning(DocumentWorkflow $workflow): bool
    {
        $document = $workflow->document;
        if (!$document || !$document->urgency_level) {
            return false;
        }
        $thresholds = self::getThresholds($document->urgency_level);
        $hours = $workflow->created_at->diffInHours(now());
        return $hours >= $thresholds['warning'] && $hours < $thresholds['escalation'];
    }
}
