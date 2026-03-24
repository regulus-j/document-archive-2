<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentWorkflow;

class DocumentStatusService
{
    /**
     * Get the effective status for a document based on user context
     */
    public static function getEffectiveStatus(Document $document, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        // Check if user has a specific workflow for this document
        $userWorkflow = $document->documentWorkflow()
            ->where('recipient_id', $userId)
            ->orderBy('step_order', 'desc')
            ->first();
        
        if ($userWorkflow) {
            return [
                'status' => $userWorkflow->status,
                'source' => 'workflow',
                'workflow_id' => $userWorkflow->id,
                'due_date' => $userWorkflow->due_date,
                'urgency' => $userWorkflow->urgency,
                'can_receive' => $userWorkflow->canReceive(),
                'can_process' => $userWorkflow->canProcess(),
                'is_overdue' => $userWorkflow->isOverdue(),
            ];
        }
        
        // Fallback to document status
        return [
            'status' => $document->status ? $document->status->status : 'unknown',
            'source' => 'document',
            'workflow_id' => null,
            'due_date' => null,
            'urgency' => null,
            'can_receive' => false,
            'can_process' => false,
            'is_overdue' => false,
        ];
    }
    
    /**
     * Get status display information (colors, icons, etc.)
     */
    public static function getStatusDisplay($status)
    {
        $statusConfig = [
            'pending' => [
                'color' => 'yellow',
                'bg_class' => 'bg-yellow-100',
                'text_class' => 'text-yellow-800',
                'icon' => 'clock',
                'label' => 'Pending',
                'description' => 'Waiting for action'
            ],
            'received' => [
                'color' => 'green',
                'bg_class' => 'bg-green-100',
                'text_class' => 'text-green-800',
                'icon' => 'check',
                'label' => 'Received',
                'description' => 'Document has been received'
            ],
            'forwarded' => [
                'color' => 'blue',
                'bg_class' => 'bg-blue-100',
                'text_class' => 'text-blue-800',
                'icon' => 'arrow-right',
                'label' => 'Forwarded',
                'description' => 'Document is in workflow'
            ],
            'approved' => [
                'color' => 'green',
                'bg_class' => 'bg-green-100',
                'text_class' => 'text-green-800',
                'icon' => 'check-circle',
                'label' => 'Approved',
                'description' => 'Document has been approved'
            ],
            'rejected' => [
                'color' => 'red',
                'bg_class' => 'bg-red-100',
                'text_class' => 'text-red-800',
                'icon' => 'x-circle',
                'label' => 'Rejected',
                'description' => 'Document has been rejected'
            ],
            'returned' => [
                'color' => 'orange',
                'bg_class' => 'bg-orange-100',
                'text_class' => 'text-orange-800',
                'icon' => 'arrow-left',
                'label' => 'Returned',
                'description' => 'Document returned for revision'
            ],
            'referred' => [
                'color' => 'purple',
                'bg_class' => 'bg-purple-100',
                'text_class' => 'text-purple-800',
                'icon' => 'share',
                'label' => 'Referred',
                'description' => 'Document has been referred'
            ],
            'complete' => [
                'color' => 'green',
                'bg_class' => 'bg-green-100',
                'text_class' => 'text-green-800',
                'icon' => 'check-double',
                'label' => 'Complete',
                'description' => 'Workflow is complete'
            ],
            'needs_revision' => [
                'color' => 'red',
                'bg_class' => 'bg-red-100',
                'text_class' => 'text-red-800',
                'icon' => 'edit',
                'label' => 'Needs Revision',
                'description' => 'Document needs to be revised'
            ],
        ];
        
        return $statusConfig[$status] ?? [
            'color' => 'gray',
            'bg_class' => 'bg-gray-100',
            'text_class' => 'text-gray-800',
            'icon' => 'question',
            'label' => ucfirst($status),
            'description' => 'Unknown status'
        ];
    }
    
    /**
     * Check if a document can be received by a user
     */
    public static function canReceiveDocument(Document $document, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        // Check if document has been recalled
        if ($document->status && $document->status->status === 'recalled') {
            return false;
        }
        
        $userWorkflow = $document->documentWorkflow()
            ->where('recipient_id', $userId)
            ->whereIn('status', ['pending'])
            ->first();
            
        return $userWorkflow !== null;
    }
    
    /**
     * Check if a user can access workflow for a document
     * This enforces the receive-first logic
     */
    public static function canAccessWorkflow(Document $document, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        // Check if document has been recalled
        if ($document->status && $document->status->status === 'recalled') {
            return false;
        }
        
        $userWorkflow = $document->documentWorkflow()
            ->where('recipient_id', $userId)
            ->first();
            
        if (!$userWorkflow) {
            return false;
        }
        
        // User must have "received" the document first (moved past "pending" status)
        return !in_array($userWorkflow->status, ['pending']);
    }
    
    /**
     * Get documents that are pending receipt for a user
     */
    public static function getPendingReceiveDocuments($userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        return Document::whereHas('documentWorkflow', function($query) use ($userId) {
            $query->where('recipient_id', $userId)
                  ->where('status', 'pending');
        })->with(['documentWorkflow' => function($query) use ($userId) {
            $query->where('recipient_id', $userId);
        }])->get();
    }
    
    /**
     * Get all possible status transitions for a workflow
     */
    public static function getAvailableActions($workflowStatus)
    {
        $transitions = [
            'pending' => ['receive'],
            'received' => ['approve', 'reject', 'return', 'refer', 'forward'],
            'approved' => [],
            'rejected' => [],
            'returned' => [],
            'referred' => ['receive'],
            'forwarded' => ['receive'],
        ];
        
        return $transitions[$workflowStatus] ?? [];
    }
}
