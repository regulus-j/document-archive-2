<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
     * Supports PDF (via FPDI), images (via GD), DOCX (via PhpWord), XLSX/ODS (via PhpSpreadsheet).
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

        $wordExts = ['doc', 'docx'];
        if (in_array($extension, $wordExts)) {
            return $this->overlayBarcodeOnDocx($absolutePath, $storagePath, $trackingNumber, $options)
                ? $storagePath
                : null;
        }

        $spreadsheetExts = ['xls', 'xlsx', 'ods', 'csv'];
        if (in_array($extension, $spreadsheetExts)) {
            return $this->overlayBarcodeOnExcel($absolutePath, $storagePath, $trackingNumber, $options)
                ? $storagePath
                : null;
        }

        Log::info('Barcode overlay skipped: unsupported format', ['path' => $storagePath, 'ext' => $extension]);
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

            // Save back to original file
            $saved = match ($extension) {
                'jpg', 'jpeg' => imagejpeg($src, $absolutePath, 90),
                'png'         => imagepng($src, $absolutePath, 6),
                'gif'         => imagegif($src, $absolutePath),
                'webp'        => imagewebp($src, $absolutePath, 85),
                'bmp'         => imagebmp($src, $absolutePath),
                default       => false,
            };

            imagedestroy($src);

            if (!$saved) {
                Log::error('Barcode overlay: failed to save image', ['path' => $absolutePath]);
                return false;
            }

            Log::info('Barcode overlay applied to image', ['path' => $storagePath]);
            return true;

        } catch (\Exception $e) {
            Log::error('Barcode image overlay failed', ['path' => $absolutePath, 'error' => $e->getMessage()]);
            return false;
        }
    }
    /**
     * Overlay a barcode onto a DOCX file using PhpWord.
     * Inserts the barcode as a floating image in the header of the target section(s).
     */
    public function overlayBarcodeOnDocx(string $absolutePath, string $storagePath, string $trackingNumber, array $options = []): bool
    {
        try {
            $xMm        = (float) ($options['x']        ?? 10);
            $yMm        = (float) ($options['y']        ?? 10);
            $widthMm    = (float) ($options['width']    ?? 60);
            $heightMm   = (float) ($options['height']   ?? 15);
            $showText   = (bool)  ($options['show_text'] ?? true);
            $targetPage = (int)   ($options['page']     ?? 1);

            $barcodeRaw  = $this->generateBarcodeRaw($trackingNumber, 2, 80);
            $tempDir     = storage_path('app/temp');
            if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);
            $tempBarcode = $tempDir . '/barcode_docx_' . md5($trackingNumber . microtime()) . '.png';
            file_put_contents($tempBarcode, $barcodeRaw);

            $widthPt  = $widthMm  * 2.835;
            $heightPt = $heightMm * 2.835;
            $xPt      = $xMm     * 2.835;
            $yPt      = $yMm     * 2.835;

            $phpWord  = WordIOFactory::load($absolutePath);
            $sections = $phpWord->getSections();

            foreach ($sections as $idx => $section) {
                if ($targetPage !== 0 && $idx !== 0) continue;
                $header = $section->getHeader() ?? $section->addHeader();
                $header->addImage($tempBarcode, [
                    'width'            => $widthPt,
                    'height'           => $heightPt,
                    'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                    'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
                    'posVertical'      => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                    'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                    'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                    'marginLeft'       => $xPt,
                    'marginTop'        => $yPt,
                    'wrappingStyle'    => \PhpOffice\PhpWord\Style\Image::WRAPPING_STYLE_NONE,
                ]);
                if ($showText) {
                    $header->addText($trackingNumber, ['size' => 7, 'name' => 'Courier New'], ['spaceAfter' => 0, 'spaceBefore' => 0]);
                }
            }

            $writer = WordIOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($absolutePath);
            if (file_exists($tempBarcode)) unlink($tempBarcode);
            Log::info('Barcode overlay applied to DOCX', ['path' => $storagePath]);
            return true;

        } catch (\Exception $e) {
            Log::error('Barcode DOCX overlay failed', ['path' => $absolutePath, 'error' => $e->getMessage()]);
            if (isset($tempBarcode) && file_exists($tempBarcode)) unlink($tempBarcode);
            return false;
        }
    }

    /**
     * Overlay a barcode onto an Excel spreadsheet using PhpSpreadsheet.
     * Inserts the barcode as a Drawing image anchored at the cell for the given mm position.
     */
    public function overlayBarcodeOnExcel(string $absolutePath, string $storagePath, string $trackingNumber, array $options = []): bool
    {
        try {
            $xMm        = (float) ($options['x']        ?? 10);
            $yMm        = (float) ($options['y']        ?? 10);
            $widthMm    = (float) ($options['width']    ?? 60);
            $heightMm   = (float) ($options['height']   ?? 15);
            $showText   = (bool)  ($options['show_text'] ?? true);
            $targetPage = (int)   ($options['page']     ?? 1);

            $barcodeRaw  = $this->generateBarcodeRaw($trackingNumber, 2, 80);
            $tempDir     = storage_path('app/temp');
            if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);
            $tempBarcode = $tempDir . '/barcode_xlsx_' . md5($trackingNumber . microtime()) . '.png';
            file_put_contents($tempBarcode, $barcodeRaw);

            $spreadsheet = SpreadsheetIOFactory::load($absolutePath);
            $sheetCount  = $spreadsheet->getSheetCount();
            $targetSheets = ($targetPage === 0) ? range(0, $sheetCount - 1) : [0];

            $colWidthMm  = 16.9;
            $rowHeightMm = 5.3;
            $col = max(1, (int) round($xMm / $colWidthMm));
            $row = max(1, (int) round($yMm / $rowHeightMm));
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $cellCoord = $colLetter . $row;

            foreach ($targetSheets as $si) {
                if ($si >= $sheetCount) continue;
                $sheet = $spreadsheet->getSheet($si);
                $drawing = new Drawing();
                $drawing->setName('Barcode');
                $drawing->setDescription($trackingNumber);
                $drawing->setPath($tempBarcode);
                $drawing->setCoordinates($cellCoord);
                $drawing->setOffsetX(0);
                $drawing->setOffsetY(0);
                $drawing->setWidth((int) round($widthMm  * 3.78));
                $drawing->setHeight((int) round($heightMm * 3.78));
                $drawing->setWorksheet($sheet);
                if ($showText) {
                    $textRow = $row + (int) ceil($heightMm / $rowHeightMm) + 1;
                    $sheet->setCellValue($colLetter . $textRow, $trackingNumber);
                    $sheet->getStyle($colLetter . $textRow)->getFont()->setSize(7);
                }
            }

            $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
            $writerType = match ($ext) {
                'xls' => 'Xls', 'ods' => 'Ods', 'csv' => 'Csv', default => 'Xlsx',
            };
            SpreadsheetIOFactory::createWriter($spreadsheet, $writerType)->save($absolutePath);
            if (file_exists($tempBarcode)) unlink($tempBarcode);
            Log::info('Barcode overlay applied to Excel', ['path' => $storagePath]);
            return true;

        } catch (\Exception $e) {
            Log::error('Barcode Excel overlay failed', ['path' => $absolutePath, 'error' => $e->getMessage()]);
            if (isset($tempBarcode) && file_exists($tempBarcode)) unlink($tempBarcode);
            return false;
        }
    }
}