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
            $table->unsignedBigInteger('parent_workflow_id')->nullable()->after('is_paused');
            $table->foreign('parent_workflow_id')
                  ->references('id')
                  ->on('document_workflows')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropForeign(['parent_workflow_id']);
            $table->dropColumn('parent_workflow_id');
        });
    }
};
