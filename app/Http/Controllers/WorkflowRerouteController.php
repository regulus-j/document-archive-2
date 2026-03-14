<?php

namespace App\Http\Controllers;

use App\Mail\DocumentUrgencyAlert;
use App\Models\Document;
use App\Models\DocumentAudit;
use App\Models\DocumentWorkflow;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WorkflowRerouteController extends Controller
{
    /**
     * Reroute a workflow step to a different user.
     *
     * POST /documents/workflows/{workflow}/reroute
     */
    public function reroute(Request $request, DocumentWorkflow $workflow)
    {
        $request->validate([
            'new_recipient_id' => 'required|exists:users,id',
            'reason'           => 'required|string|max:500',
            'apply_to_step'    => 'nullable|boolean',
            'workflow_ids'     => 'nullable|string',
        ]);

        $terminalStatuses = ['approved', 'rejected', 'returned', 'acknowledged', 'commented', 'forwarded'];
        if (in_array($workflow->status, $terminalStatuses, true)) {
            return back()->with('error', 'This workflow step is already completed and can no longer be rerouted.');
        }

        $document     = $workflow->document;
        $user         = Auth::user();
        $newRecipient = User::findOrFail($request->new_recipient_id);
        $applyToStep  = $request->boolean('apply_to_step');

        // Authorization: only the document uploader, company-admin, or super-admin can reroute
        if ($document->uploader !== $user->id &&
            !$user->hasRole('super-admin') &&
            !$user->hasRole('company-admin')) {
            return back()->with('error', 'You do not have permission to reroute this workflow step.');
        }

        $targetWorkflows = collect([$workflow]);

        if ($applyToStep) {
            $requestedIds = collect(explode(',', (string) $request->input('workflow_ids', '')))
                ->map(fn($id) => (int) trim($id))
                ->filter(fn($id) => $id > 0)
                ->values();

            $targetWorkflows = DocumentWorkflow::where('document_id', $workflow->document_id)
                ->where('step_order', $workflow->step_order)
                ->where('workflow_type', $workflow->workflow_type)
                ->where('purpose', $workflow->purpose)
                ->whereIn('status', ['received', 'pending', 'waiting'])
                ->when($workflow->parent_workflow_id, function ($query) use ($workflow) {
                    $query->where('parent_workflow_id', $workflow->parent_workflow_id);
                }, function ($query) {
                    $query->whereNull('parent_workflow_id');
                })
                ->when($requestedIds->isNotEmpty(), function ($query) use ($requestedIds) {
                    $query->whereIn('id', $requestedIds->all());
                })
                ->get();

            if ($targetWorkflows->isEmpty()) {
                return back()->with('error', 'No active recipients were found in this step to reroute.');
            }
        }

        $alreadyAssignedCount = $targetWorkflows->where('recipient_id', $newRecipient->id)->count();
        if ($alreadyAssignedCount === $targetWorkflows->count()) {
            return back()->with('error', 'Cannot reroute: all selected recipients are already assigned to that user.');
        }

        DB::beginTransaction();
        try {
            $viewUrl = url("/documents/{$document->id}");
            $reroutedCount = 0;
            $oldRecipientNames = [];

            foreach ($targetWorkflows as $targetWorkflow) {
                // Prevent rerouting to the same person for each selected workflow.
                if ((int) $targetWorkflow->recipient_id === (int) $newRecipient->id) {
                    continue;
                }

                $oldRecipient = $targetWorkflow->recipientUser;
                $oldRecipientNames[] = $oldRecipient->full_name ?? 'N/A';

                DB::table('workflow_reroute_logs')->insert([
                    'workflow_id'        => $targetWorkflow->id,
                    'document_id'        => $document->id,
                    'old_recipient_id'   => $targetWorkflow->recipient_id,
                    'new_recipient_id'   => $newRecipient->id,
                    'rerouted_by'        => $user->id,
                    'reason'             => $request->reason,
                    'old_status'         => $targetWorkflow->status,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                $targetWorkflow->update([
                    'recipient_id'           => $newRecipient->id,
                    'status'                 => 'pending',
                    'is_rerouted'            => true,
                    'last_activity_at'       => now(),
                    'inactivity_notified_at' => null,
                ]);

                DocumentAudit::logDocumentAction(
                    $document->id,
                    $user->id,
                    'workflow_rerouted',
                    'rerouted',
                    "Workflow step rerouted from " . ($oldRecipient->full_name ?? 'N/A') . " to {$newRecipient->full_name}. Reason: {$request->reason}"
                );

                if ($oldRecipient) {
                    Notifications::create([
                        'user_id' => $oldRecipient->id,
                        'type'    => 'workflow_rerouted_from',
                        'data'    => json_encode([
                            'document_id'    => $document->id,
                            'document_title' => $document->title,
                            'message'        => "Document \"{$document->title}\" has been rerouted from you to {$newRecipient->full_name} by {$user->full_name}.",
                        ]),
                    ]);
                }

                $reroutedCount++;
            }

            if ($reroutedCount === 0) {
                DB::rollBack();
                return back()->with('error', 'No workflows were rerouted. Selected recipients may already match the new recipient.');
            }

            Notifications::create([
                'user_id' => $newRecipient->id,
                'type'    => 'workflow_rerouted',
                'data'    => json_encode([
                    'document_id'    => $document->id,
                    'document_title' => $document->title,
                    'message'        => $reroutedCount > 1
                        ? "{$reroutedCount} workflow recipients for \"{$document->title}\" have been rerouted to you by {$user->full_name}. Reason: {$request->reason}"
                        : "A document \"{$document->title}\" has been rerouted to you by {$user->full_name}. Reason: {$request->reason}",
                    'rerouted_by'    => $user->full_name,
                    'urgency_level'  => $document->urgency_level,
                ]),
            ]);

            if ($newRecipient->email) {
                try {
                    Mail::to($newRecipient->email)->send(new DocumentUrgencyAlert(
                        $document,
                        $workflow,
                        'reroute',
                        [
                            'old_recipient' => implode(', ', array_unique($oldRecipientNames)),
                            'new_recipient' => $newRecipient->full_name,
                            'reason'        => $request->reason,
                            'rerouted_by'   => $user->full_name,
                            'view_url'      => $viewUrl,
                        ]
                    ));
                } catch (\Throwable $e) {
                    Log::warning("Failed to send reroute email: {$e->getMessage()}");
                }
            }

            // Notify document uploader if they're not the one doing the reroute
            $uploader = User::find($document->uploader);
            if ($uploader && $uploader->id !== $user->id) {
                Notifications::create([
                    'user_id' => $uploader->id,
                    'type'    => 'workflow_rerouted_notification',
                    'data'    => json_encode([
                        'document_id'    => $document->id,
                        'document_title' => $document->title,
                        'message'        => $reroutedCount > 1
                            ? "Your document \"{$document->title}\" had {$reroutedCount} recipients rerouted to {$newRecipient->full_name} by {$user->full_name}."
                            : "Your document \"{$document->title}\" has been rerouted to {$newRecipient->full_name} by {$user->full_name}.",
                    ]),
                ]);
            }

            DB::commit();

            return back()->with('success', $reroutedCount > 1
                ? "{$reroutedCount} workflow recipients rerouted to {$newRecipient->full_name} successfully."
                : "Workflow step rerouted to {$newRecipient->full_name} successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Workflow reroute failed', ['error' => $e->getMessage(), 'workflow_id' => $workflow->id]);
            return back()->with('error', 'Failed to reroute workflow step. Please try again.');
        }
    }

    /**
     * Get available recipients for rerouting a document workflow.
     *
     * GET /documents/workflows/{document}/reroute-recipients
     */
    public function getAvailableRecipients(Document $document)
    {
        $user = Auth::user();

        // Resolve company ID from the document first, then fall back to the
        // authenticated user's own company — never expose other companies' users.
        $companyId = $document->company_id;

        if (!$companyId) {
            $companyId = $user->companies()->first()?->id;
        }

        if (!$companyId) {
            Log::warning('Reroute: Could not determine company for document', [
                'document_id' => $document->id,
                'user_id'     => $user->id,
            ]);
            return response()->json([]);
        }

        $users = User::whereHas('companies', fn($q) => $q->where('company_accounts.id', $companyId))
            ->where('id', '!=', $user->id)
            ->with('offices:id,name')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(fn($u) => [
                'id'     => $u->id,
                'name'   => $u->first_name . ' ' . $u->last_name,
                'email'  => $u->email,
                'office' => $u->offices->first()?->name ?? '',
            ]);

        if ($users->isEmpty()) {
            Log::warning('Reroute: No company users found for rerouting', [
                'document_id' => $document->id,
                'company_id'  => $companyId,
            ]);
        }

        return response()->json($users);
    }
}
