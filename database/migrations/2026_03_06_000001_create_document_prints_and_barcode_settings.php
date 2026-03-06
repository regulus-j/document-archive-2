<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the document_prints table to track how many copies of a document
     * have been printed, by whom, and when. Also tracks which version was printed.
     */
    public function up(): void
    {
        Schema::create('document_prints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('version_id')->nullable()->comment('NULL = current version at time of print');
            $table->unsignedBigInteger('printed_by');
            $table->unsignedInteger('copies')->default(1)->comment('Number of copies printed in this batch');
            $table->string('print_reason')->nullable()->comment('Why the document was printed');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->foreign('version_id')->references('id')->on('document_versions')->nullOnDelete();
            $table->foreign('printed_by')->references('id')->on('users')->cascadeOnDelete();

            $table->index(['document_id', 'created_at']);
        });

        // Add barcode overlay settings to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->json('barcode_settings')->nullable()->after('path')
                ->comment('JSON: {x, y, width, height, page, show_text} for barcode overlay position/size');
            $table->boolean('barcode_applied')->default(false)->after('barcode_settings')
                ->comment('Whether barcode has been overlaid on the document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_prints');

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['barcode_settings', 'barcode_applied']);
        });
    }
};
