<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentContentExtractorService
{
    /**
     * Maximum characters to extract from a file for chatbot context.
     */
    const MAX_EXTRACT_CHARS = 4000;

    /**
     * Supported MIME-type groups for text extraction.
     */
    const SUPPORTED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'txt', 'rtf', 'csv', 'odt',
        'xlsx', 'xls', 'pptx', 'ppt',
        'jpg', 'jpeg', 'png', 'tiff', 'tif', 'bmp', 'webp',
    ];

    /**
     * Extract text content from a document. Tries the DB `content` column first,
     * then falls back to reading the actual file from storage.
     *
     * @param  Document  $document
     * @param  bool      $forceFile  Skip the DB column and read the file directly
     * @return array{content: string, source: string, error: string|null}
     */
    public function extract(Document $document, bool $forceFile = false): array
    {
        // 1. Try the DB content column first (unless forced to read file)
        if (!$forceFile && !empty($document->content)) {
            return [
                'content' => Str::limit($document->content, self::MAX_EXTRACT_CHARS),
                'source'  => 'database',
                'error'   => null,
            ];
        }

        // 2. Attempt to read the actual file from storage
        if (empty($document->path)) {
            return [
                'content' => '',
                'source'  => 'none',
                'error'   => 'No file path recorded for this document.',
            ];
        }

        $extension = strtolower(pathinfo($document->path, PATHINFO_EXTENSION));

        if (!in_array($extension, self::SUPPORTED_EXTENSIONS)) {
            return [
                'content' => '',
                'source'  => 'unsupported',
                'error'   => "File type \".{$extension}\" cannot be read as text. The user should download or preview this file directly.",
            ];
        }

        // Resolve full path on disk (files stored on the public disk)
        $fullPath = Storage::disk('public')->path($document->path);

        if (!file_exists($fullPath)) {
            return [
                'content' => '',
                'source'  => 'missing',
                'error'   => 'The file could not be found on disk. It may have been moved or deleted.',
            ];
        }

        try {
            $text = match ($extension) {
                'pdf'        => $this->extractPdf($fullPath),
                'doc', 'docx', 'odt' => $this->extractWord($fullPath),
                'txt', 'csv' => $this->extractPlainText($fullPath),
                'rtf'        => $this->extractRtf($fullPath),
                'xlsx', 'xls' => $this->extractSpreadsheet($fullPath),
                'pptx', 'ppt' => $this->extractPresentation($fullPath),
                'jpg', 'jpeg', 'png', 'tiff', 'tif', 'bmp', 'webp' => $this->extractImageOcr($fullPath),
                default      => '',
            };

            if (empty(trim($text))) {
                return [
                    'content' => '',
                    'source'  => 'empty',
                    'error'   => 'The file exists but no readable text could be extracted. It may be a scanned image or secured PDF.',
                ];
            }

            $text = $this->cleanText($text);
            $text = Str::limit($text, self::MAX_EXTRACT_CHARS);

            // Cache the extracted content back to the DB for future requests
            $this->cacheContent($document, $text);

            return [
                'content' => $text,
                'source'  => 'file',
                'error'   => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('DocumentContentExtractor: extraction failed', [
                'document_id' => $document->id,
                'path'        => $document->path,
                'error'       => $e->getMessage(),
            ]);

            return [
                'content' => '',
                'source'  => 'error',
                'error'   => 'Failed to extract text from the file: ' . Str::limit($e->getMessage(), 120),
            ];
        }
    }

    /**
     * Extract text content from a document's attachments.
     *
     * @param  Document  $document
     * @param  int       $maxAttachments
     * @return array  Array of ['filename' => ..., 'content' => ..., 'error' => ...]
     */
    public function extractAttachments(Document $document, int $maxAttachments = 3): array
    {
        $results = [];

        $attachments = $document->attachments()->limit($maxAttachments)->get();

        foreach ($attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));

            if (!in_array($extension, self::SUPPORTED_EXTENSIONS)) {
                $results[] = [
                    'filename' => $attachment->filename,
                    'content'  => '',
                    'error'    => "Attachment type \".{$extension}\" cannot be read as text.",
                ];
                continue;
            }

            $fullPath = Storage::disk('public')->path($attachment->path);

            if (!file_exists($fullPath)) {
                $results[] = [
                    'filename' => $attachment->filename,
                    'content'  => '',
                    'error'    => 'Attachment file not found on disk.',
                ];
                continue;
            }

            try {
                $text = match ($extension) {
                    'pdf'        => $this->extractPdf($fullPath),
                    'doc', 'docx', 'odt' => $this->extractWord($fullPath),
                    'txt', 'csv' => $this->extractPlainText($fullPath),
                    'rtf'        => $this->extractRtf($fullPath),
                    'xlsx', 'xls' => $this->extractSpreadsheet($fullPath),
                    'pptx', 'ppt' => $this->extractPresentation($fullPath),
                    'jpg', 'jpeg', 'png', 'tiff', 'tif', 'bmp', 'webp' => $this->extractImageOcr($fullPath),
                    default      => '',
                };

                $text = $this->cleanText($text);
                $text = Str::limit($text, 1500); // Smaller limit per attachment

                $results[] = [
                    'filename' => $attachment->filename,
                    'content'  => $text,
                    'error'    => empty(trim($text)) ? 'No readable text extracted from attachment.' : null,
                ];
            } catch (\Throwable $e) {
                Log::warning('DocumentContentExtractor: attachment extraction failed', [
                    'attachment_id' => $attachment->id,
                    'path'          => $attachment->path,
                    'error'         => $e->getMessage(),
                ]);

                $results[] = [
                    'filename' => $attachment->filename,
                    'content'  => '',
                    'error'    => 'Failed to extract text from attachment.',
                ];
            }
        }

        return $results;
    }

    /* ----------------------------------------------------------------
     *  EXTRACTORS
     * ---------------------------------------------------------------- */

    private function extractPdf(string $fullPath): string
    {
        // Determine poppler path
        $popplerBin = env('POPPLER_PATH');

        if (empty($popplerBin)) {
            // Try the Dependencies folder shipped with the project
            $defaultPath = base_path('Dependencies/poppler-24.08.0/Library/bin/pdftotext.exe');
            if (file_exists($defaultPath)) {
                $popplerBin = $defaultPath;
            }
        }

        if (!empty($popplerBin) && class_exists(\Spatie\PdfToText\Pdf::class)) {
            $text = (new \Spatie\PdfToText\Pdf($popplerBin))
                ->setPdf($fullPath)
                ->text();

            if (!empty(trim($text))) {
                return $text;
            }
        }

        // Fallback: shell out to pdftotext directly
        if (!empty($popplerBin)) {
            $outputFile = storage_path('app/temp/chatbot_pdf_' . Str::random(8) . '.txt');

            // Ensure temp directory exists
            $tempDir = dirname($outputFile);
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $command = escapeshellarg($popplerBin) . ' '
                     . escapeshellarg($fullPath) . ' '
                     . escapeshellarg($outputFile);

            shell_exec($command);

            if (file_exists($outputFile)) {
                $text = file_get_contents($outputFile);
                @unlink($outputFile);
                return $text ?: '';
            }
        }

        return '';
    }

    private function extractWord(string $fullPath): string
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            throw new \RuntimeException('PhpWord is not installed. Cannot read DOCX files.');
        }

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath);
        $content = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text = $element->getText();
                    if (is_string($text)) {
                        $content .= $text . "\n";
                    }
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    foreach ($element->getRows() as $row) {
                        $cells = [];
                        foreach ($row->getCells() as $cell) {
                            $cellText = '';
                            foreach ($cell->getElements() as $cellElement) {
                                if (method_exists($cellElement, 'getText')) {
                                    $t = $cellElement->getText();
                                    if (is_string($t)) {
                                        $cellText .= $t;
                                    }
                                }
                            }
                            $cells[] = $cellText;
                        }
                        $content .= implode(' | ', $cells) . "\n";
                    }
                }
            }
        }

        return $content;
    }

    private function extractPlainText(string $fullPath): string
    {
        $maxBytes = 500000; // ~500 KB max read
        $content = file_get_contents($fullPath, false, null, 0, $maxBytes);
        return $content ?: '';
    }

    private function extractRtf(string $fullPath): string
    {
        // Basic RTF stripping — remove RTF control words and extract plain text
        $raw = file_get_contents($fullPath, false, null, 0, 500000);
        if (empty($raw)) return '';

        // Strip RTF formatting
        $text = preg_replace('/\{\\\\[^{}]*\}/', '', $raw);
        $text = preg_replace('/\\\\[a-z]+\d*\s?/i', '', $text);
        $text = preg_replace('/[{}]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Extract text from Excel spreadsheets (xlsx, xls).
     * Returns sheet names, column headers, and data rows.
     */
    private function extractSpreadsheet(string $fullPath): string
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new \RuntimeException('PhpSpreadsheet is not installed. Cannot read Excel files.');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
        $content = '';

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheetName = $sheet->getTitle();
            $content .= "=== Sheet: {$sheetName} ===\n";

            $highestRow = min($sheet->getHighestDataRow(), 100); // Cap at 100 rows
            $highestCol = $sheet->getHighestDataColumn();
            $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

            if ($highestRow < 1) {
                $content .= "(empty sheet)\n\n";
                continue;
            }

            // Extract header row (row 1) — treat as column names
            $headers = [];
            for ($col = 1; $col <= $highestColIndex; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $val = $sheet->getCell("{$colLetter}1")->getFormattedValue();
                $headers[] = trim((string) $val) ?: "(Col {$colLetter})";
            }
            $content .= "Columns: " . implode(' | ', $headers) . "\n";

            // Extract data rows (row 2 onward)
            $rowCount = 0;
            for ($row = 2; $row <= $highestRow; $row++) {
                $cells = [];
                $allEmpty = true;
                for ($col = 1; $col <= $highestColIndex; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $val = trim((string) $sheet->getCell("{$colLetter}{$row}")->getFormattedValue());
                    if ($val !== '') $allEmpty = false;
                    $cells[] = $val;
                }
                if ($allEmpty) continue; // Skip empty rows
                $content .= "Row {$row}: " . implode(' | ', $cells) . "\n";
                $rowCount++;
            }

            $content .= "({$rowCount} data rows)\n\n";
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $content;
    }

    /**
     * Extract text from PowerPoint presentations (pptx, ppt).
     * Uses basic ZIP-based XML parsing since PhpPresentation is not installed.
     */
    private function extractPresentation(string $fullPath): string
    {
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Only PPTX (Open XML) can be read without PhpPresentation
        if ($extension !== 'pptx') {
            throw new \RuntimeException('Only .pptx files are supported. For .ppt files, please download and view directly.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($fullPath) !== true) {
            throw new \RuntimeException('Could not open the presentation file.');
        }

        $content = '';
        $slideNum = 1;

        // PPTX stores slides as slide1.xml, slide2.xml, etc. in ppt/slides/
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('/^ppt\/slides\/slide(\d+)\.xml$/i', $name, $m)) {
                $xml = $zip->getFromIndex($i);
                if ($xml === false) continue;

                // Strip XML tags to get raw text content
                $text = strip_tags($xml);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);

                if (!empty($text)) {
                    $content .= "--- Slide {$m[1]} ---\n{$text}\n\n";
                }
                $slideNum++;
            }
        }

        $zip->close();

        if (empty(trim($content))) {
            return '';
        }

        return "Presentation with " . ($slideNum - 1) . " slide(s):\n\n" . $content;
    }

    /**
     * Extract text from images using Tesseract OCR (if available).
     */
    private function extractImageOcr(string $fullPath): string
    {
        // Check if Tesseract is available
        $tesseractPath = $this->findTesseract();

        if (!$tesseractPath) {
            throw new \RuntimeException('Image text extraction requires Tesseract OCR which is not available. Please download the file to view it.');
        }

        $outputBase = storage_path('app/temp/chatbot_ocr_' . Str::random(8));
        $outputFile = $outputBase . '.txt';

        $tempDir = dirname($outputBase);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $command = escapeshellarg($tesseractPath) . ' '
                 . escapeshellarg($fullPath) . ' '
                 . escapeshellarg($outputBase)
                 . ' 2>&1';

        shell_exec($command);

        if (file_exists($outputFile)) {
            $text = file_get_contents($outputFile);
            @unlink($outputFile);
            return trim($text ?: '');
        }

        return '';
    }

    /**
     * Find Tesseract OCR binary path.
     */
    private function findTesseract(): ?string
    {
        // Check env variable
        $path = env('TESSERACT_PATH');
        if ($path && (file_exists($path) || $this->isCommandAvailable($path))) {
            return $path;
        }

        // Common locations
        $candidates = [
            'tesseract',            // In PATH (Linux/Docker)
            '/usr/bin/tesseract',   // Linux
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe', // Windows
            'C:\\Users\\' . (env('USERNAME', 'Admin')) . '\\AppData\\Local\\Programs\\Tesseract-OCR\\tesseract.exe',
        ];

        foreach ($candidates as $candidate) {
            // For absolute paths, check file existence directly
            if (preg_match('#^[/\\\\]|^[A-Z]:\\\\#i', $candidate) && file_exists($candidate)) {
                return $candidate;
            }
            // For command names, check if they're in PATH
            if ($this->isCommandAvailable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Check if a command is available on the system.
     */
    private function isCommandAvailable(string $command): bool
    {
        $check = PHP_OS_FAMILY === 'Windows'
            ? "where " . escapeshellarg($command) . " 2>NUL"
            : "which " . escapeshellarg($command) . " 2>/dev/null";

        $result = shell_exec($check);
        return !empty(trim($result ?? ''));
    }

    /* ----------------------------------------------------------------
     *  HELPERS
     * ---------------------------------------------------------------- */

    /**
     * Clean extracted text: normalize whitespace, remove null bytes, etc.
     */
    private function cleanText(string $text): string
    {
        // Remove null bytes
        $text = str_replace("\0", '', $text);

        // Normalize line endings
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);

        // Collapse excessive blank lines (3+ → 2)
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        // Remove excessive spaces (but preserve indentation)
        $text = preg_replace('/ {4,}/', '   ', $text);

        return trim($text);
    }

    /**
     * Cache extracted content to the document's content column.
     */
    private function cacheContent(Document $document, string $text): void
    {
        try {
            if (empty($document->content) && !empty($text)) {
                $document->update(['content' => $text]);
                Log::info('DocumentContentExtractor: cached content for document', [
                    'document_id' => $document->id,
                    'chars'       => strlen($text),
                ]);
            }
        } catch (\Throwable $e) {
            // Non-critical — just log
            Log::warning('DocumentContentExtractor: failed to cache content', [
                'document_id' => $document->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
