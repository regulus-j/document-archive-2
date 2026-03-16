<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds percentage-based barcode positioning fields to documents table.
     * This replaces the hardcoded mm-based positioning that assumed A4 dimensions.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->decimal('barcode_x_percent', 5, 2)->nullable()->after('barcode_settings')
                ->comment('Barcode X position as percentage of document width (0-100)');
            $table->decimal('barcode_y_percent', 5, 2)->nullable()->after('barcode_x_percent')
                ->comment('Barcode Y position as percentage of document height (0-100)');
            $table->decimal('barcode_width_percent', 5, 2)->nullable()->after('barcode_y_percent')
                ->comment('Barcode width as percentage of document width (0-100)');
            $table->decimal('barcode_height_percent', 5, 2)->nullable()->after('barcode_width_percent')
                ->comment('Barcode height as percentage of document height (0-100)');
        });

        // Migrate existing mm-based coordinates to percentages (assuming A4 baseline: 210x297mm)
        DB::table('documents')
            ->whereNotNull('barcode_settings')
            ->where('barcode_applied', true)
            ->get()
            ->each(function ($document) {
                $settings = json_decode($document->barcode_settings, true);
                if (!empty($settings) && is_array($settings)) {
                    // Convert mm to percentage based on A4 dimensions (210x297mm)
                    $xPercent = isset($settings['x']) ? ($settings['x'] / 210.0) * 100 : 5.0;
                    $yPercent = isset($settings['y']) ? ($settings['y'] / 297.0) * 100 : 3.0;
                    $widthPercent = isset($settings['width']) ? ($settings['width'] / 210.0) * 100 : 25.0;
                    $heightPercent = isset($settings['height']) ? ($settings['height'] / 297.0) * 100 : 5.0;

                    DB::table('documents')
                        ->where('id', $document->id)
                        ->update([
                            'barcode_x_percent' => round($xPercent, 2),
                            'barcode_y_percent' => round($yPercent, 2),
                            'barcode_width_percent' => round($widthPercent, 2),
                            'barcode_height_percent' => round($heightPercent, 2),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'barcode_x_percent',
                'barcode_y_percent',
                'barcode_width_percent',
                'barcode_height_percent',
            ]);
        });
    }
};
