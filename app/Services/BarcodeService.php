<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BarcodeService
{
    /**
     * Generate a barcode image (PNG) for a tracking number.
     *
     * @param string $trackingNumber
     * @param int    $widthFactor  Width of a single bar element in pixels (1-5)
     * @param int    $height       Height of barcode in pixels
     * @return string Base64-encoded PNG data URI
     */
    public function generateBarcodePng(string $trackingNumber, int $widthFactor = 2, int $height = 50): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode(
            $trackingNumber,
            $generator::TYPE_CODE_128,
            $widthFactor,
            $height
        );

        return 'data:image/png;base64,' . base64_encode($barcodeData);
    }

    /**
     * Generate raw barcode PNG bytes.
     *
     * @param string $trackingNumber
     * @param int    $widthFactor
     * @param int    $height
     * @return string Raw PNG bytes
     */
    public function generateBarcodeRaw(string $trackingNumber, int $widthFactor = 2, int $height = 50): string
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode(
            $trackingNumber,
            $generator::TYPE_CODE_128,
            $widthFactor,
            $height
        );
    }

    /**
     * Generate barcode as SVG string.
     *
     * @param string $trackingNumber
     * @param int    $widthFactor
     * @param int    $height
     * @return string SVG markup
     */
    public function generateBarcodeSvg(string $trackingNumber, int $widthFactor = 2, int $height = 50): string
    {
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode(
            $trackingNumber,
            $generator::TYPE_CODE_128,
            $widthFactor,
            $height
        );
    }

    /**
     * Overlay a barcode onto a PDF document.
     *
     * @param string $pdfPath     Absolute path to the source PDF
     * @param string $trackingNumber
     * @param array  $options     Overlay options:
     *                            - x: X position in mm from left (default: 10)
     *                            - y: Y position in mm from top (default: 10)
     *                            - width: Barcode width in mm (default: 60)
     *                            - height: Barcode height in mm (default: 15)
     *                            - page: Page number to overlay on, 0 = all pages (default: 1)
     *                            - show_text: Whether to show tracking number text below barcode (default: true)
     * @return string Path to the new PDF with barcode overlay
     */
    public function overlayBarcodeOnPdf(string $pdfPath, string $trackingNumber, array $options = []): string
    {
        $x          = $options['x'] ?? 10;
        $y          = $options['y'] ?? 10;
        $width      = $options['width'] ?? 60;
        $height     = $options['height'] ?? 15;
        $targetPage = $options['page'] ?? 1;
        $showText   = $options['show_text'] ?? true;

        try {
            // Generate barcode as a temporary PNG file
            $barcodeRaw = $this->generateBarcodeRaw($trackingNumber, 2, 100);
            $tempBarcodePath = storage_path('app/temp/barcode_' . md5($trackingNumber . time()) . '.png');

            // Ensure temp directory exists
            if (!is_dir(dirname($tempBarcodePath))) {
                mkdir(dirname($tempBarcodePath), 0755, true);
            }
            file_put_contents($tempBarcodePath, $barcodeRaw);

            // Create FPDI instance
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

                // Determine if we should overlay on this page
                $shouldOverlay = ($targetPage === 0 || $targetPage === $pageNo);

                if ($shouldOverlay) {
                    // Place barcode image
                    $pdf->Image($tempBarcodePath, $x, $y, $width, $height);

                    // Add tracking number text below barcode
                    if ($showText) {
                        $pdf->SetFont('Helvetica', '', 8);
                        $pdf->SetXY($x, $y + $height + 1);
                        $pdf->Cell($width, 4, $trackingNumber, 0, 0, 'C');
                    }
                }
            }

            // Save to a new file (same directory, prefixed)
            $outputPath = dirname($pdfPath) . '/bc_' . basename($pdfPath);
            $pdf->Output('F', $outputPath);

            // Clean up temp barcode
            if (file_exists($tempBarcodePath)) {
                unlink($tempBarcodePath);
            }

            return $outputPath;

        } catch (\Exception $e) {
            Log::error('Barcode overlay failed', [
                'pdf_path' => $pdfPath,
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            // Clean up temp file on error
            if (isset($tempBarcodePath) && file_exists($tempBarcodePath)) {
                unlink($tempBarcodePath);
            }

            throw $e;
        }
    }

    /**
     * Overlay barcode on a stored document (using Laravel storage).
     *
     * @param string $storagePath  Relative path within 'public' disk
     * @param string $trackingNumber
     * @param array  $options      Same as overlayBarcodeOnPdf options
     * @return string|null         New storage-relative path, or null if not a PDF
     */
    public function overlayBarcodeOnStoredDocument(string $storagePath, string $trackingNumber, array $options = []): ?string
    {
        $absolutePath = storage_path('app/public/' . $storagePath);

        // Only overlay on PDF files
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            Log::info('Barcode overlay skipped: not a PDF file', ['path' => $storagePath]);
            return null;
        }

        if (!file_exists($absolutePath)) {
            Log::warning('Barcode overlay skipped: file not found', ['path' => $storagePath]);
            return null;
        }

        $outputAbsPath = $this->overlayBarcodeOnPdf($absolutePath, $trackingNumber, $options);

        // Replace the original file with the barcode-overlaid version
        if (file_exists($outputAbsPath)) {
            // Back up original (move to versions directory if needed)
            copy($outputAbsPath, $absolutePath);
            unlink($outputAbsPath);

            return $storagePath; // Same path, file contents updated
        }

        return null;
    }
}
