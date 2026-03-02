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
        ]);

        $document     = $workflow->document;
        $user         = Auth::user();
        $oldRecipient = $workflow->recipientUser;
        $newRecipient = User::findOrFail($request->new_recipient_id);

        // Authorization: only the document uploader, company-admin, or super-admin can reroute
        if ($document->uploader !== $user->id &&
            !$user->hasRole('super-admin') &&
            !$user->hasRole('company-admin')) {
            return back()->with('error', 'You do not have permission to reroute this workflow step.');
        }

        // Prevent rerouting to the same person
        if ($workflow->recipient_id === $newRecipient->id) {
            return back()->with('error', 'Cannot reroute to the same person.');
        }

        DB::beginTransaction();
        try {
            // Log the reroute
            DB::table('workflow_reroute_logs')->insert([
                'workflow_id'        => $workflow->id,
                'document_id'        => $document->id,
                'old_recipient_id'   => $workflow->recipient_id,
                'new_recipient_id'   => $newRecipient->id,
                'rerouted_by'        => $user->id,
                'reason'             => $request->reason,
                'old_status'         => $workflow->status,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // Update the workflow
            $workflow->update([
                'recipient_id'          => $newRecipient->id,
                'status'                => 'pending',
                'is_rerouted'           => true,
                'last_activity_at'      => now(),
                'inactivity_notified_at' => null,
            ]);

            // Audit log
            DocumentAudit::logDocumentAction(
                $document->id,
                $user->id,
                'workflow_rerouted',
                'rerouted',
                "Workflow step rerouted from {$oldRecipient->full_name} to {$newRecipient->full_name}. Reason: {$request->reason}"
            );

            // Notify the new recipient
            $viewUrl = url("/documents/{$document->id}");

            Notifications::create([
                'user_id' => $newRecipient->id,
                'type'    => 'workflow_rerouted',
                'data'    => json_encode([
                    'document_id'    => $document->id,
                    'document_title' => $document->title,
                    'message'        => "A document \"{$document->title}\" has been rerouted to you by {$user->full_name}. Reason: {$request->reason}",
                    'rerouted_by'    => $user->full_name,
                    'urgency_level'  => $document->urgency_level,
                ]),
            ]);

            // Notify the old recipient
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

            // Send email to the new recipient
            if ($newRecipient->email) {
                try {
                    Mail::to($newRecipient->email)->send(new DocumentUrgencyAlert(
                        $document,
                        $workflow,
                        'reroute',
                        [
                            'old_recipient' => $oldRecipient->full_name ?? 'N/A',
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
                        'message'        => "Your document \"{$document->title}\" has been rerouted from {$oldRecipient->full_name} to {$newRecipient->full_name} by {$user->full_name}.",
                    ]),
                ]);
            }

            DB::commit();

            return back()->with('success', "Workflow step rerouted to {$newRecipient->full_name} successfully.");
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

        $companyId = $document->company_id;

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

        // Fallback: if company-based query returns empty, try all users in the system
        // (this handles cases where company_id is null or pivot table is unpopulated)
        if ($users->isEmpty()) {
            \Log::warning('Reroute: No company users found', [
                'document_id' => $document->id,
                'company_id'  => $companyId,
            ]);

            $users = User::where('id', '!=', $user->id)
                ->with('offices:id,name')
                ->select('id', 'first_name', 'last_name', 'email')
                ->orderBy('first_name')
                ->limit(100)
                ->get()
                ->map(fn($u) => [
                    'id'     => $u->id,
                    'name'   => $u->first_name . ' ' . $u->last_name,
                    'email'  => $u->email,
                    'office' => $u->offices->first()?->name ?? '',
                ]);
        }

        return response()->json($users);
    }
}
