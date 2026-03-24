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
            $table->enum('workflow_type', ['parallel', 'sequential'])->default('parallel')->after('step_order');
            $table->index(['document_id', 'workflow_type', 'step_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropIndex(['document_id', 'workflow_type', 'step_order']);
            $table->dropColumn('workflow_type');
        });
    }
};
