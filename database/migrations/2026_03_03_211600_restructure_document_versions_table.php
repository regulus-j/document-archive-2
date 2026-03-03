<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Restructure the existing (unused) document_versions table
     * from a linked-list design to a proper versioning table.
     */
    public function up(): void
    {
        Schema::table('document_versions', function (Blueprint $table) {
            // Drop unused linked-list foreign keys and columns
            $table->dropForeign(['prevdoc_id']);
            $table->dropForeign(['nextdoc_id']);
            $table->dropColumn(['prevdoc_id', 'nextdoc_id']);

            // Add versioning columns
            $table->unsignedInteger('version_number')->after('doc_id');
            $table->string('file_path')->after('version_number');
            $table->string('original_filename')->after('file_path');
            $table->string('mime_type')->nullable()->after('original_filename');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            $table->unsignedBigInteger('uploaded_by')->after('file_size');
            $table->text('change_notes')->nullable()->after('uploaded_by');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->index(['doc_id', 'version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_versions', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropIndex(['doc_id', 'version_number']);
            $table->dropColumn([
                'version_number',
                'file_path',
                'original_filename',
                'mime_type',
                'file_size',
                'uploaded_by',
                'change_notes',
                'created_at',
                'updated_at',
            ]);

            // Restore original linked-list columns
            $table->unsignedBigInteger('prevdoc_id')->nullable();
            $table->unsignedBigInteger('nextdoc_id')->nullable();
            $table->foreign('prevdoc_id')->references('id')->on('documents');
            $table->foreign('nextdoc_id')->references('id')->on('documents');
        });
    }
};
