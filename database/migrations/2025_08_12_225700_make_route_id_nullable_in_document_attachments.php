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
            // Drop the foreign key constraint first
            $table->dropForeign(['route_id']);
            
            // Make route_id nullable
            $table->unsignedBigInteger('route_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('route_id')->references('id')->on('document_workflows')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_attachments', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['route_id']);
            
            // Make route_id required again
            $table->unsignedBigInteger('route_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('route_id')->references('id')->on('document_workflows')->onDelete('cascade');
        });
    }
};
