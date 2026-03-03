<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentWorkflow;
use App\Services\DocumentUrgencyAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FastForwardNotifications
 *
 * Testing utility that triggers urgency notifications immediately by temporarily
 * back-dating workflow timestamps so the existing MonitorDocumentUrgency command
 * fires alerts without waiting for real time to pass.
 *
 * Usage examples:
 *   php artisan notifications:fast-forward --dry-run
 *   php artisan notifications:fast-forward --no-email
 *   php artisan notifications:fast-forward --document=123 --type=warning --no-email
 *   php artisan notifications:fast-forward --urgency=critical --type=escalation
 *   php artisan notifications:fast-forward --workflow=456 --type=inactivity --no-email
 *
 * This command NEVER permanently modifies timestamps — originals are always restored.
 */
class FastForwardNotifications extends Command
{
    protected $signature = 'notifications:fast-forward
        {--document= : Target a specific document ID}
        {--workflow= : Target a specific workflow ID}
        {--urgency= : Filter by urgency level (critical|high|medium|low)}
        {--type=all : Alert type to trigger: warning, escalation, inactivity, or all}
        {--no-email : Skip email delivery (in-app notifications only)}
        {--dry-run : Show what would be triggered without actually sending}';

    protected $description = 'Fast-forward urgency notifications for testing — triggers alerts immediately without waiting';

    /** Saved originals for restore. */
    private array $originals = [];

    public function handle(): int
    {
        $type     = $this->option('type');
        $dryRun   = $this->option('dry-run');
        $noEmail  = $this->option('no-email');

        if (!in_array($type, ['warning', 'escalation', 'inactivity', 'all'])) {
            $this->error("Invalid --type value \"{$type}\". Use: warning, escalation, inactivity, or all.");
            return self::FAILURE;
        }

        // ── Gather target workflows ──────────────────────────────────────
        $workflows = $this->getTargetWorkflows();

        if ($workflows->isEmpty()) {
            $this->warn('No active workflows matched your filters.');
            return self::SUCCESS;
        }

        // ── Preview table ────────────────────────────────────────────────
        $this->info("Found {$workflows->count()} active workflow(s) to fast-forward.\n");
        $this->previewTable($workflows, $type);

        if ($dryRun) {
            $this->newLine();
            $this->info('🏁 Dry-run complete — no notifications were sent.');
            return self::SUCCESS;
        }

        // ── Confirm ──────────────────────────────────────────────────────
        if (!$this->confirm('Proceed with sending these notifications?', true)) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        // ── Execute with timestamp manipulation ──────────────────────────
        $this->originals = [];

        try {
            $this->backDateWorkflows($workflows, $type);
            $this->clearCooldowns($workflows, $type);

            // Suppress email if requested
            if ($noEmail) {
                config(['mail.default' => 'log']);
                $this->line('<fg=yellow>📧 Email suppressed</> — notifications will be in-app only (emails logged).');
            }

            $this->newLine();
            $this->info('⏩ Running urgency monitor with fast-forwarded timestamps...');
            $this->newLine();

            Artisan::call('documents:monitor-urgency', [], $this->output);

            $this->newLine();
            $this->info('✅ Fast-forward complete! Notifications have been triggered.');

        } finally {
            // ALWAYS restore original timestamps
            $this->restoreWorkflows();
            $this->line('<fg=green>🔄 Original timestamps restored.</> No production data was modified.');
        }

        return self::SUCCESS;
    }

    /**
     * Build the target workflow query with optional filters.
     */
    protected function getTargetWorkflows()
    {
        $query = DocumentWorkflow::with(['document', 'recipientUser', 'senderUser'])
            ->whereIn('status', ['pending', 'waiting', 'received'])
            ->whereHas('document', fn($q) => $q->whereNotNull('urgency_level'));

        if ($id = $this->option('document')) {
            $query->where('document_id', $id);
        }

        if ($id = $this->option('workflow')) {
            $query->where('id', $id);
        }

        if ($urgency = $this->option('urgency')) {
            if (!in_array($urgency, ['critical', 'high', 'medium', 'low'])) {
                $this->error("Invalid --urgency value \"{$urgency}\".");
                return collect();
            }
            $query->whereHas('document', fn($q) => $q->where('urgency_level', $urgency));
        }

        return $query->get();
    }

    /**
     * Display a preview table of workflows that will be affected.
     */
    protected function previewTable($workflows, string $type): void
    {
        $rows = [];
        foreach ($workflows as $wf) {
            $doc = $wf->document;
            $urgency = $doc->urgency_level ?? 'unknown';
            $thresholds = DocumentUrgencyAnalyzer::getThresholds($urgency);
            $inactivity = $this->getInactivityThreshold($urgency);

            $types = [];
            if (in_array($type, ['warning', 'all'])) {
                $types[] = "Warning ({$thresholds['warning']}h)";
            }
            if (in_array($type, ['escalation', 'all'])) {
                $types[] = "Escalation ({$thresholds['escalation']}h)";
            }
            if (in_array($type, ['inactivity', 'all'])) {
                $types[] = "Inactivity ({$inactivity}h)";
            }

            $rows[] = [
                $wf->id,
                $doc->id,
                \Illuminate\Support\Str::limit($doc->title, 30),
                strtoupper($urgency),
                $wf->recipientUser->full_name ?? 'N/A',
                implode(', ', $types),
            ];
        }

        $this->table(
            ['Workflow', 'Doc ID', 'Title', 'Urgency', 'Recipient', 'Alerts to Trigger'],
            $rows
        );
    }

    /**
     * Back-date workflow timestamps to trigger the desired alert type.
     */
    protected function backDateWorkflows($workflows, string $type): void
    {
        foreach ($workflows as $wf) {
            $doc = $wf->document;
            $urgency = $doc->urgency_level ?? 'medium';
            $thresholds = DocumentUrgencyAnalyzer::getThresholds($urgency);
            $inactivityThreshold = $this->getInactivityThreshold($urgency);

            // Save originals
            $this->originals[$wf->id] = [
                'created_at'             => $wf->getRawOriginal('created_at'),
                'last_activity_at'       => $wf->getRawOriginal('last_activity_at'),
                'inactivity_notified_at' => $wf->getRawOriginal('inactivity_notified_at'),
            ];

            $updates = [];

            // Determine how far to back-date created_at
            if (in_array($type, ['escalation', 'all'])) {
                // Back-date far enough for escalation (which also passes warning threshold)
                $hours = $thresholds['escalation'] + 1;
                $updates['created_at'] = now()->subHours($hours);
            } elseif ($type === 'warning') {
                // Back-date just past warning but before escalation
                $hours = $thresholds['warning'] + 1;
                $updates['created_at'] = now()->subHours($hours);
            }

            // Inactivity: back-date last_activity_at and clear flag
            if (in_array($type, ['inactivity', 'all'])) {
                $updates['last_activity_at'] = now()->subHours($inactivityThreshold + 1);
                $updates['inactivity_notified_at'] = null;
            }

            if (!empty($updates)) {
                // Use raw DB update to bypass model events and mutators
                DB::table('document_workflows')
                    ->where('id', $wf->id)
                    ->update($updates);
            }
        }

        $this->line("<fg=cyan>⏪ Back-dated {$workflows->count()} workflow(s).</>");
    }

    /**
     * Clear cooldown records so the monitor sees these as fresh.
     */
    protected function clearCooldowns($workflows, string $type): void
    {
        $workflowIds = $workflows->pluck('id')->toArray();
        $documentIds = $workflows->pluck('document_id')->unique()->toArray();

        $query = DB::table('document_urgency_notifications')
            ->whereIn('document_id', $documentIds)
            ->whereIn('workflow_id', $workflowIds);

        if ($type !== 'all') {
            $query->where('notification_type', $type);
        }

        $deleted = $query->delete();

        if ($deleted > 0) {
            $this->line("<fg=cyan>🗑️  Cleared {$deleted} cooldown record(s).</>");
        }
    }

    /**
     * Restore all original timestamps.
     */
    protected function restoreWorkflows(): void
    {
        foreach ($this->originals as $workflowId => $original) {
            DB::table('document_workflows')
                ->where('id', $workflowId)
                ->update($original);
        }
    }

    /**
     * Get the inactivity threshold in hours for a given urgency level.
     * Mirrors MonitorDocumentUrgency::getInactivityThreshold().
     */
    protected function getInactivityThreshold(string $level): int
    {
        return match ($level) {
            'critical' => 1,
            'high'     => 6,
            'medium'   => 24,
            'low'      => 72,
            default    => 24,
        };
    }
}
