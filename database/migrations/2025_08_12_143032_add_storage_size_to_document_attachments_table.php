<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_attachments', function (Blueprint $table) {
            $table->bigInteger('storage_size')->nullable()->after('path')->comment('File size in bytes');
            $table->string('mime_type')->nullable()->after('storage_size')->comment('File MIME type');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_attachments', function (Blueprint $table) {
            $table->dropColumn('storage_size');
            $table->dropColumn('mime_type');
        });
    }
};
