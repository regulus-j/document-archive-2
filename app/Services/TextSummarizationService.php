<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Extractive text summarization using TextRank (graph-based sentence ranking).
 *
 * Pure PHP implementation — no external APIs, models, or GPU required.
 * Works by: splitting text into sentences → computing sentence similarity via
 * shared terms (TF overlap) → building a graph → ranking via iterative scoring
 * (similar to PageRank) → selecting top-ranked sentences in document order.
 */
class TextSummarizationService
{
    /** Default number of sentences to extract */
    const DEFAULT_SENTENCE_COUNT = 5;

    /** Damping factor for TextRank iteration (same as PageRank) */
    const DAMPING = 0.85;

    /** Number of ranking iterations */
    const ITERATIONS = 30;

    /** Common English stop words to ignore during similarity computation */
    const STOP_WORDS = [
        'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
        'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were', 'be', 'been',
        'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would',
        'could', 'should', 'may', 'might', 'shall', 'can', 'need', 'dare',
        'it', 'its', 'this', 'that', 'these', 'those', 'i', 'me', 'my', 'we',
        'our', 'you', 'your', 'he', 'him', 'his', 'she', 'her', 'they', 'them',
        'their', 'what', 'which', 'who', 'whom', 'where', 'when', 'how', 'why',
        'not', 'no', 'nor', 'as', 'if', 'then', 'than', 'too', 'very', 'just',
        'about', 'above', 'after', 'again', 'all', 'also', 'am', 'any', 'because',
        'before', 'between', 'both', 'each', 'few', 'more', 'most', 'other',
        'some', 'such', 'into', 'over', 'own', 'same', 'so', 'only', 'out',
        'up', 'down', 'here', 'there', 'once', 'during', 'while', 'through',
    ];

    /**
     * Summarize text using extractive TextRank.
     *
     * @param  string  $text           The input text to summarize
     * @param  int     $sentenceCount  Number of sentences to extract
     * @return string                  The summary (top sentences in original order)
     */
    public function summarize(string $text, int $sentenceCount = self::DEFAULT_SENTENCE_COUNT): string
    {
        $text = $this->cleanInput($text);

        if (empty($text)) {
            return '';
        }

        // Split into sentences
        $sentences = $this->splitSentences($text);

        // If text is already short, return as-is
        if (count($sentences) <= $sentenceCount) {
            return implode(' ', $sentences);
        }

        // Tokenize each sentence into meaningful words
        $tokenized = array_map(fn($s) => $this->tokenize($s), $sentences);

        // Build similarity matrix
        $n = count($sentences);
        $similarity = $this->buildSimilarityMatrix($tokenized, $n);

        // Run TextRank iteration
        $scores = $this->rankSentences($similarity, $n);

        // Select top sentences, preserving original order
        $ranked = $scores;
        arsort($ranked);
        $topIndices = array_slice(array_keys($ranked), 0, $sentenceCount);
        sort($topIndices); // restore document order

        $summary = [];
        foreach ($topIndices as $idx) {
            $summary[] = $sentences[$idx];
        }

        return implode(' ', $summary);
    }

    /**
     * Generate a structured summary with metadata for chatbot display.
     *
     * @param  string  $text           Document content
     * @param  string  $title          Document title
     * @param  int     $sentenceCount  Number of sentences
     * @return array{summary: string, sentence_count: int, original_length: int}
     */
    public function summarizeForChatbot(string $text, string $title = '', int $sentenceCount = self::DEFAULT_SENTENCE_COUNT): array
    {
        $originalLength = mb_strlen($text);
        $originalSentences = count($this->splitSentences($this->cleanInput($text)));

        $summary = $this->summarize($text, $sentenceCount);
        $summaryLength = mb_strlen($summary);

        return [
            'summary'            => $summary,
            'sentence_count'     => min($sentenceCount, $originalSentences),
            'original_sentences' => $originalSentences,
            'original_length'    => $originalLength,
            'summary_length'     => $summaryLength,
            'compression_ratio'  => $originalLength > 0
                ? round((1 - $summaryLength / $originalLength) * 100)
                : 0,
        ];
    }

    /**
     * Extract key phrases / terms from text (top N by frequency).
     *
     * @param  string  $text
     * @param  int     $count
     * @return array<string>
     */
    public function extractKeyTerms(string $text, int $count = 8): array
    {
        $words = $this->tokenize($text);

        if (empty($words)) {
            return [];
        }

        // Count frequencies
        $freq = array_count_values($words);

        // Filter out very short or very common terms
        $freq = array_filter($freq, function ($count, $word) {
            return mb_strlen($word) >= 3 && $count >= 1;
        }, ARRAY_FILTER_USE_BOTH);

        arsort($freq);

        return array_slice(array_keys($freq), 0, $count);
    }

    /* ----------------------------------------------------------------
     *  INTERNAL METHODS
     * ---------------------------------------------------------------- */

    /**
     * Clean input text: normalize whitespace, remove non-printable chars.
     */
    private function cleanInput(string $text): string
    {
        // Remove null bytes and non-printable characters (keep newlines, tabs)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Normalize line endings and collapse excessive whitespace
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = preg_replace('/ {2,}/', ' ', $text);

        return trim($text);
    }

    /**
     * Split text into sentences using punctuation-based heuristics.
     */
    private function splitSentences(string $text): array
    {
        if (empty($text)) {
            return [];
        }

        // Replace newlines with spaces for sentence splitting, but keep paragraph breaks as boundaries
        $text = preg_replace("/\n{2,}/", ".\n", $text);
        $text = str_replace("\n", ' ', $text);

        // Split on sentence-ending punctuation followed by space or end-of-string
        // Handles: periods, exclamation marks, question marks, semicolons
        $raw = preg_split(
            '/(?<=[.!?;])\s+(?=[A-Z0-9])|(?<=[.!?])\s*$/',
            $text,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        // If regex splitting produced only 1 result, try splitting on newlines/periods more aggressively
        if (count($raw) <= 1 && mb_strlen($text) > 200) {
            $raw = preg_split('/[.!?;]+\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
        }

        // Clean and filter sentences
        $sentences = [];
        foreach ($raw as $s) {
            $s = trim($s);
            // Skip very short fragments (< 20 chars) or empty ones
            if (mb_strlen($s) >= 20) {
                $sentences[] = $s;
            }
        }

        return $sentences;
    }

    /**
     * Tokenize a sentence/text into meaningful lowercase words, removing stop words.
     *
     * @return array<string>
     */
    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text);

        // Extract words (including accented characters)
        preg_match_all('/\b[a-zA-Z\x{00C0}-\x{024F}]{2,}\b/u', $text, $matches);

        $words = $matches[0] ?? [];

        // Remove stop words
        $stopWords = array_flip(self::STOP_WORDS);
        $words = array_filter($words, fn($w) => !isset($stopWords[$w]));

        return array_values($words);
    }

    /**
     * Build a cosine-similarity-like matrix between sentences
     * based on shared term overlap (normalized by sentence lengths).
     *
     * @return array<array<float>>
     */
    private function buildSimilarityMatrix(array $tokenized, int $n): array
    {
        $matrix = array_fill(0, $n, array_fill(0, $n, 0.0));

        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $sim = $this->sentenceSimilarity($tokenized[$i], $tokenized[$j]);
                $matrix[$i][$j] = $sim;
                $matrix[$j][$i] = $sim;
            }
        }

        return $matrix;
    }

    /**
     * Compute similarity between two tokenized sentences
     * using term overlap normalized by log of sentence lengths.
     */
    private function sentenceSimilarity(array $wordsA, array $wordsB): float
    {
        if (empty($wordsA) || empty($wordsB)) {
            return 0.0;
        }

        $setA = array_flip($wordsA);
        $overlap = 0;

        foreach ($wordsB as $word) {
            if (isset($setA[$word])) {
                $overlap++;
            }
        }

        if ($overlap === 0) {
            return 0.0;
        }

        // Normalize by log of lengths to reduce bias toward long sentences
        $lenA = max(1, log(count($wordsA) + 1));
        $lenB = max(1, log(count($wordsB) + 1));

        return $overlap / ($lenA + $lenB);
    }

    /**
     * Rank sentences using iterative graph-based scoring (TextRank / PageRank).
     *
     * @return array<int, float>  Index → score
     */
    private function rankSentences(array $similarity, int $n): array
    {
        // Initialize all scores equally
        $scores = array_fill(0, $n, 1.0 / $n);

        // Pre-compute row sums for normalization
        $rowSums = [];
        for ($i = 0; $i < $n; $i++) {
            $rowSums[$i] = max(array_sum($similarity[$i]), 0.0001);
        }

        // Iterate
        for ($iter = 0; $iter < self::ITERATIONS; $iter++) {
            $newScores = [];

            for ($i = 0; $i < $n; $i++) {
                $sum = 0.0;
                for ($j = 0; $j < $n; $j++) {
                    if ($i !== $j && $similarity[$j][$i] > 0) {
                        $sum += ($similarity[$j][$i] / $rowSums[$j]) * $scores[$j];
                    }
                }
                $newScores[$i] = (1 - self::DAMPING) / $n + self::DAMPING * $sum;
            }

            $scores = $newScores;
        }

        return $scores;
    }
}
