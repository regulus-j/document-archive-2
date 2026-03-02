<?php

namespace App\Console\Commands;

use App\Mail\DocumentUrgencyAlert;
use App\Models\Document;
use App\Models\DocumentWorkflow;
use App\Models\Notifications;
use App\Services\DocumentUrgencyAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * MonitorDocumentUrgency
 *
 * Scheduled command that scans all active document workflows and:
 *   1. Sends WARNING notifications when approaching response deadline.
 *   2. Sends ESCALATION alerts when the deadline has passed.
 *   3. Sends INACTIVITY alerts when a workflow step has been idle too long.
 *
 * Intended to run every 30 minutes via the Laravel scheduler.
 */
class MonitorDocumentUrgency extends Command
{
    protected $signature = 'documents:monitor-urgency';
    protected $description = 'Monitor active document workflows and send urgency-based notifications';

    public function handle(): int
    {
        $this->info('Scanning active workflows for urgency monitoring...');

        $activeWorkflows = DocumentWorkflow::with(['document', 'recipientUser', 'senderUser'])
            ->whereIn('status', ['pending', 'waiting', 'received'])
            ->whereHas('document', fn($q) => $q->whereNotNull('urgency_level'))
            ->get();

        $warnings   = 0;
        $escalations = 0;
        $inactivity  = 0;

        foreach ($activeWorkflows as $workflow) {
            $document = $workflow->document;
            if (!$document || !$document->urgency_level) {
                continue;
            }

            $thresholds   = DocumentUrgencyAnalyzer::getThresholds($document->urgency_level);
            $hoursSince   = $workflow->created_at->diffInHours(now());
            $lastActivity = $workflow->last_activity_at ? \Carbon\Carbon::parse($workflow->last_activity_at) : $workflow->created_at;
            $inactiveHours = $lastActivity->diffInHours(now());

            // Check for escalation (deadline exceeded)
            if ($hoursSince >= $thresholds['escalation']) {
                if ($this->shouldNotify($document->id, $workflow->id, 'escalation', $document->urgency_level)) {
                    $this->sendAlert($document, $workflow, 'escalation');
                    $this->incrementEscalation($document);
                    $escalations++;
                }
            }
            // Check for warning (approaching deadline)
            elseif ($hoursSince >= $thresholds['warning']) {
                if ($this->shouldNotify($document->id, $workflow->id, 'warning', $document->urgency_level)) {
                    $this->sendAlert($document, $workflow, 'warning');
                    $warnings++;
                }
            }

            // Check for inactivity (no activity on the workflow)
            $inactivityThreshold = $this->getInactivityThreshold($document->urgency_level);
            if ($inactiveHours >= $inactivityThreshold && !$workflow->inactivity_notified_at) {
                $this->sendAlert($document, $workflow, 'inactivity');
                $workflow->update(['inactivity_notified_at' => now()]);
                $inactivity++;
            }
        }

        $this->info("Monitoring complete: {$warnings} warnings, {$escalations} escalations, {$inactivity} inactivity alerts.");
        Log::info('Document urgency monitoring run', compact('warnings', 'escalations', 'inactivity'));

        return self::SUCCESS;
    }

    /**
     * Resolve the notification cooldown (in hours) based on urgency level.
     *
     * The higher the urgency, the shorter the cooldown so alerts repeat more often.
     * The minimum cooldown is 6 hours regardless of urgency level.
     */
    protected function getNotificationCooldownHours(string $urgencyLevel): int
    {
        return match ($urgencyLevel) {
            'critical' => 6,   // Most frequent — minimum allowed
            'high'     => 8,
            'medium'   => 12,
            'low'      => 24,
            default    => 12,
        };
    }

    /**
     * Check if a notification of this type was already sent recently.
     * Cooldown period is driven by the document's urgency level.
     */
    protected function shouldNotify(int $documentId, int $workflowId, string $type, string $urgencyLevel): bool
    {
        $cooldownHours = $this->getNotificationCooldownHours($urgencyLevel);

        return !DB::table('document_urgency_notifications')
            ->where('document_id', $documentId)
            ->where('workflow_id', $workflowId)
            ->where('notification_type', $type)
            ->where('sent_at', '>=', now()->subHours($cooldownHours))
            ->exists();
    }

    /**
     * Whether a document's urgency level warrants email delivery.
     * Only critical and high urgency documents trigger emails;
     * medium and low receive in-app notifications only.
     */
    protected function shouldSendEmail(string $urgencyLevel): bool
    {
        return in_array($urgencyLevel, ['critical', 'high'], true);
    }

    /**
     * Send an urgency alert — email (critical/high only) and in-app notification.
     */
    protected function sendAlert(Document $document, DocumentWorkflow $workflow, string $type): void
    {
        $recipient   = $workflow->recipientUser;
        $sender      = $workflow->senderUser;
        $viewUrl     = url("/documents/{$document->id}");
        $sendEmail   = $this->shouldSendEmail($document->urgency_level);

        // Notify the assigned recipient
        if ($recipient && $recipient->email) {
            if ($sendEmail) {
                try {
                    Mail::to($recipient->email)->send(new DocumentUrgencyAlert(
                        $document,
                        $workflow,
                        $type,
                        ['view_url' => $viewUrl]
                    ));
                } catch (\Throwable $e) {
                    Log::warning("Failed to send urgency email to {$recipient->email}", ['error' => $e->getMessage()]);
                }
            }

            Notifications::create([
                'user_id' => $recipient->id,
                'type'    => "urgency_{$type}",
                'data'    => json_encode([
                    'document_id'    => $document->id,
                    'document_title' => $document->title,
                    'urgency_level'  => $document->urgency_level,
                    'alert_type'     => $type,
                    'message'        => $this->getNotificationMessage($document, $type),
                ]),
            ]);
        }

        // For escalations, also notify the sender/uploader
        if ($type === 'escalation' && $sender && $sender->id !== ($recipient->id ?? 0)) {
            if ($sendEmail && $sender->email) {
                try {
                    Mail::to($sender->email)->send(new DocumentUrgencyAlert(
                        $document,
                        $workflow,
                        $type,
                        ['view_url' => $viewUrl]
                    ));
                } catch (\Throwable $e) {
                    Log::warning("Failed to send escalation email to sender {$sender->email}", ['error' => $e->getMessage()]);
                }
            }

            Notifications::create([
                'user_id' => $sender->id,
                'type'    => "urgency_{$type}",
                'data'    => json_encode([
                    'document_id'    => $document->id,
                    'document_title' => $document->title,
                    'urgency_level'  => $document->urgency_level,
                    'alert_type'     => $type,
                    'message'        => "Your document \"{$document->title}\" has exceeded its response time at workflow step assigned to {$recipient->full_name}.",
                ]),
            ]);
        }

        // Record the notification
        DB::table('document_urgency_notifications')->insert([
            'document_id'       => $document->id,
            'workflow_id'       => $workflow->id,
            'notification_type' => $type,
            'recipient_user_id' => $recipient->id ?? null,
            'sent_at'           => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Increment the document's escalation counter.
     */
    protected function incrementEscalation(Document $document): void
    {
        $document->update([
            'escalation_count'    => ($document->escalation_count ?? 0) + 1,
            'urgency_escalated_at' => now(),
        ]);
    }

    /**
     * Get a human-readable notification message.
     */
    protected function getNotificationMessage(Document $document, string $type): string
    {
        return match ($type) {
            'warning'    => "Document \"{$document->title}\" ({$document->urgency_level} priority) is approaching its response deadline. Please take action soon.",
            'escalation' => "URGENT: Document \"{$document->title}\" ({$document->urgency_level} priority) has exceeded its response time. Immediate action required.",
            'inactivity' => "Document \"{$document->title}\" workflow step has been inactive. Please process or reroute.",
            default      => "Action needed for document \"{$document->title}\".",
        };
    }

    /**
     * Get the inactivity threshold in hours for a given urgency level.
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
