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
        // First, let's check current values and update any that would be invalid
        DB::statement("UPDATE document_workflows SET status = 'received' WHERE status NOT IN ('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded')");
        
        // Now modify the enum to include new status values
        DB::statement("ALTER TABLE document_workflows MODIFY COLUMN status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded', 'commented', 'acknowledged') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert any new status values to 'received' before changing back
        DB::statement("UPDATE document_workflows SET status = 'received' WHERE status IN ('commented', 'acknowledged')");
        
        // Revert to original enum without new status values
        DB::statement("ALTER TABLE document_workflows MODIFY COLUMN status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded') DEFAULT 'pending'");
    }
};
