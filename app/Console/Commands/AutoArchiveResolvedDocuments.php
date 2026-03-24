<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentAudit;
use App\Models\Office;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * AutoArchiveResolvedDocuments
 *
 * Runs on a schedule (every 6 hours) and checks each office that has an
 * archive_schedule_days value set. If the office's archive interval has elapsed
 * since the last run (or has never run), all resolved documents (completed,
 * rejected, acknowledged, commented) submitted by members of that office are
 * automatically moved to "archived" status.
 *
 * This command is triggered by the team leader's custom archive schedule
 * configured on the Document Archive page.
 */
class AutoArchiveResolvedDocuments extends Command
{
    protected $signature   = 'documents:auto-archive';
    protected $description = 'Auto-archive resolved documents for offices whose schedule interval has elapsed';

    /** Document statuses considered "resolved" and eligible for auto-archiving. */
    private const RESOLVED_STATUSES = ['complete', 'completed', 'rejected', 'acknowledged', 'commented'];

    public function handle(): int
    {
        $offices = Office::whereNotNull('archive_schedule_days')->get();

        if ($offices->isEmpty()) {
            $this->info('No offices with an active archive schedule. Skipping.');
            return self::SUCCESS;
        }

        $totalArchived = 0;

        foreach ($offices as $office) {
            $days = (int) $office->archive_schedule_days;

            // Determine whether the schedule interval has elapsed
            $isDue = $office->archive_last_run_at === null
                || $office->archive_last_run_at->addDays($days)->isPast();

            if (!$isDue) {
                $this->line("Office [{$office->id}] \"{$office->name}\": next run in "
                    . now()->diffInHours($office->archive_last_run_at->addDays($days)) . 'h. Skipping.');
                continue;
            }

            // Collect member user IDs for this office
            $memberIds = $office->users()->pluck('users.id')->toArray();

            if (empty($memberIds)) {
                $office->update(['archive_last_run_at' => now()]);
                continue;
            }

            // Find eligible documents: resolved, not yet archived, uploaded by office members
            $documents = Document::with(['status'])
                ->whereIn('uploader', $memberIds)
                ->whereHas('status', function ($q) {
                    $q->whereIn('status', self::RESOLVED_STATUSES);
                })
                ->get();

            $count = 0;
            foreach ($documents as $document) {
                $document->status()->update(['status' => 'archived']);

                DocumentAudit::create([
                    'document_id' => $document->id,
                    'user_id'     => $office->office_lead ?? $document->uploader,
                    'action'      => 'Auto-Archived',
                    'status'      => 'Archived',
                    'details'     => "Automatically archived by office schedule ({$days}-day interval) for office \"{$office->name}\".",
                ]);

                $count++;
            }

            $office->update(['archive_last_run_at' => now()]);
            $totalArchived += $count;

            $this->info("Office [{$office->id}] \"{$office->name}\": archived {$count} document(s).");
            Log::info('Auto-archive run completed', [
                'office_id'   => $office->id,
                'office_name' => $office->name,
                'archived'    => $count,
            ]);
        }

        $this->info("Auto-archive complete. Total archived: {$totalArchived}.");
        return self::SUCCESS;
    }
}
