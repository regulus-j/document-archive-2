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
        Schema::create('document_deletion_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedInteger('retention_days')->default(365); // Default 1 year retention
            $table->unsignedBigInteger('storage_limit_mb')->nullable(); // Storage limit in MB that triggers deletion
            $table->boolean('is_active')->default(true);
            $table->enum('criteria', ['age', 'storage', 'both'])->default('age');
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('company_accounts')->onDelete('cascade');
        });

        // Add storage_size field to documents to track file sizes
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_size')->nullable()->after('path')->comment('File size in bytes');
            $table->unsignedBigInteger('company_id')->nullable()->after('uploader');
            $table->foreign('company_id')->references('id')->on('company_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['storage_size', 'company_id']);
        });
        
        Schema::dropIfExists('document_deletion_schedules');
    }
};
