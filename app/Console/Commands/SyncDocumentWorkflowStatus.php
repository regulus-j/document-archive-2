<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\DocumentWorkflow;

class SyncDocumentWorkflowStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:sync-workflow-status {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize document status with their workflow statuses to fix inconsistencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->info('');
        }
        
        $this->info('Starting document workflow status synchronization...');
        $this->info('');
        
        // Get all documents with workflows
        $documents = Document::with(['status', 'documentWorkflow'])->get();
        
        $totalUpdated = 0;
        $inconsistencies = [];
        
        foreach ($documents as $document) {
            $workflows = $document->documentWorkflow;
            
            if ($workflows->isEmpty()) {
                continue;
            }
            
            $currentDocStatus = $document->status ? $document->status->status : null;
            $workflowStatuses = $workflows->pluck('status')->unique();
            
            // Determine what the document status should be based on workflows
            $suggestedStatus = $this->determineSuggestedStatus($workflowStatuses);
            
            if ($currentDocStatus !== $suggestedStatus) {
                $inconsistencies[] = [
                    'document_id' => $document->id,
                    'title' => $document->title,
                    'current_status' => $currentDocStatus ?: 'null',
                    'workflow_statuses' => $workflowStatuses->implode(', '),
                    'suggested_status' => $suggestedStatus
                ];
                
                if (!$dryRun && $document->status) {
                    $document->status->update(['status' => $suggestedStatus]);
                    $totalUpdated++;
                }
            }
        }
        
        // Display results
        if (!empty($inconsistencies)) {
            $this->info('Found ' . count($inconsistencies) . ' documents with status inconsistencies:');
            $this->info('');
            
            $headers = ['Doc ID', 'Title', 'Current Status', 'Workflow Statuses', 'Suggested Status'];
            $this->table($headers, array_map(function($item) {
                return [
                    $item['document_id'],
                    substr($item['title'], 0, 30) . (strlen($item['title']) > 30 ? '...' : ''),
                    $item['current_status'],
                    $item['workflow_statuses'],
                    $item['suggested_status']
                ];
            }, $inconsistencies));
            
            if (!$dryRun) {
                $this->info('');
                $this->info("Updated {$totalUpdated} document statuses.");
            } else {
                $this->info('');
                $this->info('Run without --dry-run flag to apply these changes.');
            }
        } else {
            $this->info('No status inconsistencies found. All documents are properly synchronized.');
        }
        
        $this->info('');
        $this->info('Synchronization complete!');
    }
    
    /**
     * Determine the suggested status based on workflow statuses
     */
    private function determineSuggestedStatus($workflowStatuses)
    {
        if ($workflowStatuses->contains('rejected')) {
            return 'rejected';
        } elseif ($workflowStatuses->contains('returned')) {
            return 'returned';
        } elseif ($workflowStatuses->every(fn($status) => $status === 'received')) {
            return 'received';
        } elseif ($workflowStatuses->every(fn($status) => in_array($status, ['approved', 'received']))) {
            return 'complete';
        } elseif ($workflowStatuses->contains('pending')) {
            return 'forwarded';
        } else {
            return 'forwarded'; // Default fallback
        }
    }
}
