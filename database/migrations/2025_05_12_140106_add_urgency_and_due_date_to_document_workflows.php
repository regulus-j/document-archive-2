<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrgencyAndDueDateToDocumentWorkflows extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->nullable()->after('purpose');
            $table->date('due_date')->nullable()->after('urgency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropColumn(['urgency', 'due_date']);
        });
    }
};
