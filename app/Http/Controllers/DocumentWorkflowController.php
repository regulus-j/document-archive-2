<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\DocumentWorkflow;
use App\Models\Document;
use App\Models\User;
use App\Models\CompanyUser;
use App\Models\DocumentAttachment;
use App\Models\DocumentAudit;
use App\Models\ESignature;
use App\Services\DocumentAccessService;
use Illuminate\Support\Facades\Storage;


class DocumentWorkflowController extends Controller
{
    protected $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
    }
    /**
     * Check if user can access workflow for a document
     * User must have "received" the document first in the receive view
     */
    private function canAccessWorkflow($workflowId, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        $workflow = DocumentWorkflow::find($workflowId);
        if (!$workflow) {
            return false;
        }
        
        // Check if document has been recalled
        if ($workflow->document && $workflow->document->status && $workflow->document->status->status === 'recalled') {
            return false;
        }
        
        // If user is the sender, they can always access (to monitor)
        if ($workflow->sender_id === $userId) {
            return true;
        }
        
        // If user is recipient, check access based on workflow type
        if ($workflow->recipient_id === $userId) {
            // For sequential workflows, check if it's their turn and auto-receive if needed
            if ($workflow->isSequential() && $workflow->status === 'pending') {
                // Auto-receive for sequential workflows and update status
                if ($workflow->received_at === null) {
                    $workflow->receive();
                    \Log::info('Auto-received sequential workflow for user', [
                        'workflow_id' => $workflowId,
                        'user_id' => $userId,
                        'document_id' => $workflow->document_id,
                        'step_order' => $workflow->step_order
                    ]);
                }
                return true;
            }
            
            // For sequential workflows that are waiting, deny access
            if ($workflow->isSequential() && $workflow->status === 'waiting') {
                \Log::info('User tried to access sequential workflow that is waiting', [
                    'workflow_id' => $workflowId,
                    'user_id' => $userId,
                    'step_order' => $workflow->step_order
                ]);
                return false;
            }
            
            // Normal access check for received workflows (parallel or sequential that has been received)
            return in_array($workflow->status, ['received', 'approved', 'rejected', 'returned', 'referred', 'forwarded', 'commented', 'acknowledged']);
        }
        
        return false;
    }
    
    /**
     * Middleware-like check for workflow access
     */
    private function ensureWorkflowAccess($workflowId)
    {
        $workflow = DocumentWorkflow::find($workflowId);
        
        // Check if document has been recalled
        if ($workflow && $workflow->document && $workflow->document->status && $workflow->document->status->status === 'recalled') {
            return redirect()->route('documents.workflows')
                ->with('error', 'This document has been recalled by the sender. Workflow actions are no longer available.');
        }
        
        if (!$this->canAccessWorkflow($workflowId)) {
            // Check if it's a sequential workflow that needs special handling
            if ($workflow && $workflow->isSequential() && $workflow->recipient_id === auth()->id()) {
                if ($workflow->status === 'pending') {
                    // This should have been auto-received in canAccessWorkflow, try again
                    $workflow->receive();
                    \Log::info('Manual receive attempt for sequential workflow', [
                        'workflow_id' => $workflowId,
                        'user_id' => auth()->id(),
                        'status' => $workflow->status
                    ]);
                    
                    // Check access again
                    if ($this->canAccessWorkflow($workflowId)) {
                        return null; // Access granted
                    }
                }
                
                if ($workflow->status === 'waiting') {
                    return redirect()->route('documents.workflows')
                        ->with('info', 'This document is in sequential workflow. Please wait for your turn to process it.');
                }
            }
            
            return redirect()->route('documents.receive.index')
                ->with('error', 'You must receive this document first before accessing the workflow. Please check the "Receive Documents" section.');
        }
        
        return null;
    }

    /**
     * After a sub-workflow (forwarded-from-review) completes, reactivate the parent workflow
     * so the original forwarder can continue processing in the main workflow.
     */
    private function handleSubWorkflowCompletion(DocumentWorkflow $completedWorkflow): void
    {
        // Only applies if this workflow is a sub-workflow
        if (!$completedWorkflow->parent_workflow_id) {
            return;
        }

        $parentWorkflow = DocumentWorkflow::find($completedWorkflow->parent_workflow_id);
        if (!$parentWorkflow) {
            return;
        }

        // Check if ALL child workflows of the parent are now completed
        $pendingChildren = DocumentWorkflow::where('parent_workflow_id', $parentWorkflow->id)
            ->whereIn('status', ['pending', 'received', 'waiting'])
            ->count();

        if ($pendingChildren > 0) {
            // Still waiting on other sub-workflow recipients
            return;
        }

        // All sub-workflows completed — reactivate the parent workflow
        $parentWorkflow->status = 'received';
        $parentWorkflow->save();

        \Log::info('Sub-workflow completed, reactivating parent workflow', [
            'completed_workflow_id' => $completedWorkflow->id,
            'parent_workflow_id' => $parentWorkflow->id,
            'parent_recipient_id' => $parentWorkflow->recipient_id,
        ]);

        // Collect results from child workflows for the notification
        $childResults = DocumentWorkflow::where('parent_workflow_id', $parentWorkflow->id)
            ->with('recipient')
            ->get()
            ->map(fn($w) => ($w->recipient ? $w->recipient->first_name . ' ' . $w->recipient->last_name : 'Unknown') . ': ' . ucfirst($w->status))
            ->implode(', ');

        // Notify the original forwarder that the sub-workflow is complete
        if ($parentWorkflow->recipient_id) {
            \App\Models\Notifications::create([
                'user_id' => $parentWorkflow->recipient_id,
                'type' => 'sub_workflow_completed',
                'data' => json_encode([
                    'document_id' => $parentWorkflow->document_id,
                    'message' => 'The document you forwarded for review has been completed. You can now continue processing.',
                    'title' => $parentWorkflow->document->title ?? 'Document',
                    'results' => $childResults,
                    'workflow_id' => $parentWorkflow->id,
                ]),
            ]);
        }

        // Log the return-to-parent action
        DocumentAudit::logDocumentAction(
            $parentWorkflow->document_id,
            $completedWorkflow->recipient_id ?? auth()->id(),
            'workflow',
            'sub_workflow_completed',
            'Sub-workflow completed by ' . (auth()->user()->first_name ?? '') . ' ' . (auth()->user()->last_name ?? '') .
            '. Document returned to ' . ($parentWorkflow->recipient ? $parentWorkflow->recipient->first_name . ' ' . $parentWorkflow->recipient->last_name : 'original reviewer') .
            ' for continued processing.'
        );
    }

    /**
     * Store e-signature if provided in the request (called from action methods)
     */
    private function handleInlineSignature(Request $request, DocumentWorkflow $workflow, string $action): void
    {
        if (!$request->has('signature_data') || empty($request->signature_data)) {
            return;
        }

        $user = auth()->user();
        $signatureData = $request->signature_data;
        $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureData = str_replace(' ', '+', $signatureData);
        $imageData = base64_decode($signatureData);

        $fileName = 'sig_' . $user->id . '_' . $workflow->id . '_' . time() . '.png';
        $companyId = $workflow->document->company_id ?? 'general';
        $signaturePath = $companyId . '/signatures/' . $fileName;

        Storage::disk('public')->put($signaturePath, $imageData);

        ESignature::create([
            'document_id' => $workflow->document_id,
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'action' => $action,
            'signature_path' => $signaturePath,
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'position' => $user->position ?? null,
            'ip_address' => $request->ip(),
            'signed_at' => now(),
        ]);
    }

    // workflow logic
    public function createWorkflow(Request $request): RedirectResponse
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'sender_id' => 'required|exists:users,id',
            'recipient_id' => 'required|exists:users,id',
            'step_order' => 'required|integer',
        ]);

        try {
            $workflow = DocumentWorkflow::create($request->only([
                'document_id',
                'sender_id',
                'recipient_id',
                'step_order',
            ]));

            DocumentAudit::logDocumentAction($workflow->document, 'workflow', 'pending', 'Document workflow created');

            return redirect()->back()->with('success', 'Document workflow created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating document workflow: ' . $e->getMessage());
        }
    }

    public function forwardDocumentSubmit(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $request->validate([
            'recipient_batch' => 'required|array',
            'recipient_batch.*' => 'required|string',
            'step_order' => 'required|array',
            'purpose_batch' => 'required|array',
            'purpose_batch.*' => 'required|string|in:appropriate_action,dissemination,for_comment',
            'urgency_batch' => 'nullable|array',
            'urgency_batch.*' => 'nullable|string|in:low,medium,high,critical',
            'due_date_batch' => 'nullable|array',
            'due_date_batch.*' => 'nullable|date|after_or_equal:today',
            'workflow_mode' => 'required|string|in:parallel,sequential',
        ]);

        // Ensure document from_office is set to the uploader's office if missing or mismatched
        $uploaderOffice = auth()->user()->offices->first();
        if (!$document->from_office && $uploaderOffice) {
            $document->from_office = $uploaderOffice->id;
            $document->save();
        }

        $document->status()->update(['status' => 'forwarded']);

        // Generate tracking number
        $trackingNumber = $this->createTrackingNumber($document, auth()->user());

        // Log the forward action
        DocumentAudit::logDocumentAction(
            $document->id,
            auth()->id(),
            'forward',
            'forwarded',
            'Document forwarded'
        );

        $recipientBatches = $request->recipient_batch ?? [];
        $workflowMode = $request->workflow_mode ?? 'parallel';
        $isSequential = $workflowMode === 'sequential';
        
        // Keep track of all recipient IDs to sync with document_recipients table
        $allRecipientIds = [];
        
        foreach ($recipientBatches as $batchIndex => $recipientValue) {
            if (empty($recipientValue)) {
                continue;
            }
            
            // Parse the recipient value to determine if it's an office or user
            // Format: "office_ID" or "user_ID"
            $parts = explode('_', $recipientValue);
            $type = $parts[0];
            $id = intval($parts[1]);
            
            // Determine status based on workflow mode and step order
            $stepOrder = intval($request->step_order[$batchIndex]);
            $status = 'pending'; // Default for parallel mode
            
            if ($isSequential) {
                // In sequential mode, only the first step is pending, others wait
                $status = ($stepOrder == 1) ? 'pending' : 'waiting';
                \Log::info('Sequential workflow step created', [
                    'step_order' => $stepOrder,
                    'status' => $status,
                    'recipient_type' => $type,
                    'recipient_id' => $id
                ]);
            }
            
            if ($type === 'user') {
                // It's a user recipient
                $recipientId = $id;
                $allRecipientIds[] = $recipientId;
                
                // Get the recipient user's office - use their first office, or fall back to the sender's office
                $user = \App\Models\User::with('offices')->find($recipientId);
                $senderOffice = auth()->user()->offices->first();
                $recipientOfficeId = $user && $user->offices->isNotEmpty()
                    ? $user->offices->first()->id
                    : ($senderOffice ? $senderOffice->id : null);
                
                DocumentWorkflow::create([
                    'tracking_number' => $trackingNumber,
                    'document_id' => $document->id,
                    'sender_id' => auth()->id(),
                    'recipient_id' => $recipientId,
                    'recipient_office' => $recipientOfficeId,
                    'step_order' => $stepOrder,
                    'workflow_type' => $workflowMode,
                    'remarks' => $request->remarks[$batchIndex] ?? null,
                    'status' => $status,
                    'received_at' => null,
                    'purpose' => $request->purpose_batch[$batchIndex] ?? null,
                    'urgency' => $request->urgency_batch[$batchIndex] ?? null,
                    'due_date' => $request->due_date_batch[$batchIndex] ?? null,
                ]);

                // Notify the user recipient (only if status is pending)
                if ($status === 'pending') {
                    \App\Models\Notifications::create([
                        'user_id' => $recipientId,
                        'type' => 'document_forwarded',
                        'data' => json_encode([
                            'document_id' => $document->id,
                            'message' => $isSequential ? 
                                'A document has been forwarded to you in sequential workflow.' : 
                                'A document has been forwarded to you.',
                            'title' => $document->title,
                            'sender' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                            'workflow_type' => $workflowMode,
                            'step_order' => $stepOrder,
                        ]),
                    ]);
                }
            } else if ($type === 'office') {
                // It's an office recipient - get all users in this office
                $officeId = $id;
                $office = \App\Models\Office::find($officeId);
                
                if ($office) {
                    $officeUsers = $office->users; // Get all users in this office
                    
                    foreach ($officeUsers as $user) {
                        $recipientId = $user->id;
                        $allRecipientIds[] = $recipientId; // Add to tracking array
                        
                        // Create workflow entry for each user in the office
                        DocumentWorkflow::create([
                            'tracking_number' => $trackingNumber,
                            'document_id' => $document->id,
                            'sender_id' => auth()->id(),
                            'recipient_id' => $recipientId,
                            'recipient_office' => $officeId,
                            'step_order' => $stepOrder,
                            'workflow_type' => $workflowMode,
                            'remarks' => $request->remarks[$batchIndex] ?? null,
                            'status' => $status,
                            'received_at' => null,
                            'purpose' => $request->purpose_batch[$batchIndex] ?? null,
                            'urgency' => $request->urgency_batch[$batchIndex] ?? null,
                            'due_date' => $request->due_date_batch[$batchIndex] ?? null,
                        ]);

                        // Notify each user in the office (only if status is pending)
                        if ($status === 'pending') {
                            \App\Models\Notifications::create([
                                'user_id' => $recipientId,
                                'type' => 'document_forwarded',
                                'data' => json_encode([
                                    'document_id' => $document->id,
                                    'message' => $isSequential ? 
                                        'A document has been forwarded to your office (' . $office->name . ') in sequential workflow.' : 
                                        'A document has been forwarded to your office (' . $office->name . ').',
                                    'title' => $document->title,
                                    'sender' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                                    'workflow_type' => $workflowMode,
                                    'step_order' => $stepOrder,
                                    'office_name' => $office->name,
                                ]),
                            ]);
                        }
                    }
                    
                    \Log::info('Office forwarding completed', [
                        'office_id' => $officeId,
                        'office_name' => $office->name,
                        'users_count' => $officeUsers->count(),
                        'step_order' => $stepOrder,
                        'status' => $status
                    ]);
                } else {
                    \Log::error('Office not found for forwarding', ['office_id' => $officeId]);
                }
            }
        }
        
        // Sync all recipient IDs with the document_recipients table to ensure proper recipient data
        if (!empty($allRecipientIds)) {
            foreach ($allRecipientIds as $recipientId) {
                \DB::table('document_recipients')->updateOrInsert(
                    ['document_id' => $document->id, 'recipient_id' => $recipientId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $docQR = $document->trackingNumber();
        $qrCodeData = app(DocumentController::class)->generateTrackingSlip($document->id, auth()->id(), $trackingNumber);
        
        $successMessage = $isSequential ? 
            'Document forwarded successfully with sequential workflow. Recipients will process in order.' :
            'Document forwarded successfully with parallel workflow.';
        
        // === Urgency Matrix: Analyze document urgency after forwarding (deferred) ===
        // Runs after the HTTP response so the user isn't blocked
        $docId = $document->id;
        app()->terminating(function () use ($docId) {
            try {
                $doc = \App\Models\Document::find($docId);
                if (!$doc) return;
                $urgencyAnalyzer = app(\App\Services\DocumentUrgencyAnalyzer::class);
                $urgencyResult = $urgencyAnalyzer->analyze($doc);
                \Log::info('Document urgency analyzed after forwarding (deferred)', [
                    'document_id' => $docId,
                    'level' => $urgencyResult['level'],
                    'confidence' => $urgencyResult['confidence'],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Urgency analysis failed (non-blocking)', ['document_id' => $docId, 'error' => $e->getMessage()]);
            }
        });
        // === End Urgency Matrix ===

        return redirect()->route('documents.index')
        ->with('data', $qrCodeData)
        ->with('success', $successMessage);
    }

    public function approveWorkflow(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $workflow = DocumentWorkflow::findOrFail($id);
        $workflow->approve();
        
        // Store e-signature if provided
        $this->handleInlineSignature($request, $workflow, 'approved');
        
        // Save remarks if provided
        if ($request->has('remarks') && !empty($request->remarks)) {
            $workflow->remarks = $request->remarks;
            $workflow->save();
        }

        // Log with remarks if provided
        $logMessage = 'Document workflow approved';
        if ($request->has('remarks') && !empty($request->remarks)) {
            $logMessage .= ': ' . $request->remarks;
        }
        
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'approved',
            $logMessage
        );

        // Handle sequential workflow progression
        $nextStepActivated = $this->activateNextSequentialStep($workflow);
        
        // For parallel workflows or when no next step was activated, use old notification logic
        if (!$nextStepActivated) {
            // Notify next recipient (if any) - for parallel workflows
            $nextWorkflow = \App\Models\DocumentWorkflow::where('document_id', $workflow->document_id)
                ->where('step_order', '>', $workflow->step_order)
                ->orderBy('step_order')
                ->first();
            if ($nextWorkflow && $nextWorkflow->recipient_id && $nextWorkflow->recipient_id != auth()->id()) {
                \App\Models\Notifications::create([
                    'user_id' => $nextWorkflow->recipient_id,
                    'type' => 'document_next_step',
                    'data' => json_encode([
                        'document_id' => $workflow->document_id,
                        'message' => 'A document is now assigned to you in the workflow.',
                        'title' => $workflow->document->title,
                        'from' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    ]),
                ]);
            }
            // Notify previous sender (if any)
            if ($workflow->sender_id && $workflow->sender_id != auth()->id()) {
                \App\Models\Notifications::create([
                    'user_id' => $workflow->sender_id,
                    'type' => 'document_next_step',
                    'data' => json_encode([
                        'document_id' => $workflow->document_id,
                        'message' => 'A document you forwarded has moved to the next step.',
                        'title' => $workflow->document->title,
                        'to' => $nextWorkflow && $nextWorkflow->recipient_id ? $nextWorkflow->recipient->first_name . ' ' . $nextWorkflow->recipient->last_name : null,
                    ]),
                ]);
            }
        }

        // If this was a sub-workflow, check if parent should be reactivated
        $this->handleSubWorkflowCompletion($workflow);

        return redirect()->route('documents.index')
            ->with('success', 'Document workflow approved');
    }

    public function rejectWorkflow(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $workflow = DocumentWorkflow::findOrFail($id);
        $workflow->reject();
        $workflow->remarks = $request->remarks;
        $workflow->save();

        // Store e-signature if provided
        $this->handleInlineSignature($request, $workflow, 'rejected');

        // Log with remarks
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'rejected',
            'Document rejected: ' . $request->remarks
        );

        // If this was a sub-workflow, check if parent should be reactivated
        $this->handleSubWorkflowCompletion($workflow);

        // Optional: Notify the sender
        if (class_exists('\App\Notifications\DocumentRejected')) {
            $document->sender->notify(new \App\Notifications\DocumentRejected($document, $workflow));
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document rejected. The sender has been notified to revise or cancel the workflow.');
    }

    // helper functions
    private function storeFile($file, $company_id)
    {
        Storage::disk('local')->put(date('mYd'), 'Contents');
    }

    // workflow management
    public function workflowManagement()
    {
        $currentUserId = auth()->id();
        
        // Show workflows where the user can take action
        // This includes:
        // 1. Workflows they've received (status != 'pending' for parallel)
        // 2. Sequential workflows that are pending and assigned to them
        // 3. Workflows where they are the sender (monitoring)
        $workflows = DocumentWorkflow::with(['document.status', 'sender', 'recipient'])
            ->where(function($query) use ($currentUserId) {
                $query->where('recipient_id', $currentUserId)
                      ->where(function($subQuery) {
                          // Include received workflows (parallel or sequential)
                          $subQuery->where('status', '!=', 'pending')
                                  ->where('status', '!=', 'waiting')
                                  // OR include sequential workflows that are pending (ready for action)
                                  ->orWhere(function($seqQuery) {
                                      $seqQuery->where('workflow_type', 'sequential')
                                              ->where('status', 'pending');
                                  });
                      });
            })
            ->orWhere(function($query) use ($currentUserId) {
                // Also show workflows where user is the sender (they can monitor progress)
                $query->where('sender_id', $currentUserId);
            })
            ->whereHas('document', function($query) {
                $query->whereHas('status', function($statusQuery) {
                    $statusQuery->where('status', '!=', 'recalled');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get pending documents that need to be received first (exclude recalled and sequential pending)
        // For sequential workflows, pending means ready to process, not pending receipt
        $pendingReceive = DocumentWorkflow::with(['document.status', 'sender'])
            ->where('recipient_id', $currentUserId)
            ->where('status', 'pending')
            ->where(function($query) {
                // Only include parallel workflows in pending receive
                // Sequential workflows with pending status are ready for action
                $query->where('workflow_type', '!=', 'sequential')
                      ->orWhereNull('workflow_type'); // Handle legacy workflows without type
            })
            ->whereHas('document', function($query) {
                $query->whereHas('status', function($statusQuery) {
                    $statusQuery->where('status', '!=', 'recalled');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('documents.workflow', compact('workflows', 'pendingReceive'));
    }

    public function receiveWorkflow($id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $workflow = DocumentWorkflow::findOrFail($id);
        
        // For sequential workflows that are already activated (pending), go directly to review
        if ($workflow->isSequential() && $workflow->status === 'pending' && $workflow->recipient_id === auth()->id()) {
            // Auto-receive if not already received
            if ($workflow->received_at === null) {
                $workflow->receive();
                \Log::info('Auto-received sequential workflow in receiveWorkflow', [
                    'workflow_id' => $id,
                    'user_id' => auth()->id()
                ]);
            }
            
            // Redirect to review page for processing
            return redirect()->route('documents.review', $workflow->id)
                ->with('success', 'Document is ready for your action.');
        }
        
        // For parallel workflows or other cases, use receive documents feature
        return redirect()->route('documents.receive.index')
            ->with('info', 'Please use the "Receive Documents" feature to receive documents first, then access them in the workflow.');
    }

    public function reviewDocument($id)
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $workflow = DocumentWorkflow::findOrFail($id);
        $document = $workflow->document;
        $document->load(['attachments.uploader', 'eSignatures.user']);
        
        // Check if user can view this document based on classification
        if (!$this->documentAccessService->canViewDocument($document)) {
            abort(403, 'Access Denied: You are not authorized to view this document based on its access level. Please contact your administrator if you believe this is an error.');
        }
        
        // Get company ID from document or authenticated user's first company
        $companyId = $document->company_id ?? null;
        
        // If document doesn't have company_id, try to get it from the authenticated user
        if (!$companyId) {
            $userCompany = CompanyUser::where('user_id', auth()->id())->first();
            $companyId = $userCompany ? $userCompany->company_id : null;
        }
        
        // Default to empty collection if no company found
        $companyUsers = collect();
        
        if ($companyId) {
            // Get users from the same company via the pivot table
            $companyUserIds = CompanyUser::where('company_id', $companyId)
                ->where('user_id', '!=', auth()->id())
                ->pluck('user_id');
                
            // Get the actual user objects
            $companyUsers = User::whereIn('id', $companyUserIds)->get();
        }

        return view('documents.review', compact('workflow', 'document', 'companyUsers'));
    }

    public function reviewSubmit(Request $request)
    {
        $validated = $request->validate([
            'workflow_id' => 'required|exists:document_workflows,id',
            'remark' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,odt,ods,odp,rtf,jpeg,png,jpg,gif,webp,bmp,svg|max:10240',
            'action' => 'required|in:approve,reject',
        ]);

        $workflow = DocumentWorkflow::findOrFail($validated['workflow_id']);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $attachmentName = time() . '_' . $attachment->getClientOriginalName();
                $attachmentPath = $attachment->storeAs('attachments', $attachmentName, 'public');
                DocumentAttachment::create([
                    'document_id' => $workflow->document->id,
                    'filename' => $attachmentName,
                    'path' => $attachmentPath,
                    'storage_size' => $attachment->getSize(),
                    'mime_type' => $attachment->getMimeType(),
                ]);
            }
        }

        $workflow->remarks = $validated['remark'] ?? '';
        $workflow->save();

        if ($validated['action'] === 'approve') {
            $workflow->approve();
            // Fix: Pass document ID instead of document object
            DocumentAudit::logDocumentAction(
                $workflow->document_id,
                auth()->id(),
                'review',
                'approved',
                'Document workflow approved during review' . ($validated['remark'] ? ": {$validated['remark']}" : '')
            );
        } else {
            $workflow->reject();
            // Fix: Pass document ID instead of document object
            DocumentAudit::logDocumentAction(
                $workflow->document_id,
                auth()->id(),
                'review',
                'rejected',
                'Document workflow rejected during review' . ($validated['remark'] ? ": {$validated['remark']}" : '')
            );
        }

        return redirect()->route('documents.show', $workflow->document_id)
            ->with('success', 'Review submitted successfully');
    }

    public function returnWorkflow(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $workflow = DocumentWorkflow::findOrFail($id);
        $workflow->return();
        $workflow->remarks = $request->remarks;
        $workflow->save();

        // Store e-signature if provided
        $this->handleInlineSignature($request, $workflow, 'returned');

        // Update document status to indicate it's returned to uploader
        $document = Document::findOrFail($workflow->document_id);
        $document->status()->update(['status' => 'returned']);

        // Log with remarks
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'returned',
            'Document returned to uploader: ' . $request->remarks
        );

        // If this was a sub-workflow, check if parent should be reactivated
        $this->handleSubWorkflowCompletion($workflow);

        return redirect()->route('documents.index')
            ->with('success', 'Document returned to uploader. The uploader will need to revise the document based on your remarks.');
    }

    public function referWorkflow(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $workflow = DocumentWorkflow::findOrFail($id);
        $document = Document::findOrFail($workflow->document_id);
        
        // Mark the current workflow as referred
        $workflow->refer();
        $workflow->remarks = $request->remarks ?? '';
        $workflow->save();
        
        // Generate tracking number for new workflow stages
        $trackingNumber = $this->createTrackingNumber($document, auth()->user());
        
        // Find the last step order used in existing workflow
        $lastStepOrder = DocumentWorkflow::where('document_id', $document->id)
            ->max('step_order');
            
        $newStepOrder = $lastStepOrder + 1;
        
        // Create new workflow entries for each additional recipient
        foreach ($request->recipients as $recipientId) {
            // Skip if trying to refer to self
            if ($recipientId == auth()->id()) {
                continue;
            }
            
            // Get the user's office ID
            $user = \App\Models\User::with('offices')->find($recipientId);
            $senderOffice = auth()->user()->offices->first();
            $recipientOfficeId = $user && $user->offices->isNotEmpty()
                ? $user->offices->first()->id
                : ($senderOffice ? $senderOffice->id : null);
            
            DocumentWorkflow::create([
                'tracking_number' => $trackingNumber,
                'document_id' => $document->id,
                'sender_id' => auth()->id(),
                'recipient_id' => $recipientId,
                'recipient_office' => $recipientOfficeId,
                'step_order' => $newStepOrder,
                'remarks' => $request->remarks ?? null,
                'status' => 'pending',
                'received_at' => null,
                'purpose' => $workflow->purpose,
                'workflow_type' => $workflow->workflow_type,
                'urgency' => $workflow->urgency,
                'due_date' => $workflow->due_date,
            ]);

            // Notify the referred user
            \App\Models\Notifications::create([
                'user_id' => $recipientId,
                'type' => 'document_referred',
                'data' => json_encode([
                    'document_id' => $document->id,
                    'message' => 'A document has been referred to you.',
                    'title' => $document->title,
                    'sender' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]),
            ]);
        }
        
        // Log action
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'referred',
            'Document referred to additional recipients: ' . ($request->remarks ? $request->remarks : 'No remarks')
        );

        return redirect()->route('documents.index')
            ->with('success', 'Document referred to additional recipients successfully.');
    }

    public function forwardFromWorkflow(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $workflow = DocumentWorkflow::findOrFail($id);
        $document = Document::findOrFail($workflow->document_id);
        
        // Keep the current workflow unchanged but mark as forwarded
        $workflow->forward();
        $workflow->remarks = $request->remarks ?? '';
        $workflow->save();
        
        // Generate tracking number for new workflow stages
        $trackingNumber = $this->createTrackingNumber($document, auth()->user());
        
        // Get the last step order for the document workflow
        $lastStepOrder = DocumentWorkflow::where('document_id', $document->id)
            ->max('step_order');
            
        $newStepOrder = $lastStepOrder + 1;
        
        // Create new workflow entries for each recipient
        foreach ($request->recipients as $recipientId) {
            // Skip if trying to forward to self
            if ($recipientId == auth()->id()) {
                continue;
            }
            
            // Get the user's office ID
            $user = \App\Models\User::with('offices')->find($recipientId);
            $senderOffice = auth()->user()->offices->first();
            $recipientOfficeId = $user && $user->offices->isNotEmpty()
                ? $user->offices->first()->id
                : ($senderOffice ? $senderOffice->id : null);
            
            DocumentWorkflow::create([
                'tracking_number' => $trackingNumber,
                'document_id' => $document->id,
                'sender_id' => auth()->id(),
                'recipient_id' => $recipientId,
                'recipient_office' => $recipientOfficeId,
                'step_order' => $newStepOrder,
                'remarks' => $request->remarks ?? null,
                'status' => 'pending',
                'received_at' => null,
                'purpose' => $workflow->purpose,
                'workflow_type' => $workflow->workflow_type,
                'urgency' => $workflow->urgency,
                'due_date' => $workflow->due_date,
                'parent_workflow_id' => $workflow->id,
            ]);

            // Notify the forwarded user
            \App\Models\Notifications::create([
                'user_id' => $recipientId,
                'type' => 'document_forwarded',
                'data' => json_encode([
                    'document_id' => $document->id,
                    'message' => 'A document has been forwarded to you.',
                    'title' => $document->title,
                    'sender' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]),
            ]);
        }
        
        // Log action
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'forwarded',
            'Document forwarded from review: ' . ($request->remarks ? $request->remarks : 'No remarks')
        );

        // === Urgency Matrix: Analyze document urgency after re-forwarding (deferred) ===
        $docId = $document->id;
        app()->terminating(function () use ($docId) {
            try {
                $doc = \App\Models\Document::find($docId);
                if (!$doc) return;
                $urgencyAnalyzer = app(\App\Services\DocumentUrgencyAnalyzer::class);
                $urgencyResult = $urgencyAnalyzer->analyze($doc);
                \Log::info('Document urgency analyzed after re-forwarding (deferred)', [
                    'document_id' => $docId,
                    'level' => $urgencyResult['level'],
                    'confidence' => $urgencyResult['confidence'],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Urgency analysis failed (non-blocking)', ['document_id' => $docId, 'error' => $e->getMessage()]);
            }
        });
        // === End Urgency Matrix ===

        return redirect()->route('documents.index')
            ->with('success', 'Document forwarded to new recipients successfully.');
    }

    private function createTrackingNumber(Document $document, $user)
    {
        $originatingOffice = $user->originating_office ?? 'UNK';
        $officeCode = strtoupper(substr($originatingOffice, 0, 3));
        $uploadDate = $document->created_at
            ? $document->created_at->format('Ymd')
            : date('Ymd');
        $numberPart = str_pad($document->id, 6, '0', STR_PAD_LEFT);

        return "{$officeCode}-{$uploadDate}-{$numberPart}";
    }

    // Method to handle receipt confirmation
    public function confirmReceipt(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
        ]);

        $document = Document::findOrFail($request->input('document_id'));

        // Update the workflow status to 'received'
        $workflow = $document->workflow;
        if ($workflow) {
            $workflow->status = 'received';
            $workflow->save();
        }

        return redirect()->route('documents.receive.index')->with('success', 'Document status updated to received.');
    }

    /**
     * Add comment to workflow (for 'for_comment' purpose)
     */
    public function addComment(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);
        
        $workflow = DocumentWorkflow::findOrFail($id);
        
        // Ensure this is a comment purpose workflow
        if ($workflow->purpose !== 'for_comment') {
            return redirect()->back()->with('error', 'This action is only available for documents requesting comments.');
        }
        
        // Update workflow with comment
        $workflow->status = 'commented';
        $workflow->remarks = $request->remarks;
        $workflow->received_at = now();
        $workflow->save();
        
        // Store e-signature if provided
        $this->handleInlineSignature($request, $workflow, 'commented');
        
        // Handle sequential workflow progression for comments
        $nextStepActivated = $this->activateNextSequentialStep($workflow);
        
        // Update document status - but don't mark as "commented" if sequential workflow continues
        if ($workflow->document && $workflow->document->status) {
            if (!$nextStepActivated) {
                // Only update to "commented" if this is the final step or parallel workflow
                $workflow->document->status()->update(['status' => 'commented']);
            }
            // If next step was activated, let syncDocumentStatus handle the status
        }
        
        // Log the action
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'commented',
            'Comment added: ' . $request->remarks
        );

        // If this was a sub-workflow, check if parent should be reactivated
        $this->handleSubWorkflowCompletion($workflow);

        // Notify sender about the comment
        if ($workflow->sender_id && $workflow->sender_id != auth()->id()) {
            \App\Models\Notifications::create([
                'user_id' => $workflow->sender_id,
                'title' => 'Document Comment Received',
                'message' => 'A comment has been added to document: ' . $workflow->document->title,
                'type' => 'workflow_comment',
                'data' => json_encode([
                    'document_id' => $workflow->document_id,
                    'workflow_id' => $workflow->id,
                    'commenter' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'comment' => $request->remarks
                ])
            ]);
        }

        return redirect()->route('documents.workflows')->with('success', 'Comment submitted successfully.');
    }

    /**
     * Acknowledge workflow (for 'dissemination' purpose)
     */
    public function acknowledgeWorkflow(Request $request, $id): RedirectResponse
    {
        // Check if user can access this workflow
        $accessCheck = $this->ensureWorkflowAccess($id);
        if ($accessCheck) return $accessCheck;
        
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);
        
        $workflow = DocumentWorkflow::findOrFail($id);
        
        // Ensure this is a dissemination purpose workflow
        if ($workflow->purpose !== 'dissemination') {
            return redirect()->back()->with('error', 'This action is only available for information dissemination documents.');
        }
        
        // Update workflow with acknowledgment
        $workflow->status = 'acknowledged';
        if ($request->remarks) {
            $workflow->remarks = $request->remarks;
        }
        $workflow->received_at = now();
        $workflow->save();
        
        // Store e-signature if provided
        $this->handleInlineSignature($request, $workflow, 'acknowledged');
        
        // Handle sequential workflow progression for acknowledgments
        $nextStepActivated = $this->activateNextSequentialStep($workflow);
        
        // Update document status - but don't mark as "acknowledged" if sequential workflow continues
        if ($workflow->document && $workflow->document->status) {
            if (!$nextStepActivated) {
                // Only update to "acknowledged" if this is the final step or parallel workflow
                $workflow->document->status()->update(['status' => 'acknowledged']);
            }
            // If next step was activated, let syncDocumentStatus handle the status
        }
        
        // Log the action
        $logMessage = 'Document information acknowledged';
        if ($request->remarks) {
            $logMessage .= ': ' . $request->remarks;
        }
        
        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            auth()->id(),
            'workflow',
            'acknowledged',
            $logMessage
        );

        // If this was a sub-workflow, check if parent should be reactivated
        $this->handleSubWorkflowCompletion($workflow);

        // Notify sender about the acknowledgment
        if ($workflow->sender_id && $workflow->sender_id != auth()->id()) {
            \App\Models\Notifications::create([
                'user_id' => $workflow->sender_id,
                'title' => 'Document Information Acknowledged',
                'message' => 'Document information has been acknowledged: ' . $workflow->document->title,
                'type' => 'workflow_acknowledged',
                'data' => json_encode([
                    'document_id' => $workflow->document_id,
                    'workflow_id' => $workflow->id,
                    'acknowledger' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'remarks' => $request->remarks
                ])
            ]);
        }

        return redirect()->route('documents.workflows')->with('success', 'Document information acknowledged successfully.');
    }

    /**
     * Activate the next step in sequential workflow
     */
    private function activateNextSequentialStep(DocumentWorkflow $currentWorkflow)
    {
        // Only process if current workflow is sequential
        if (!$currentWorkflow->isSequential()) {
            \Log::info('Workflow is not sequential, skipping next step activation', [
                'workflow_id' => $currentWorkflow->id,
                'workflow_type' => $currentWorkflow->workflow_type
            ]);
            return false;
        }

        \Log::info('Attempting to activate next sequential step', [
            'current_workflow_id' => $currentWorkflow->id,
            'current_step_order' => $currentWorkflow->step_order,
            'document_id' => $currentWorkflow->document_id,
            'current_status' => $currentWorkflow->status
        ]);

        // Find the next step in the sequence
        $nextStep = DocumentWorkflow::where('document_id', $currentWorkflow->document_id)
            ->where('workflow_type', 'sequential')
            ->where('step_order', $currentWorkflow->step_order + 1)
            ->where('status', 'waiting')
            ->first();

        if ($nextStep) {
            \Log::info('Found next sequential step, activating it', [
                'next_workflow_id' => $nextStep->id,
                'next_step_order' => $nextStep->step_order,
                'next_recipient_id' => $nextStep->recipient_id
            ]);

            // Activate the next step
            $nextStep->status = 'pending';
            $nextStep->save();

            // Send notification to the next recipient
            if ($nextStep->recipient_id) {
                \App\Models\Notifications::create([
                    'user_id' => $nextStep->recipient_id,
                    'type' => 'document_sequential_next',
                    'data' => json_encode([
                        'document_id' => $currentWorkflow->document_id,
                        'message' => 'A document is now ready for your action in sequential workflow.',
                        'title' => $currentWorkflow->document->title,
                        'step_order' => $nextStep->step_order,
                        'previous_step_completed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'workflow_type' => 'sequential',
                        'previous_action' => $currentWorkflow->status, // Include what action was taken
                    ]),
                ]);
            }

            // Also notify the sender that the workflow progressed
            if ($currentWorkflow->sender_id && $currentWorkflow->sender_id != auth()->id()) {
                $actionText = $currentWorkflow->status === 'commented' ? 'commented on' : 'completed';
                \App\Models\Notifications::create([
                    'user_id' => $currentWorkflow->sender_id,
                    'type' => 'document_sequential_progress',
                    'data' => json_encode([
                        'document_id' => $currentWorkflow->document_id,
                        'message' => "Sequential workflow has progressed to the next step after being {$actionText}.",
                        'title' => $currentWorkflow->document->title,
                        'completed_step' => $currentWorkflow->step_order,
                        'next_step' => $nextStep->step_order,
                        'completed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'action_taken' => $currentWorkflow->status,
                        'next_recipient' => $nextStep->recipient ? 
                            $nextStep->recipient->first_name . ' ' . $nextStep->recipient->last_name : 
                            ($nextStep->recipientOffice ? 'Office: ' . $nextStep->recipientOffice->name : 'Unknown Office'),
                    ]),
                ]);
            }

            \Log::info('Successfully activated next sequential step', [
                'next_workflow_id' => $nextStep->id,
                'next_step_order' => $nextStep->step_order
            ]);

            return true;
        }

        \Log::info('No next sequential step found', [
            'current_step_order' => $currentWorkflow->step_order,
            'looking_for_step_order' => $currentWorkflow->step_order + 1,
            'document_id' => $currentWorkflow->document_id
        ]);

        return false;
    }

    /**
     * Preview/view a document file inline in the browser
     */
    public function previewDocument($id)
    {
        $document = Document::findOrFail($id);

        if (!$this->documentAccessService->canViewDocument($document)) {
            abort(403, 'Access denied.');
        }

        $filePath = storage_path('app/public/' . $document->path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->path) . '"',
        ]);
    }

    /**
     * Preview/view an attachment file inline in the browser
     */
    public function previewAttachment($id)
    {
        $attachment = DocumentAttachment::findOrFail($id);
        $document = $attachment->document;

        if (!$this->documentAccessService->canViewDocument($document)) {
            abort(403, 'Access denied.');
        }

        $filePath = storage_path('app/public/' . $attachment->path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        $mimeType = $attachment->mime_type ?? mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $attachment->filename . '"',
        ]);
    }

    /**
     * Store e-signature for a workflow action
     */
    public function storeSignature(Request $request, $workflowId)
    {
        $accessCheck = $this->ensureWorkflowAccess($workflowId);
        if ($accessCheck) return $accessCheck;

        $request->validate([
            'signature_data' => 'required|string', // base64 image data
            'action' => 'required|in:approved,rejected,acknowledged,commented,returned',
        ]);

        $workflow = DocumentWorkflow::findOrFail($workflowId);
        $user = auth()->user();

        // Decode and store the signature image
        $signatureData = $request->signature_data;
        $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureData = str_replace(' ', '+', $signatureData);
        $imageData = base64_decode($signatureData);

        $fileName = 'sig_' . $user->id . '_' . $workflow->id . '_' . time() . '.png';
        $companyId = $workflow->document->company_id ?? 'general';
        $signaturePath = $companyId . '/signatures/' . $fileName;

        Storage::disk('public')->put($signaturePath, $imageData);

        $signature = ESignature::create([
            'document_id' => $workflow->document_id,
            'workflow_id' => $workflow->id,
            'user_id' => $user->id,
            'action' => $request->action,
            'signature_path' => $signaturePath,
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'position' => $user->position ?? null,
            'ip_address' => $request->ip(),
            'signed_at' => now(),
        ]);

        DocumentAudit::logDocumentAction(
            $workflow->document_id,
            $user->id,
            'signature',
            $request->action,
            'E-signature applied: ' . $request->action . ' by ' . $signature->full_name
        );

        return response()->json([
            'success' => true,
            'signature_id' => $signature->id,
            'message' => 'Signature saved successfully.',
        ]);
    }

    /**
     * Upload attachment from processor during workflow review
     */
    public function uploadProcessorAttachment(Request $request, $workflowId)
    {
        $accessCheck = $this->ensureWorkflowAccess($workflowId);
        if ($accessCheck) return $accessCheck;

        $request->validate([
            'attachments' => 'required|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,odt,ods,odp,rtf,jpeg,png,jpg,gif,webp,bmp,svg|max:10240',
        ]);

        $workflow = DocumentWorkflow::findOrFail($workflowId);
        $document = $workflow->document;
        $user = auth()->user();
        $companyId = $document->company_id ?? 'general';

        $uploaded = [];
        foreach ($request->file('attachments') as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($companyId . '/attachments', $fileName, 'public');

            $attachment = DocumentAttachment::create([
                'document_id' => $document->id,
                'filename' => $file->getClientOriginalName(),
                'path' => $filePath,
                'route_id' => $workflow->id,
                'storage_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $uploaded[] = $attachment;
        }

        DocumentAudit::logDocumentAction(
            $document->id,
            $user->id,
            'attachment',
            'uploaded',
            count($uploaded) . ' attachment(s) uploaded during workflow review by ' . $user->first_name . ' ' . $user->last_name
        );

        return redirect()->back()->with('success', count($uploaded) . ' attachment(s) uploaded successfully.');
    }
}
