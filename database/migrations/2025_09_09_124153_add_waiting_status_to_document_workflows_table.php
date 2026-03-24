<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'waiting' status to the enum for sequential workflows
        DB::statement("ALTER TABLE document_workflows MODIFY COLUMN status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded', 'commented', 'acknowledged', 'waiting') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'waiting' status from the enum
        DB::statement("UPDATE document_workflows SET status = 'pending' WHERE status = 'waiting'");
        DB::statement("ALTER TABLE document_workflows MODIFY COLUMN status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded', 'commented', 'acknowledged') DEFAULT 'pending'");
    }
};
