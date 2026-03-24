<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Local LLM service using Ollama (qwen2.5:0.5b by default).
 *
 * Used for document summarization and content Q&A — runs entirely
 * on-premise with no external API calls.
 */
class OllamaService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ollama.base_url', 'http://localhost:11434'), '/');
        $this->model   = config('services.ollama.model', 'qwen2.5:0.5b');
        $this->timeout = (int) config('services.ollama.timeout', 120);

        $this->client = new Client([
            'timeout'         => $this->timeout,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * Check if Ollama is running and reachable.
     */
    public function isAvailable(): bool
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/tags", [
                'timeout'         => 2,
                'connect_timeout' => 1,
            ]);
            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Summarize document content.
     *
     * @param  string  $content     The document text to summarize
     * @param  string  $title       Document title for context
     * @param  int     $maxLength   Approximate max words for summary
     * @return array{summary: string, error: string|null}
     */
    public function summarize(string $content, string $title = '', int $maxLength = 150): array
    {
        // Trim content to avoid overwhelming the small model
        $content = Str::limit($content, 3000);

        $prompt = "Summarize the following document concisely in {$maxLength} words or less. "
                . "Focus on the main points, key details, and important information.\n\n";

        if ($title) {
            $prompt .= "Document title: {$title}\n\n";
        }

        $prompt .= "Document content:\n{$content}\n\nSummary:";

        return $this->generate($prompt, [
            'temperature'   => 0.3,
            'top_p'         => 0.85,
            'num_predict'   => 512,
        ]);
    }

    /**
     * Answer a question about document content.
     *
     * @param  string  $content   The document text
     * @param  string  $question  The user's question
     * @param  string  $title     Document title for context
     * @return array{summary: string, error: string|null}
     */
    public function answerQuestion(string $content, string $question, string $title = ''): array
    {
        $content = Str::limit($content, 3000);

        $prompt = "Based on the following document, answer the question concisely and accurately. "
                . "Only use information from the document. If the answer is not in the document, say so.\n\n";

        if ($title) {
            $prompt .= "Document title: {$title}\n\n";
        }

        $prompt .= "Document content:\n{$content}\n\nQuestion: {$question}\n\nAnswer:";

        return $this->generate($prompt, [
            'temperature'   => 0.3,
            'top_p'         => 0.9,
            'num_predict'   => 512,
        ]);
    }

    /**
     * General-purpose chat: takes a fully-assembled prompt (system + context + history + user message)
     * and returns the LLM response text.
     *
     * @param  string  $fullPrompt  The complete prompt string
     * @param  array   $options     Generation options
     * @return string  The response text (or an error message)
     */
    public function chat(string $fullPrompt, array $options = []): string
    {
        $result = $this->generate($fullPrompt, array_merge([
            'temperature' => 0.4,
            'top_p'       => 0.85,
            'num_predict' => 1024,
        ], $options));

        if (!empty($result['error'])) {
            Log::warning('Ollama chat error', ['error' => $result['error']]);
            throw new \RuntimeException($result['error']);
        }

        return $result['summary'];
    }

    /**
     * Generate a response from Ollama.
     *
     * @param  string  $prompt   The full prompt
     * @param  array   $options  Generation options (temperature, top_p, num_predict, etc.)
     * @return array{summary: string, error: string|null}
     */
    public function generate(string $prompt, array $options = []): array
    {
        try {
            $payload = [
                'model'   => $this->model,
                'prompt'  => $prompt,
                'stream'  => false,
                'options' => array_merge([
                    'temperature' => 0.3,
                    'top_p'       => 0.85,
                    'num_predict' => 512,
                ], $options),
            ];

            $response = $this->client->post("{$this->baseUrl}/api/generate", [
                'json'    => $payload,
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            $text = trim($body['response'] ?? '');

            if (empty($text)) {
                return [
                    'summary' => '',
                    'error'   => 'Ollama returned an empty response.',
                ];
            }

            return [
                'summary' => $text,
                'error'   => null,
            ];

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::warning('Ollama connection failed', ['error' => $e->getMessage()]);
            return [
                'summary' => '',
                'error'   => 'Could not connect to the local AI service (Ollama). Make sure it is running.',
            ];
        } catch (\Throwable $e) {
            Log::error('Ollama generate error', [
                'error'  => $e->getMessage(),
                'model'  => $this->model,
            ]);
            return [
                'summary' => '',
                'error'   => 'Local AI service error: ' . Str::limit($e->getMessage(), 120),
            ];
        }
    }

    /**
     * Get the configured model name.
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
