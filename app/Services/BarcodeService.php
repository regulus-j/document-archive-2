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
    public function generateBarcodePng(string $trackingNumber, int $widthFactor = 3, int $height = 80): string
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
    public function generateBarcodeRaw(string $trackingNumber, int $widthFactor = 3, int $height = 80): string
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
    public function generateBarcodeSvg(string $trackingNumber, int $widthFactor = 3, int $height = 80): string
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
        $x          = (float) ($options['x']         ?? 10);
        $y          = (float) ($options['y']         ?? 10);
        $width      = (float) ($options['width']     ?? 60);
        $height     = (float) ($options['height']    ?? 15);
        $targetPage = (int)   ($options['page']      ?? 1);
        $showText   = (bool)  ($options['show_text'] ?? true);

        try {
            // Generate a high-resolution barcode PNG
            $barcodeRaw      = $this->generateBarcodeRaw($trackingNumber, 3, 200);
            $tempBarcodePath = storage_path('app/temp/barcode_' . md5($trackingNumber . time()) . '.png');

            if (!is_dir(dirname($tempBarcodePath))) {
                mkdir(dirname($tempBarcodePath), 0755, true);
            }
            file_put_contents($tempBarcodePath, $barcodeRaw);

            // FPDI always returns getTemplateSize() in mm regardless of constructor unit.
            // Use 'mm' so that our x/y/width/height coordinates are also in mm.
            $pdf = new Fpdi('P', 'mm');
            $pdf->SetAutoPageBreak(false);
            $pdf->SetMargins(0, 0, 0);
            $pageCount = $pdf->setSourceFile($pdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);

                // getTemplateSize() returns mm dimensions directly
                $size = $pdf->getTemplateSize($templateId);
                $pageW = $size['width'];
                $pageH = $size['height'];
                $orientation = ($pageW > $pageH) ? 'L' : 'P';

                $pdf->AddPage($orientation, [$pageW, $pageH]);

                // Stamp the original page content as a full-page background
                $pdf->useTemplate($templateId, 0, 0, $pageW, $pageH);

                $shouldOverlay = ($targetPage === 0 || $targetPage === $pageNo);

                if ($shouldOverlay) {
                    // White background rectangle behind barcode for readability
                    $pdf->SetFillColor(255, 255, 255);
                    $textH = $showText ? 5 : 0;
                    $pdf->Rect($x - 1, $y - 1, $width + 2, $height + $textH + 2, 'F');

                    // Place barcode image
                    $pdf->Image($tempBarcodePath, $x, $y, $width, $height, 'PNG');

                    // Optionally add tracking number text below barcode
                    if ($showText) {
                        $pdf->SetFont('Helvetica', '', 7);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetXY($x, $y + $height + 1);
                        $pdf->Cell($width, 4, $trackingNumber, 0, 0, 'C');
                    }
                }
            }

            // Write to a temp output file then swap with the original
            $outputPath = dirname($pdfPath) . '/bc_' . basename($pdfPath);
            $pdf->Output('F', $outputPath);

            if (file_exists($tempBarcodePath)) {
                unlink($tempBarcodePath);
            }

            return $outputPath;

        } catch (\Exception $e) {
            Log::error('Barcode overlay failed', [
                'pdf_path'        => $pdfPath,
                'tracking_number' => $trackingNumber,
                'error'           => $e->getMessage(),
                'trace'           => $e->getTraceAsString(),
            ]);

            if (isset($tempBarcodePath) && file_exists($tempBarcodePath)) {
                unlink($tempBarcodePath);
            }

            throw $e;
        }
    }


    /**
     * Overlay barcode on a stored document (using Laravel storage).
     * Supports PDF (via FPDI) and images (via GD).
     * DOCX and XLSX are NOT supported for overlay.
     *
     * @param string $storagePath  Relative path within 'public' disk
     * @param string $trackingNumber
     * @param array  $options      x, y, width, height (all in mm), page, show_text
     * @return string|null         Storage-relative path, or null if unsupported/failed
     */
    public function overlayBarcodeOnStoredDocument(string $storagePath, string $trackingNumber, array $options = []): ?string
    {
        $absolutePath = storage_path('app/public/' . $storagePath);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if (!file_exists($absolutePath)) {
            Log::warning('Barcode overlay skipped: file not found', ['path' => $storagePath]);
            return null;
        }

        if ($extension === 'pdf') {
            $outputAbsPath = $this->overlayBarcodeOnPdf($absolutePath, $trackingNumber, $options);
            if (file_exists($outputAbsPath)) {
                copy($outputAbsPath, $absolutePath);
                unlink($outputAbsPath);
                return $storagePath;
            }
            return null;
        }

        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        if (in_array($extension, $imageExts)) {
            return $this->overlayBarcodeOnImage($absolutePath, $storagePath, $trackingNumber, $options)
                ? $storagePath
                : null;
        }

        Log::info('Barcode overlay skipped: unsupported format (only PDF and images are supported)', ['path' => $storagePath, 'ext' => $extension]);
        return null;
    }

    /**
     * Overlay a barcode onto an image file using the GD library.
     * The barcode is placed at the specified X/Y position (in mm on an A4 page,
     * scaled proportionally to the image dimensions).
     *
     * @param string $absolutePath  Absolute path to the image file
     * @param string $storagePath   Relative storage path (for logging only)
     * @param string $trackingNumber
     * @param array  $options       Overlay options (x, y, width, height in mm, show_text)
     * @return bool  True on success, false on failure
     */
    public function overlayBarcodeOnImage(string $absolutePath, string $storagePath, string $trackingNumber, array $options = []): bool
    {
        if (!extension_loaded('gd')) {
            Log::error('GD extension not loaded â€” cannot overlay barcode on image.');
            return false;
        }

        $x         = $options['x']         ?? 10;
        $y         = $options['y']         ?? 10;
        $widthMm   = $options['width']     ?? 60;
        $heightMm  = $options['height']    ?? 15;
        $showText  = $options['show_text'] ?? true;

        try {
            $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

            // Load the source image
            $src = match ($extension) {
                'jpg', 'jpeg' => imagecreatefromjpeg($absolutePath),
                'png'         => imagecreatefrompng($absolutePath),
                'gif'         => imagecreatefromgif($absolutePath),
                'webp'        => imagecreatefromwebp($absolutePath),
                'bmp'         => imagecreatefrombmp($absolutePath),
                default       => null,
            };

            if (!$src) {
                Log::error('Barcode overlay: could not load image', ['path' => $absolutePath]);
                return false;
            }

            $imgW = imagesx($src);
            $imgH = imagesy($src);

            // Scale mm â†’ pixels (assuming A4 = 210Ã—297 mm)
            $A4W = 210.0; $A4H = 297.0;
            $pxX = (int) round(($x       / $A4W) * $imgW);
            $pxY = (int) round(($y       / $A4H) * $imgH);
            $pxW = (int) round(($widthMm / $A4W) * $imgW);
            $pxH = (int) round(($heightMm/ $A4H) * $imgH);

            // Generate barcode PNG (high-resolution for scanner readability)
            $barcodeRaw = $this->generateBarcodeRaw($trackingNumber, 3, max(100, $pxH * 2));
            $barcode    = imagecreatefromstring($barcodeRaw);

            if (!$barcode) {
                imagedestroy($src);
                Log::error('Barcode overlay: could not create barcode image resource.');
                return false;
            }

            // Scale barcode to target pixel dimensions
            $barcodeScaled = imagescale($barcode, $pxW, $pxH, IMG_BICUBIC);
            imagedestroy($barcode);

            if (!$barcodeScaled) {
                imagedestroy($src);
                Log::error('Barcode overlay: could not scale barcode.');
                return false;
            }

            // Composite barcode onto image (white background box first)
            $white = imagecolorallocate($src, 255, 255, 255);
            $textH = $showText ? 14 : 0;
            imagefilledrectangle($src, $pxX, $pxY, $pxX + $pxW, $pxY + $pxH + $textH, $white);
            imagecopy($src, $barcodeScaled, $pxX, $pxY, 0, 0, $pxW, $pxH);
            imagedestroy($barcodeScaled);

            // Add tracking number text below barcode
            if ($showText) {
                $black = imagecolorallocate($src, 0, 0, 0);
                $font  = 2; // built-in GD font
                $textY = $pxY + $pxH + 2;
                imagestring($src, $font, $pxX, $textY, $trackingNumber, $black);
            }

            // Save to a temp path first — never overwrite the original until success
            $tempPath = $absolutePath . '.bc_tmp';
            $saved = match ($extension) {
                'jpg', 'jpeg' => imagejpeg($src, $tempPath, 90),
                'png'         => imagepng($src, $tempPath, 6),
                'gif'         => imagegif($src, $tempPath),
                'webp'        => imagewebp($src, $tempPath, 85),
                'bmp'         => imagebmp($src, $tempPath),
                default       => false,
            };

            imagedestroy($src);

            if (!$saved) {
                if (file_exists($tempPath)) unlink($tempPath);
                Log::error('Barcode overlay: failed to save image to temp', ['path' => $absolutePath]);
                return false;
            }

            // Copy temp over original (original untouched until this point)
            if (!copy($tempPath, $absolutePath)) {
                unlink($tempPath);
                Log::error('Barcode overlay: failed to copy temp to original', ['path' => $absolutePath]);
                return false;
            }
            unlink($tempPath);

            Log::info('Barcode overlay applied to image', ['path' => $storagePath]);
            return true;

        } catch (\Exception $e) {
            Log::error('Barcode image overlay failed', ['path' => $absolutePath, 'error' => $e->getMessage()]);
            return false;
        }
    }

}