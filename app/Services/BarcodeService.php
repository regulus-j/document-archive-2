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
     * Convert percentage coordinates to mm based on actual page dimensions.
     *
     * @param float $xPercent X position as percentage (0-100)
     * @param float $yPercent Y position as percentage (0-100)
     * @param float $widthPercent Width as percentage (0-100)
     * @param float $heightPercent Height as percentage (0-100)
     * @param float $pageWidthMm Actual page width in mm
     * @param float $pageHeightMm Actual page height in mm
     * @return array ['x' => mm, 'y' => mm, 'width' => mm, 'height' => mm]
     */
    private function convertPercentToMm(
        float $xPercent,
        float $yPercent,
        float $widthPercent,
        float $heightPercent,
        float $pageWidthMm,
        float $pageHeightMm
    ): array {
        return [
            'x' => ($xPercent / 100.0) * $pageWidthMm,
            'y' => ($yPercent / 100.0) * $pageHeightMm,
            'width' => ($widthPercent / 100.0) * $pageWidthMm,
            'height' => ($heightPercent / 100.0) * $pageHeightMm,
        ];
    }

    /**
     * Convert percentage coordinates to pixels based on actual image dimensions.
     *
     * @param float $xPercent X position as percentage (0-100)
     * @param float $yPercent Y position as percentage (0-100)
     * @param float $widthPercent Width as percentage (0-100)
     * @param float $heightPercent Height as percentage (0-100)
     * @param int $imageWidth Actual image width in pixels
     * @param int $imageHeight Actual image height in pixels
     * @return array ['x' => px, 'y' => px, 'width' => px, 'height' => px]
     */
    private function convertPercentToPixels(
        float $xPercent,
        float $yPercent,
        float $widthPercent,
        float $heightPercent,
        int $imageWidth,
        int $imageHeight
    ): array {
        return [
            'x' => (int) round(($xPercent / 100.0) * $imageWidth),
            'y' => (int) round(($yPercent / 100.0) * $imageHeight),
            'width' => (int) round(($widthPercent / 100.0) * $imageWidth),
            'height' => (int) round(($heightPercent / 100.0) * $imageHeight),
        ];
    }

    /**
     * Overlay a barcode onto a PDF document.
     *
     * @param string $pdfPath     Absolute path to the source PDF
     * @param string $trackingNumber
     * @param array  $options     Overlay options:
     *                            - x_percent: X position as % (0-100, default: 5)
     *                            - y_percent: Y position as % (0-100, default: 3)
     *                            - width_percent: Width as % (0-100, default: 25)
     *                            - height_percent: Height as % (0-100, default: 5)
     *                            - x, y, width, height: Legacy mm-based (for backward compatibility)
     *                            - page: Page number to overlay on, 0 = all pages (default: 1)
     *                            - show_text: Whether to show tracking number text below barcode (default: true)
     * @return string Path to the new PDF with barcode overlay
     */
    public function overlayBarcodeOnPdf(string $pdfPath, string $trackingNumber, array $options = []): string
    {
        // Default percentage values
        $xPercent      = $options['x_percent'] ?? null;
        $yPercent      = $options['y_percent'] ?? null;
        $widthPercent  = $options['width_percent'] ?? null;
        $heightPercent = $options['height_percent'] ?? null;
        
        // Legacy mm values (for backward compatibility)
        $xMm      = $options['x'] ?? null;
        $yMm      = $options['y'] ?? null;
        $widthMm  = $options['width'] ?? null;
        $heightMm = $options['height'] ?? null;
        
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
                    // Get actual page dimensions in mm (FPDI returns points, convert to mm)
                    $pageWidthMm = $size['width'] * 25.4 / 72.0;  // points to mm
                    $pageHeightMm = $size['height'] * 25.4 / 72.0;

                    // Determine final coordinates (prefer percentage, fall back to mm)
                    if ($xPercent !== null && $yPercent !== null && $widthPercent !== null && $heightPercent !== null) {
                        // Use percentage-based positioning
                        $coords = $this->convertPercentToMm(
                            $xPercent,
                            $yPercent,
                            $widthPercent,
                            $heightPercent,
                            $pageWidthMm,
                            $pageHeightMm
                        );
                        $x = $coords['x'] * 72.0 / 25.4;  // mm back to points for FPDI
                        $y = $coords['y'] * 72.0 / 25.4;
                        $width = $coords['width'] * 72.0 / 25.4;
                        $height = $coords['height'] * 72.0 / 25.4;
                    } else {
                        // Use legacy mm-based positioning (backward compatibility)
                        // Convert mm to points for FPDI
                        $x = ($xMm ?? 10) * 72.0 / 25.4;
                        $y = ($yMm ?? 10) * 72.0 / 25.4;
                        $width = ($widthMm ?? 60) * 72.0 / 25.4;
                        $height = ($heightMm ?? 15) * 72.0 / 25.4;
                    }

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
     * Supports both percentage-based and legacy mm-based positioning.
     *
     * @param string $absolutePath  Absolute path to the image file
     * @param string $storagePath   Relative storage path (for logging only)
     * @param string $trackingNumber
     * @param array  $options       Overlay options:
     *                              - x_percent, y_percent, width_percent, height_percent (0-100)
     *                              - x, y, width, height (legacy mm-based for backward compatibility)
     *                              - show_text
     * @return bool  True on success, false on failure
     */
    public function overlayBarcodeOnImage(string $absolutePath, string $storagePath, string $trackingNumber, array $options = []): bool
    {
        if (!extension_loaded('gd')) {
            Log::error('GD extension not loaded â€" cannot overlay barcode on image.');
            return false;
        }

        // Percentage values (preferred)
        $xPercent      = $options['x_percent'] ?? null;
        $yPercent      = $options['y_percent'] ?? null;
        $widthPercent  = $options['width_percent'] ?? null;
        $heightPercent = $options['height_percent'] ?? null;
        
        // Legacy mm values (for backward compatibility)
        $xMm       = $options['x'] ?? null;
        $yMm       = $options['y'] ?? null;
        $widthMm   = $options['width'] ?? null;
        $heightMm  = $options['height'] ?? null;
        
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

            // Determine pixel coordinates (prefer percentage, fall back to mm)
            if ($xPercent !== null && $yPercent !== null && $widthPercent !== null && $heightPercent !== null) {
                // Use percentage-based positioning with actual image dimensions
                $coords = $this->convertPercentToPixels(
                    $xPercent,
                    $yPercent,
                    $widthPercent,
                    $heightPercent,
                    $imgW,
                    $imgH
                );
                $pxX = $coords['x'];
                $pxY = $coords['y'];
                $pxW = $coords['width'];
                $pxH = $coords['height'];
            } else {
                // Legacy: scale mm to pixels (assuming A4 = 210×297 mm for backward compatibility)
                $A4W = 210.0; $A4H = 297.0;
                $pxX = (int) round((($xMm ?? 10) / $A4W) * $imgW);
                $pxY = (int) round((($yMm ?? 10) / $A4H) * $imgH);
                $pxW = (int) round((($widthMm ?? 60) / $A4W) * $imgW);
                $pxH = (int) round((($heightMm ?? 15) / $A4H) * $imgH);
            }

            // Generate barcode PNG
            $barcodeRaw = $this->generateBarcodeRaw($trackingNumber, 2, max(50, $pxH));
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