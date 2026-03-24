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
        // Check if the column doesn't already exist before adding it
        if (!Schema::hasColumn('document_workflows', 'status')) {
            Schema::table('document_workflows', function (Blueprint $table) {
                $table->enum('status', ['uploaded', 'pending', 'received', 'approved', 'rejected', 'forwarded'])->default('uploaded');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Properly drop the column when rolling back
        if (Schema::hasColumn('document_workflows', 'status')) {
            Schema::table('document_workflows', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
