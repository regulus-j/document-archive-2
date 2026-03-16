<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentWorkflow extends Model
{
    //
    protected $table = 'document_workflows';

    protected $fillable = [
        'tracking_number',
        'document_id',
        'sender_id',
        'recipient_id',
        'recipient_office',
        'step_order',
        'workflow_type',
        'status',
        'purpose',
        'urgency',
        'due_date',
        'remarks',
        'is_paused',
        'parent_workflow_id',
        // Urgency Matrix activity tracking
        'last_activity_at',
        'inactivity_notified_at',
        'is_rerouted',
        'requires_terminal_decision',
    ];

    protected $casts = [
        'due_date'               => 'datetime',
        'last_activity_at'       => 'datetime',
        'inactivity_notified_at' => 'datetime',
        'is_rerouted'            => 'boolean',
        'is_paused'              => 'boolean',
        'requires_terminal_decision' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function recipientOffice()
    {
        return $this->belongsTo(Office::class, 'recipient_office');
    }

    /**
     * The parent workflow that spawned this sub-workflow (via forward-from-review).
     */
    public function parentWorkflow()
    {
        return $this->belongsTo(self::class, 'parent_workflow_id');
    }

    /**
     * Child workflows spawned from this workflow (via forward-from-review).
     */
    public function childWorkflows()
    {
        return $this->hasMany(self::class, 'parent_workflow_id');
    }

    /**
     * Check if this is a sub-workflow (forwarded from another workflow's review).
     */
    public function isSubWorkflow(): bool
    {
        return $this->parent_workflow_id !== null;
    }

    public function receive()
    {
        $this->status = 'received';
        $this->received_at = now();
        $this->save();
        
        // Sync document status
        $this->syncDocumentStatus();
    }

    public function approve()
    {
        $this->status = 'approved';
        $this->save();
        
        // Sync document status
        $this->syncDocumentStatus();
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
        
        // Sync document status
        $this->syncDocumentStatus();
    }
    
    public function return()
    {
        $this->status = 'returned';
        $this->save();
        
        // Sync document status
        $this->syncDocumentStatus();
    }
    
    public function refer()
    {
        $this->status = 'referred';
        $this->save();
        
        // Sync document status
        $this->syncDocumentStatus();
    }
    
    public function forward()
    {
        $this->status = 'forwarded';
        $this->save();
        
        // Sync document status
        $this->syncDocumentStatus();
    }

    /**
     * Synchronize document status based on workflow states
     */
    private function syncDocumentStatus()
    {
        $document = $this->document;
        if (!$document || !$document->status) {
            return;
        }

        // Only consider top-level workflows (exclude sub-workflows) for document status
        $allWorkflows = $document->documentWorkflow()->whereNull('parent_workflow_id')->get();
        
        // If no workflows exist, keep current status
        if ($allWorkflows->isEmpty()) {
            return;
        }

        // Check if this is a sequential workflow
        $isSequential = $allWorkflows->where('workflow_type', 'sequential')->isNotEmpty();
        
        // Terminal/completed action statuses (forwarded counts because it means the step is done — sub-workflow handles the rest)
        $completedStatuses = ['approved', 'commented', 'acknowledged', 'forwarded'];

        // A top-level workflow is only considered complete when forwarded branches are fully terminal.
        $isTopLevelWorkflowComplete = function (DocumentWorkflow $workflow) use ($completedStatuses) {
            if (!in_array($workflow->status, $completedStatuses, true)) {
                return false;
            }

            if ($workflow->status !== 'forwarded') {
                return true;
            }

            return $this->isForwardBranchComplete($workflow->id);
        };
        
        // Determine overall document status based on workflow states
        $statuses = $allWorkflows->pluck('status')->unique();
        
        if ($statuses->contains('rejected')) {
            $document->status()->update(['status' => 'rejected']);
        } elseif ($statuses->contains('returned')) {
            $document->status()->update(['status' => 'returned']);
        } elseif ($isSequential) {
            // For sequential workflows, be more nuanced about status
            $allComplete = $allWorkflows->every(fn($w) => $isTopLevelWorkflowComplete($w));
            $hasWaiting = $allWorkflows->where('status', 'waiting')->isNotEmpty();
            $hasPending = $allWorkflows->where('status', 'pending')->isNotEmpty();
            $hasReceived = $allWorkflows->where('status', 'received')->isNotEmpty();
            
            if ($allComplete && !$hasWaiting && !$hasPending && !$hasReceived) {
                // All steps completed
                $document->status()->update(['status' => 'complete']);
            } elseif ($hasPending) {
                // There's an active step
                $document->status()->update(['status' => 'received']);
            } elseif ($hasReceived) {
                // Someone has received but not yet processed
                $document->status()->update(['status' => 'received']);
            } elseif ($hasWaiting) {
                // Still in progress, waiting for next step
                $document->status()->update(['status' => 'forwarded']);
            } elseif ($statuses->contains('commented')) {
                $document->status()->update(['status' => 'commented']);
            } elseif ($statuses->contains('acknowledged')) {
                $document->status()->update(['status' => 'acknowledged']);
            }
        } elseif ($allWorkflows->every(fn($w) => $w->status === 'received')) {
            $document->status()->update(['status' => 'received']);
        } elseif ($allWorkflows->every(fn($w) => $isTopLevelWorkflowComplete($w))) {
            // For parallel workflows, mark complete when all are processed (includes forwarded)
            $document->status()->update(['status' => 'complete']);
        } elseif ($statuses->contains('commented')) {
            $document->status()->update(['status' => 'commented']);
        } elseif ($statuses->contains('acknowledged')) {
            $document->status()->update(['status' => 'acknowledged']);
        } elseif ($statuses->contains('pending')) {
            $document->status()->update(['status' => 'forwarded']);
        }
    }

    /**
     * A forwarded workflow branch is complete only when every descendant is terminal.
     * A workflow is only terminal when it has been actually processed (approved, rejected, etc.),
     * not when it's been forwarded to create another sub-workflow.
     */
    private function isForwardBranchComplete(int $workflowId): bool
    {
        $children = static::where('parent_workflow_id', $workflowId)->get();

        // No sub-workflows means nothing is pending in this branch.
        if ($children->isEmpty()) {
            return true;
        }

        // Truly terminal statuses - these mean the workflow step is actually done.
        // 'forwarded' is NOT terminal because it means delegation to a sub-workflow.
        $terminalStatuses = ['approved', 'rejected', 'acknowledged', 'commented', 'returned'];

        foreach ($children as $child) {
            // If child is in a non-terminal status (pending, waiting, received, forwarded), not complete
            if (!in_array($child->status, $terminalStatuses, true)) {
                // Special case: if child is 'forwarded', check its descendants recursively
                if ($child->status === 'forwarded') {
                    if (!$this->isForwardBranchComplete($child->id)) {
                        return false;
                    }
                    // If all descendants are complete, this forwarded child is considered complete
                    continue;
                }
                // For any other non-terminal status (pending, waiting, received), branch is incomplete
                return false;
            }
        }

        return true;
    }

    public function changeStatus($action)
    {
        $this->status = $action;
        $this->save();
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isReceived() 
    {
        return $this->status === 'received';
    }
    
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
    
    public function isReturned()
    {
        return $this->status === 'returned';
    }
    
    public function isReferred()
    {
        return $this->status === 'referred';
    }
    
    public function isForwarded()
    {
        return $this->status === 'forwarded';
    }
    
    public function isCommented()
    {
        return $this->status === 'commented';
    }
    
    public function isAcknowledged()
    {
        return $this->status === 'acknowledged';
    }
    
    public function canProcess()
    {
        return $this->status === 'received';
    }
    
    public function canReceive()
    {
        return $this->status === 'pending';
    }
    
    public function workflowActive()
    {
        return !in_array($this->status, ['rejected', 'returned', 'approved', 'commented', 'acknowledged']);
    }
    public function isOverdue()
    {
        if (!$this->due_date) {
            return false;
        }
        
        return now()->startOfDay()->gt($this->due_date);
    }
    
    public function getDaysRemainingAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        
        return now()->startOfDay()->diffInDays($this->due_date, false);
    }
    
    public function pause()
    {
        $this->is_paused = true;
        $this->save();
    }

    public function resume()
    {
        $this->is_paused = false;
        $this->save();
    }

    public function isPaused()
    {
        return $this->is_paused === true;
    }

    /**
     * Check if this workflow is sequential
     */
    public function isSequential()
    {
        return $this->workflow_type === 'sequential';
    }

    /**
     * Check if this workflow is parallel
     */
    public function isParallel()
    {
        return $this->workflow_type === 'parallel';
    }

    /**
     * Get the next step in sequential workflow
     */
    public function getNextStep()
    {
        if (!$this->isSequential()) {
            return null;
        }

        return static::where('document_id', $this->document_id)
            ->where('workflow_type', 'sequential')
            ->where('step_order', $this->step_order + 1)
            ->first();
    }

    /**
     * Get the previous step in sequential workflow
     */
    public function getPreviousStep()
    {
        if (!$this->isSequential()) {
            return null;
        }

        return static::where('document_id', $this->document_id)
            ->where('workflow_type', 'sequential')
            ->where('step_order', $this->step_order - 1)
            ->first();
    }

    /**
     * Check if this is the first step in sequential workflow
     */
    public function isFirstStep()
    {
        return $this->isSequential() && $this->step_order === 1;
    }

    /**
     * Check if this is the last step in sequential workflow
     */
    public function isLastStep()
    {
        if (!$this->isSequential()) {
            return false;
        }

        $maxStep = static::where('document_id', $this->document_id)
            ->where('workflow_type', 'sequential')
            ->max('step_order');

        return $this->step_order === $maxStep;
    }

    /**
     * Activate the next step in sequential workflow
     */
    public function activateNextStep()
    {
        if (!$this->isSequential()) {
            return false;
        }

        $nextStep = $this->getNextStep();
        if ($nextStep && $nextStep->status === 'waiting') {
            $nextStep->status = 'pending';
            $nextStep->save();
            return true;
        }

        return false;
    }

    /**
     * Scope to get only sequential workflows
     */
    public function scopeSequential($query)
    {
        return $query->where('workflow_type', 'sequential');
    }

    /**
     * Scope to get only parallel workflows
     */
    public function scopeParallel($query)
    {
        return $query->where('workflow_type', 'parallel');
    }

    /**
     * Check if this workflow should be visible to the user based on sequential rules
     */
    public function isVisibleToUser($userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        // If user is not the recipient, they can't see it
        if ($this->recipient_id !== $userId) {
            return false;
        }
        
        // If it's parallel workflow, user can see it if status is pending
        if (!$this->isSequential()) {
            return $this->status === 'pending';
        }
        
        // For sequential workflows, user can only see if:
        // 1. Status is pending (their turn), OR
        // 2. They have already processed it (received, approved, etc.)
        return $this->status === 'pending' || !in_array($this->status, ['waiting']);
    }

    /**
     * Check if workflow can be processed by user
     */
    public function canBeProcessedBy($userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        // Must be the recipient
        if ($this->recipient_id !== $userId) {
            return false;
        }
        
        // For parallel workflows, can process if pending or received
        if (!$this->isSequential()) {
            return in_array($this->status, ['pending', 'received']);
        }
        
        // For sequential workflows, can only process if status is pending or received
        return in_array($this->status, ['pending', 'received']);
    }

    /**
     * Scope to get active steps (pending status)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get waiting steps (waiting status)
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Get reroute logs for this workflow step.
     */
    public function rerouteLogs()
    {
        return \Illuminate\Support\Facades\DB::table('workflow_reroute_logs')
            ->where('workflow_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if this workflow step is inactive based on its document urgency.
     */
    public function isInactive(): bool
    {
        $document = $this->document;
        if (!$document || !$document->urgency_level) {
            return false;
        }

        $thresholds = \App\Services\DocumentUrgencyAnalyzer::getThresholds($document->urgency_level);
        $lastActivity = $this->last_activity_at ?? $this->created_at;
        $hours = $lastActivity->diffInHours(now());

        return $hours >= $thresholds['warning'];
    }

    /**
     * Alias for recipient (used in urgency system).
     */
    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Alias for sender (used in urgency system).
     */
    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
