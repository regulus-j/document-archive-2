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
            $table->boolean('requires_terminal_decision')->default(false)->after('is_rerouted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropColumn('requires_terminal_decision');
        });
    }
};
