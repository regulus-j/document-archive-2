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
        // Add urgency analysis fields to documents table (skip if already present)
        if (!Schema::hasColumn('documents', 'urgency_level')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->nullable()->after('classification');
                $table->text('urgency_reasoning')->nullable()->after('urgency_level');
                $table->json('urgency_keywords')->nullable()->after('urgency_reasoning');
                $table->timestamp('urgency_analyzed_at')->nullable()->after('urgency_keywords');
                $table->integer('urgency_confidence')->nullable()->after('urgency_analyzed_at'); // 0-100
                $table->timestamp('urgency_escalated_at')->nullable()->after('urgency_confidence');
                $table->integer('escalation_count')->default(0)->after('urgency_escalated_at');
            });
        }

        // Create document_urgency_notifications tracking table
        if (!Schema::hasTable('document_urgency_notifications')) {
            Schema::create('document_urgency_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('workflow_id')->nullable();
                $table->string('notification_type'); // warning, escalation, inactivity, reroute
                $table->unsignedBigInteger('recipient_user_id')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->foreign('workflow_id')->references('id')->on('document_workflows')->onDelete('cascade');
                $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('set null');
                $table->index(['document_id', 'workflow_id', 'notification_type']);
            });
        }

        // Create workflow_reroute_logs table for tracking rerouting history
        if (!Schema::hasTable('workflow_reroute_logs')) {
            Schema::create('workflow_reroute_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('workflow_id');
                $table->unsignedBigInteger('old_recipient_id');
                $table->unsignedBigInteger('new_recipient_id');
                $table->unsignedBigInteger('rerouted_by');
                $table->text('reason')->nullable();
                $table->string('old_status')->nullable();
                $table->timestamps();

                $table->foreign('workflow_id')->references('id')->on('document_workflows')->onDelete('cascade');
                $table->foreign('old_recipient_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('new_recipient_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('rerouted_by')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Add inactivity tracking to document_workflows
        if (!Schema::hasColumn('document_workflows', 'last_activity_at')) {
            Schema::table('document_workflows', function (Blueprint $table) {
                $table->timestamp('last_activity_at')->nullable()->after('received_at');
                $table->timestamp('inactivity_notified_at')->nullable()->after('last_activity_at');
                $table->boolean('is_rerouted')->default(false)->after('inactivity_notified_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_workflows', function (Blueprint $table) {
            $table->dropColumn(['last_activity_at', 'inactivity_notified_at', 'is_rerouted']);
        });

        Schema::dropIfExists('workflow_reroute_logs');
        Schema::dropIfExists('document_urgency_notifications');

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'urgency_level', 'urgency_reasoning', 'urgency_keywords',
                'urgency_analyzed_at', 'urgency_confidence',
                'urgency_escalated_at', 'escalation_count',
            ]);
        });
    }
};
