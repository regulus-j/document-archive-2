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
        // First check if the status column exists
        if (Schema::hasColumn('document_workflows', 'status')) {
            // Modify the existing column using a DB statement
            DB::statement("ALTER TABLE document_workflows MODIFY status ENUM('uploaded', 'pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded') DEFAULT 'uploaded'");
        } else {
            // If it doesn't exist, add it
            Schema::table('document_workflows', function (Blueprint $table) {
                $table->enum('status', ['uploaded', 'pending', 'received', 'approved', 'rejected', 'returned', 'forwarded', 'referred'])
                      ->default('uploaded')
                      ->after('step_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values if needed
        DB::statement("ALTER TABLE document_workflows MODIFY status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded') DEFAULT 'pending'");
    }
};
