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
        // Add delegation tracking fields to document_workflows
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->enum('delegation_type', ['retain', 'delegate'])->nullable()->after('parent_workflow_id');
            $table->boolean('requires_terminal_decision')->default(false)->after('delegation_type');
            $table->integer('delegation_depth')->default(0)->after('requires_terminal_decision');
            $table->enum('wait_policy', ['wait_all', 'decide_anytime'])->nullable()->default('wait_all')->after('delegation_depth');
            $table->timestamp('terminal_decision_notified_at')->nullable()->after('wait_policy');
            
            // Add index for performance
            $table->index('delegation_depth');
            $table->index('requires_terminal_decision');
        });

        // Update status enum to include 'delegated'
        DB::statement("ALTER TABLE document_workflows MODIFY COLUMN status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded', 'commented', 'acknowledged', 'delegated', 'waiting') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropIndex(['delegation_depth']);
            $table->dropIndex(['requires_terminal_decision']);
            $table->dropColumn([
                'delegation_type',
                'requires_terminal_decision',
                'delegation_depth',
                'wait_policy',
                'terminal_decision_notified_at',
            ]);
        });

        // Revert status enum
        DB::statement("UPDATE document_workflows SET status = 'forwarded' WHERE status = 'delegated'");
        DB::statement("ALTER TABLE document_workflows MODIFY COLUMN status ENUM('pending', 'received', 'approved', 'rejected', 'returned', 'referred', 'forwarded', 'commented', 'acknowledged', 'waiting') DEFAULT 'pending'");
    }
};
