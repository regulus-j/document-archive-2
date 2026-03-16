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
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->boolean('is_final_recipient')->default(false)->after('requires_terminal_decision');
            $table->index('is_final_recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropIndex(['is_final_recipient']);
            $table->dropColumn('is_final_recipient');
        });
    }
};
