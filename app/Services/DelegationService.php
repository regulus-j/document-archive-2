<?php

namespace App\Services;

use App\Models\DocumentWorkflow;
use App\Models\WorkflowDelegationChain;
use App\Models\Notifications;
use App\Models\DocumentAudit;
use Illuminate\Support\Facades\Log;

class DelegationService
{
    /**
     * Calculate delegation depth for a workflow by traversing parent chain.
     */
    public function calculateDelegationDepth(DocumentWorkflow $workflow): int
    {
        if (!$workflow->parent_workflow_id) {
            return 0;
        }

        $depth = 0;
        $current = $workflow;

        while ($current->parent_workflow_id) {
            $parent = DocumentWorkflow::find($current->parent_workflow_id);
            if (!$parent) {
                break;
            }
            $depth++;
            $current = $parent;
        }

        return $depth;
    }

    /**
     * Check if terminal decision is required for this workflow.
     * Returns true if workflow has sub-workflows that are complete and user retained authority.
     */
    public function checkTerminalDecisionRequired(DocumentWorkflow $workflow): bool
    {
        // Only applies if user forwarded and retained decision authority
        if ($workflow->delegation_type !== 'retain') {
            return false;
        }

        // Only applies if workflow was forwarded
        if ($workflow->status !== 'forwarded' && $workflow->status !== 'received') {
            return false;
        }

        // Check if all required sub-workflows are complete
        return $workflow->isAllSubWorkflowsComplete();
    }

    /**
     * Notify all users in the delegation chain about the terminal decision.
     */
    public function notifyDelegationChain(DocumentWorkflow $terminalWorkflow, string $decision, string $remarks = ''): void
    {
        $delegationPath = $this->buildDelegationChainPath($terminalWorkflow);
        $delegators = $this->getDelegatorsInChain($terminalWorkflow);

        $decisionMaker = $terminalWorkflow->recipient;
        $document = $terminalWorkflow->document;

        foreach ($delegators as $delegatorData) {
            if ($delegatorData['user_id'] == $terminalWorkflow->recipient_id) {
                continue; // Skip notifying themselves
            }

            Notifications::create([
                'user_id' => $delegatorData['user_id'],
                'type' => 'delegation_chain_completed',
                'data' => json_encode([
                    'document_id' => $document->id,
                    'message' => sprintf(
                        'Document you %s has been %s by %s',
                        $delegatorData['delegation_type'] === 'delegate' ? 'delegated' : 'forwarded for input',
                        $decision,
                        $decisionMaker ? ($decisionMaker->first_name . ' ' . $decisionMaker->last_name) : 'recipient'
                    ),
                    'title' => $document->title,
                    'decision' => $decision,
                    'decision_maker' => $decisionMaker ? ($decisionMaker->first_name . ' ' . $decisionMaker->last_name) : 'Unknown',
                    'delegation_path' => $delegationPath,
                    'remarks' => $remarks,
                    'workflow_id' => $delegatorData['workflow_id'],
                ]),
            ]);
        }

        Log::info('Delegation chain notified', [
            'terminal_workflow_id' => $terminalWorkflow->id,
            'decision' => $decision,
            'delegation_path' => $delegationPath,
            'notified_count' => count($delegators),
        ]);
    }

    /**
     * Get wait policy status message for a workflow.
     */
    public function getWaitPolicyStatus(DocumentWorkflow $workflow): ?string
    {
        if (!$workflow->hasSubWorkflows()) {
            return null;
        }

        $terminalStatuses = ['approved', 'rejected', 'acknowledged', 'commented', 'returned', 'delegated'];
        $children = $workflow->childWorkflows;
        $completedCount = $children->whereIn('status', $terminalStatuses)->count();
        $totalCount = $children->count();

        if ($workflow->wait_policy === 'decide_anytime') {
            if ($completedCount > 0) {
                return "You can make your decision now ($completedCount of $totalCount consultations complete).";
            }
            return "Waiting for any consultation to complete ($completedCount of $totalCount done).";
        }

        // wait_all
        if ($completedCount === $totalCount) {
            return "All consultations complete. Decision required.";
        }
        return "Waiting for all consultations ($completedCount of $totalCount complete).";
    }

    /**
     * Build a human-readable delegation chain path (e.g., "Alice → Bob → Charlie").
     */
    public function buildDelegationChainPath(DocumentWorkflow $workflow): string
    {
        $chain = [];
        $current = $workflow;

        // Build chain from current up to root
        while ($current) {
            $userName = $current->recipient 
                ? ($current->recipient->first_name . ' ' . $current->recipient->last_name)
                : 'Unknown';
            
            array_unshift($chain, $userName);

            if (!$current->parent_workflow_id) {
                break;
            }

            $current = DocumentWorkflow::find($current->parent_workflow_id);
        }

        return implode(' → ', $chain);
    }

    /**
     * Get all delegators in the chain (workflows that forwarded to create this workflow).
     */
    public function getDelegatorsInChain(DocumentWorkflow $workflow): array
    {
        $delegators = [];
        $current = $workflow;

        while ($current->parent_workflow_id) {
            $parent = DocumentWorkflow::find($current->parent_workflow_id);
            if (!$parent) {
                break;
            }

            $delegators[] = [
                'workflow_id' => $parent->id,
                'user_id' => $parent->recipient_id,
                'delegation_type' => $parent->delegation_type,
                'status' => $parent->status,
            ];

            $current = $parent;
        }

        return $delegators;
    }

    /**
     * Create delegation chain record.
     */
    public function createDelegationRecord(
        DocumentWorkflow $childWorkflow,
        DocumentWorkflow $parentWorkflow,
        string $delegationType,
        int $depthLevel
    ): void {
        WorkflowDelegationChain::create([
            'workflow_id' => $childWorkflow->id,
            'delegator_workflow_id' => $parentWorkflow->id,
            'delegator_user_id' => $parentWorkflow->recipient_id,
            'delegate_user_id' => $childWorkflow->recipient_id,
            'delegation_type' => $delegationType,
            'depth_level' => $depthLevel,
        ]);
    }

    /**
     * Get delegation depth warning message.
     */
    public function getDelegationWarning(int $depth): ?array
    {
        if ($depth < 2) {
            return null;
        }

        if ($depth === 2) {
            return [
                'level' => 'warning',
                'message' => 'This document has been delegated twice. Consider making a decision to avoid excessive forwarding.',
                'color' => 'amber',
            ];
        }

        // depth >= 3
        return [
            'level' => 'high',
            'message' => sprintf(
                'Multiple delegation levels detected (%d levels). All delegators will be notified of your decision.',
                $depth
            ),
            'color' => 'red',
        ];
    }

    /**
     * Check if a workflow should enforce terminal decision (approve/reject only).
     */
    public function shouldEnforceTerminalDecision(DocumentWorkflow $workflow): bool
    {
        // Must be received status and require terminal decision
        if ($workflow->status !== 'received' || !$workflow->requires_terminal_decision) {
            return false;
        }

        // Must be appropriate action workflow
        if ($workflow->purpose !== 'appropriate_action') {
            return false;
        }

        // All sub-workflows must be complete
        return $workflow->isAllSubWorkflowsComplete();
    }

    /**
     * Mark parent workflows as complete after terminal decision in delegation chain.
     */
    public function completeParentDelegations(DocumentWorkflow $terminalWorkflow): void
    {
        $current = $terminalWorkflow;

        while ($current->parent_workflow_id) {
            $parent = DocumentWorkflow::find($current->parent_workflow_id);
            if (!$parent) {
                break;
            }

            // If parent delegated authority, mark as complete
            if ($parent->delegation_type === 'delegate' && $parent->status === 'delegated') {
                $parent->status = 'forwarded'; // Mark as complete but keep delegation visible
                $parent->save();
                
                Log::info('Parent delegation completed', [
                    'parent_workflow_id' => $parent->id,
                    'terminal_workflow_id' => $terminalWorkflow->id,
                ]);
            }

            $current = $parent;
        }
    }
}
