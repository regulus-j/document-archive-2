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
        Schema::table('documents', function (Blueprint $table) {
            // First, update any existing null or invalid purpose values to a default
            DB::statement("UPDATE documents SET purpose = 'appropriate_action' WHERE purpose IS NULL OR purpose NOT IN ('appropriate_action', 'comment', 'disseminate_info')");
            
            // Change the purpose column to enum
            $table->enum('purpose', ['appropriate_action', 'comment', 'disseminate_info'])->default('appropriate_action')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Revert back to string
            $table->string('purpose')->nullable()->change();
        });
    }
};
