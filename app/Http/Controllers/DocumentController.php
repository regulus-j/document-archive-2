<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\DocumentAudit;
use App\Models\DocumentCategory;

use App\Models\DocumentTrackingNumber;
use App\Models\DocumentTransaction;
use App\Models\DocumentWorkflow;
use App\Models\CompanyAccount;
use App\Models\DocumentCategories;
use App\Models\Office;
use App\Models\User;
use App\Services\DocumentAccessService;
use chillerlan\QRCode\QRCode;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;


class DocumentController extends Controller
{
    protected $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
    }
    // B-09 FIX: Removed commented-out duplicate __construct() block that contained
    // permission middleware. Access control is now handled via DocumentAccessService
    // and policies. The commented block was confusing and implied it was still active.

    /**
     * Display a listing of the documents.
     */
    public function index(Request $request): View
    {
        // Determine active tab: 'active' (default) or 'archived'
        $tab = $request->query('tab', 'active');

        // Use the access service to get documents the user can view
        $query = $this->documentAccessService->getAccessibleDocuments()
            ->with([
                'user.offices',
                'status',
                'transaction.fromOffice',
                'transaction.toOffice',
                'categories',
                'documentWorkflow.recipient',
                'documentWorkflow.recipientOffice',
            ]);

        // Scope to tab: archived tab shows only archived, active tab excludes archived
        if ($tab === 'archived') {
            $query->whereHas('status', fn($q) => $q->where('status', 'archived'));
        } else {
            $query->whereHas('status', fn($q) => $q->where('status', '!=', 'archived'));
        }

        // Get the user's company ID
        $userCompany = auth()->user()->companies()->first();
        $offices = collect();

        // If user is company admin, get all offices in their company for the filter dropdown
        if (auth()->user()->hasRole('company-admin') && $userCompany) {
            $offices = Office::where('company_id', $userCompany->id)->orderBy('name')->get();

            // Apply office filter if selected
            if ($request->has('office_id') && $request->office_id !== 'all') {
                $query->where(function($q) use ($request) {
                    $q->whereHas('user.offices', function($officeQuery) use ($request) {
                        $officeQuery->where('offices.id', $request->office_id);
                    })->orWhereHas('transaction', function($transQuery) use ($request) {
                        $transQuery->where('from_office', $request->office_id)
                            ->orWhere('to_office', $request->office_id);
                    });
                });
            }
        }

        // Apply status filtering to the main query if requested
        if ($request->has('status')) {
            $status = strtolower($request->status);
            if ($status === 'approved') {
                $query->whereHas('status', fn($q) => $q->whereIn('status', ['approved', 'complete']));
            } elseif ($status === 'acknowledged') {
                $query->whereHas('status', fn($q) => $q->whereIn('status', ['acknowledged', 'acknowledge']));
            } else {
                $query->whereHas('status', fn($q) => $q->where('status', $status));
            }
        }

        // ── Date range filter ──
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ── User (uploader) filter ──
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // ── Category filter ──
        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            $query->where(function ($q) use ($catId) {
                $q->where('category', $catId)
                  ->orWhereHas('categories', fn($cq) => $cq->where('document_categories.id', $catId));
            });
        }

        // ── Team (office) filter ──
        if ($request->filled('team_id')) {
            $teamId = $request->team_id;
            $query->where(function ($q) use ($teamId) {
                $q->whereHas('user.offices', fn($oq) => $oq->where('offices.id', $teamId))
                  ->orWhereHas('transaction', fn($tq) => $tq->where('from_office', $teamId)->orWhere('to_office', $teamId));
            });
        }

        $documents = $query->latest()->paginate(5);

        $auditLogs = DocumentAudit::latest()->paginate(15);

        // Determine the recipients for each document.
        // documentWorkflow (with recipient + recipientOffice) is already eager-loaded above —
        // no additional queries are fired here.
        $documentRecipients = [];
        foreach ($documents as $doc) {
            $recipients = collect();

            foreach ($doc->documentWorkflow as $workflow) {
                // Add user recipients
                if ($workflow->recipient) {
                    $name = trim($workflow->recipient->first_name . ' ' . $workflow->recipient->last_name);
                    $recipients->push([
                        'name' => $name,
                        'type' => 'user',
                        'step_order' => $workflow->step_order,
                    ]);
                }

                // Add office recipients
                if ($workflow->recipient_office && $workflow->recipientOffice) {
                    $recipients->push([
                        'name' => $workflow->recipientOffice->name,
                        'type' => 'office',
                        'step_order' => $workflow->step_order,
                    ]);
                }
            }

            $documentRecipients[$doc->id] = $recipients;
        }

        // Add the selected office ID to pass to the view
        $selectedOfficeId = $request->input('office_id', 'all');

        // Count archived documents for the tab badge
        $archivedCount = $this->documentAccessService->getAccessibleDocuments()
            ->whereHas('status', fn($q) => $q->where('status', 'archived'))
            ->count();

        // Count non-archived documents for the active tab badge
        $activeDocCount = $this->documentAccessService->getAccessibleDocuments()
            ->whereHas('status', fn($q) => $q->where('status', '!=', 'archived'))
            ->count();

        // Fetch users, teams and categories for filter dropdowns
        if ($userCompany) {
            $filterUsers = User::whereHas('companies', fn($q) => $q->where('company_accounts.id', $userCompany->id))
                ->orderBy('first_name')->get();
            $filterCategories = DocumentCategory::where(function ($q) use ($userCompany) {
                $q->where('company_id', $userCompany->id)
                  ->orWhere('is_global', true)
                  ->orWhereNull('company_id');
            })->orderBy('category')->get();
            $filterTeams = Office::where('company_id', $userCompany->id)->orderBy('name')->get();
        } else {
            $filterUsers = collect();
            $filterCategories = DocumentCategory::orderBy('category')->get();
            $filterTeams = collect();
        }

        return view('documents.index', compact(
            'documents', 'auditLogs', 'documentRecipients', 'offices', 'selectedOfficeId',
            'filterUsers', 'filterCategories', 'filterTeams', 'tab', 'archivedCount', 'activeDocCount'
        ));
    }

    /**
     * Display pending documents (sent but not received, or received but not actioned)
     */
    public function showPending(Request $request): View
    {
        $currentUserId = auth()->id();
        $tab = $request->query('tab', 'received'); // Default to received tab

        // Base query with common relations
        $baseQuery = Document::with(['user', 'status', 'transaction.fromOffice', 'transaction.toOffice', 'documentWorkflow']);

        if ($tab === 'received') {
            // Get documents that have been received but not yet actioned by current user
            $documents = $baseQuery->whereHas('documentWorkflow', function($query) use ($currentUserId) {
                $query->where('recipient_id', $currentUserId)
                      ->where('status', 'received');
            });
        } else {
            // Get documents sent by current user that have been received but not yet actioned by recipients
            $documents = $baseQuery->whereHas('documentWorkflow', function($query) use ($currentUserId) {
                $query->where('sender_id', $currentUserId)
                      ->where('status', 'received');
            });
        }

        // Common status filter
        $documents = $documents->whereHas('status', function($q) {
                $q->whereNotIn('status', ['complete', 'archived', 'recalled']);
            })
            ->latest()
            ->paginate(10);

        // Get document recipients for display
        $documentRecipients = [];
        foreach ($documents as $doc) {
            $documentRecipients[$doc->id] = $doc->documentWorkflow()
                ->with(['recipient:id,first_name,last_name', 'sender:id,first_name,last_name'])
                ->get()
                ->map(function ($workflow) {
                    return [
                        'name' => optional($workflow->recipient)->first_name . ' ' . optional($workflow->recipient)->last_name,
                        'sender' => optional($workflow->sender)->first_name . ' ' . optional($workflow->sender)->last_name,
                        'received_at' => $workflow->received_at,
                        'received' => $workflow->status === 'received',
                        'purpose' => $workflow->purpose ?? null
                    ];
                });
        }        return view('documents.pending', compact('documents', 'documentRecipients', 'tab'));
    }

    public function showArchive(Request $request): View
    {
        $search = $request->input('search');
        $user = auth()->user();
        $userCompany = $user->companies()->first();

        if (!$userCompany) {
            return view('documents.archive', [
                'documents' => collect(),
                'search' => $search,
            ])->with('error', 'No company associated with your account.');
        }

        $query = Document::with(['user', 'status', 'transaction.fromOffice', 'transaction.toOffice'])
            ->whereHas('status', function ($q) {
                $q->where('status', 'archived');
            })
            ->whereHas('user.companies', function($q) use ($userCompany) {
                $q->where('company_accounts.id', $userCompany->id);
            });

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // For non-company admins, further restrict to only their office's documents
        if (!$user->hasRole('company-admin')) {
            $userOfficeIds = $user->offices->pluck('id')->toArray();
            $query->whereHas('user.offices', function ($q) use ($userOfficeIds) {
                $q->whereIn('offices.id', $userOfficeIds);
            });
        }

        $documents = $query->latest()->paginate(5);

        // B-08 FIX: scope audit logs to documents in the current company only, not all companies.
        $companyDocumentIds = Document::whereHas('user.companies', function ($q) use ($userCompany) {
            $q->where('company_accounts.id', $userCompany->id);
        })->pluck('id');

        $auditLogs = DocumentAudit::whereIn('document_id', $companyDocumentIds)->latest()->paginate(15);

        return view('documents.archive', array_merge([
            'documents' => $documents,
            'archivedDocuments' => $documents,
            'i' => (request()->input('page', 1) - 1) * 5,
            'search' => $search,
        ], compact('auditLogs')));
    }

    /**
     * Display completed documents
     */
    public function showComplete(Request $request): View
    {
        $user = Auth::user();

        // Base query with essential relations
        $query = Document::with(['status', 'documentWorkflow'])
            ->whereHas('status', function($q) {
                $q->whereIn('status', ['complete', 'completed', 'acknowledged', 'commented']);
            });

        // Filter by tab (sent/received)
        $tab = $request->input('tab', 'received');

        // Count documents for sent tab (documents user has uploaded and are complete)
        $sentCount = Document::whereHas('status', function($q) {
            $q->whereIn('status', ['complete', 'completed', 'acknowledged', 'commented']);
        })->where('uploader', $user->id)->count();

        // Count documents for received tab (documents where user is in workflow and are complete)
        $receivedCount = Document::whereHas('status', function($q) {
            $q->whereIn('status', ['complete', 'completed', 'acknowledged', 'commented']);
        })->whereHas('documentWorkflow', function($q) use ($user) {
            $q->where('recipient_id', $user->id);
        })->count();

        // Apply filter based on selected tab
        if ($tab === 'sent') {
            // Show documents uploaded by the user that are complete
            $query->where('uploader', $user->id);
        } else {
            // Show documents where user is a recipient in the workflow and are complete
            $query->whereHas('documentWorkflow', function($q) use ($user) {
                $q->where('recipient_id', $user->id);
            });
        }

        // Sort by latest first and paginate
        $documents = $query->latest()->paginate(10);

        return view('documents.complete', [
            'documents' => $documents,
            'tab' => $tab,
            'receivedCount' => $receivedCount,
            'sentCount' => $sentCount
        ]);
    }

    //Storing the document
    public function uploadController(Request $request)
    {
        Log::info('uploadController called');

        // Check if user has an office assigned
        $user = Auth::user();
        if (!$user || !$user->offices->first()) {
            return redirect()->back()
                ->with('error', 'You must be assigned to an office before creating documents. Please contact your administrator.')
                ->withInput();
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required',
            'category'       => 'nullable|integer',
            'classification' => 'required|in:Public,Office Only,Custom Offices,Private', // A-04 FIX: restrict to known values.
            'from_office'    => 'required|exists:offices,id',
            'main_document'  => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'attachments.*'  => 'file|mimes:jpeg,png,jpg,gif,pdf,docx|max:10240',
            'archive'        => 'nullable|string',
            'forward'        => 'nullable|string',
        ]);

        // Custom validation for Custom Offices classification
        if ($request->classification === 'Custom Offices') {
            if (!$request->has('allowed_offices') || empty($request->allowed_offices)) {
                return redirect()->back()
                    ->with('error', 'Please select at least one office for Custom Offices classification.')
                    ->withInput();
            }
        }

        try {
            Log::info('Starting document upload process');
            $companyId = $user->companies()->first()->id ?? 'default';
            $companyPath = $companyId;

            $file = $request->file('main_document');
            // A-02 FIX: Never use client-supplied filename for storage (path traversal / double-extension risk).
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            Log::info('Uploading file', ['fileName' => $fileName]);
            $filePath = $file->storeAs($companyPath . '/documents', $fileName, 'public');

            $document = Document::create([
                'title' => $request->title,
                'uploader' => $user->id,
                'description' => $request->description,
                'classification' => $request->classification,
                'category' => $request->category ?? null,
                'path' => $filePath,
                'from_office' => $request->from_office,
            ]);
            Log::info('Document created', ['document_id' => $document->id]);

            // Attach the document category - This fixes the categories not being assigned
            if ($request->has('category')) {
                $document->categories()->attach([$request->category]);
                \Log::info('Document category assigned', ['document_id' => $document->id, 'category_id' => $request->category]);
            }

            // Handle Custom Offices permissions
            if ($request->classification === 'Custom Offices' && $request->has('allowed_offices')) {
                foreach ($request->allowed_offices as $officeId) {
                    $document->allowedOffices()->create([
                        'office_id' => $officeId
                    ]);
                }
                \Log::info('Custom office permissions created', [
                    'document_id' => $document->id,
                    'offices' => $request->allowed_offices
                ]);
            }

            // Always set the initial status based on whether document is being forwarded
            $initialStatus = ($request->forward == '1') ? 'pending' : 'uploaded';
            $document->status()->create([
                'status' => 'uploaded',  // Always set to uploaded first
            ]);
            \Log::info('Initial document status set to uploaded', ['document_id' => $document->id]);

            $tracking_number = $this->generateTrackingNumber($request->from_office);
            \Log::info('Generated tracking number', ['tracking_number' => $tracking_number]);

            // Create tracking number record
            DocumentTrackingNumber::create([
                'doc_id' => $document->id,
                'tracking_number' => $tracking_number,
            ]);
            \Log::info('Tracking number record created', ['document_id' => $document->id]);

            // Only create transaction if to_office is provided AND forwarding is enabled
            if ($request->has('to_office') && $request->forward == '1') {
                DocumentTransaction::create([
                    'doc_id' => $document->id,
                    'from_office' => $request->from_office,
                    'to_office' => $request->to_office,
                ]);
                \Log::info('Document transaction created', ['document_id' => $document->id]);
            }

            // Handle additional attachments if any
            if ($request->hasFile('attachments')) {
                \Log::info('Processing attachments');
                foreach ($request->file('attachments') as $attachment) {
                    // A-02 FIX: random storage name; keep sanitised original for display.
                    $attachmentDisplayName = basename($attachment->getClientOriginalName());
                    $attachmentName        = Str::random(40) . '.' . $attachment->getClientOriginalExtension();
                    $companyPath = auth()->user()->companies()->first()->id ?? 'default';
                    \Log::info('Uploading attachment', ['attachmentName' => $attachmentName]);
                    $attachmentPath = $attachment->storeAs($companyPath . '/attachments', $attachmentName, 'public');

                    DocumentAttachment::create([
                        'document_id' => $document->id,
                        'filename' => $attachmentDisplayName,
                        'path' => $attachmentPath,
                        'storage_size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                    \Log::info('Attachment record created', ['document_id' => $document->id, 'attachmentName' => $attachmentName]);
                }
            }

            // Log document creation
            $this->logDocumentAction($document, 'created', 'pending', 'Document uploaded');
            \Log::info('Document upload action logged', ['document_id' => $document->id]);

            $data = $this->generateTrackingSlip($document->id, auth()->id(), $tracking_number);
            \Log::info('Tracking slip generated', ['document_id' => $document->id]);

            if ($request->forward == '1') {
                \Log::info('Redirecting to forward route', ['document_id' => $document->id]);
                // Document is already created with 'uploaded' status, now redirect to forward page
                return redirect()->route('documents.forward', $document->id)
                    ->with('data', $data)
                    ->with('success', 'Document uploaded successfully. Please select users to forward to.');
            } else {
                \Log::info('Redirecting to index route', ['document_id' => $document->id]);
                return redirect()->route('documents.index')
                    ->with('data', $data)
                    ->with('success', 'Document uploaded successfully');
            }
        } catch (Exception $e) {
            \Log::error('Document processing error in uploadController: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while processing your document. Please try again.') // A-05 FIX: no internal details exposed.
                ->withInput();
        }
    }

    public function forwardDocument(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // A-08 FIX: Only the uploader (or a company-admin / super-admin) may reach the
        // forward page.  Anyone else gets a 403.
        if (!$this->documentAccessService->canEditDocument($document)) {
            abort(403, 'Access Denied: You are not authorized to forward this document.');
        }

        // Get the companies this user belongs to
        $userCompanyIds = auth()->user()->companies()->pluck('company_id');

        // Get users from these companies
        $users = User::whereHas('companies', function ($query) use ($userCompanyIds) {
            $query->whereIn('company_id', $userCompanyIds);
        })->where('id', '!=', auth()->id())->get(); // Exclude the current user

        // Get offices from these companies
        $offices = Office::whereIn('company_id', $userCompanyIds)->get();

        return view('documents.forward', compact('document', 'offices', 'users'));
    }

    public function searchByTr(Request $request)
    {
        // Validate the tracking number input
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        try {
            // Search for the document by tracking number
            $documentTracking = DocumentTrackingNumber::where('tracking_number', $request->tracking_number)
                ->with('document') // Load the related document
                ->firstOrFail();

            $document = $documentTracking->document;

            // Redirect to the document's show route
            return redirect()->route('documents.show', $document->id)
                ->with('success', 'Document found.');
        } catch (\Exception $e) {
            // Log the error and return an error message
            \Log::error('Error searching for tracking number: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Tracking number not found.');
        }
    }
    /**
     * Search for documents using text input or image upload.
     */
    public function search(Request $request)
    {
        $request->validate([
            'text' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        try {
            $searchText = $request->input('text', '');
            $content = '';

            $query = Document::with(['user', 'status', 'transaction.fromOffice', 'transaction.toOffice']);

            if (!auth()->user()->hasRole('company-admin')) {
                $userOfficeIds = auth()->user()->offices->pluck('id')->toArray();
                $query->whereHas('user.offices', function ($q) use ($userOfficeIds) {
                    $q->whereIn('offices.id', $userOfficeIds);
                });
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                // A-02 FIX: random storage name.
                $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('documents', $fileName, 'public');
                $fullPath = storage_path("app/public/temp/{$filePath}");

                if ($this->isImage($file)) {
                    $qrResult = $this->scanQr($fullPath);

                    if (!empty($qrResult)) {
                        // If QR code contains a tracking number
                        $documentTracking = DocumentTrackingNumber::where('tracking_number', $qrResult)->first();

                        if ($documentTracking) {
                            $document = Document::find($documentTracking->doc_id);
                            if ($document) {
                                return redirect()->route('documents.show', $document->id)
                                    ->with('success', 'Document found by QR code.');
                            }
                        }
                    }
                }
            }

            if (!empty($searchText)) {
                $query->whereRaw('MATCH(content) AGAINST(? IN NATURAL LANGUAGE MODE)', [$searchText]);
            }

            $documents = $query->latest()->paginate(5);
            $auditLogs = DocumentAudit::paginate(15);

            // Determine the recipients for each document (same code as in index method)
            $documentRecipients = [];
            foreach ($documents as $doc) {
                $workflows = DocumentWorkflow::with(['recipient', 'recipientOffice'])
                    ->where('document_id', $doc->id)
                    ->get();

                $recipients = collect();

                foreach ($workflows as $workflow) {
                    // Add user recipients
                    if ($workflow->recipient) {
                        $name = trim($workflow->recipient->first_name . ' ' . $workflow->recipient->last_name);
                        $recipients->push([
                            'name' => $name,
                            'type' => 'user',
                            'step_order' => $workflow->step_order
                        ]);
                    }

                    // Add office recipients
                    if ($workflow->recipient_office && $workflow->recipientOffice) {
                        $recipients->push([
                            'name' => $workflow->recipientOffice->name,
                            'type' => 'office',
                            'step_order' => $workflow->step_order
                        ]);
                    }
                }

                $documentRecipients[$doc->id] = $recipients;
            }

            // Return to the current page with search results
            return redirect()->back()->with([
                'documents' => $documents,
                'auditLogs' => $auditLogs,
                'documentRecipients' => $documentRecipients,
                'searchPerformed' => true,
                'searchText' => $searchText,
            ]);
        } catch (Exception $e) {
            \Log::error('Search processing error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while processing your search. Please try again.') // A-05 FIX.
                ->withInput();
        }
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(): View
    {
        $currentUserCompany = auth()->user()->companies()->first();
        $originatingOfficeId = auth()->user()->offices->first()->id ?? null;

        // Use the correct model with company-based filtering
        $categories = DocumentCategory::where(function($query) use ($currentUserCompany) {
            $query->where('company_id', $currentUserCompany->id ?? null)
                  ->orWhere('is_global', true)
                  ->orWhereNull('company_id');
        })->orderBy('category')->get();

        $isCompanyAdmin = auth()->user()->hasRole('company-admin');

        // Get all offices from the user's company for Custom Offices selection
        $offices = Office::where('company_id', $currentUserCompany->id ?? null)->orderBy('name')->get();

        return view('documents.create', compact('categories', 'originatingOfficeId', 'currentUserCompany', 'offices', 'isCompanyAdmin'));
    }

    /**
     * B-09 FIX: This method was marked DEFUNCT and is unreachable via any active route.
     * (POST /documents → uploadController().)
     * Method is kept as an explicit stub to surface any future routing mistake early.
     *
     * @deprecated  Use uploadController() for document uploads.
     */
    public function store(Request $request): RedirectResponse
    {
        // Explicit rejection — not the active upload handler.
        abort(405, 'This endpoint is no longer in use.');
    }

    private function isImage($file): bool
    {
        $mimeType = $file->getMimeType();

        return strpos($mimeType, 'image/') === 0;
    }

    private function extractPdfContent(string $path): string
    {
        try {
            // Use the path directly in the constructor of Pdf
            $pdfContent = (new Pdf(
                env('POPPLER_PATH')
            ))
                ->setPdf($path)
                ->text();

            if (empty($pdfContent)) {
                // If no content extracted, try using shell_exec as fallback
                $outputFile = storage_path('app/temp/pdf_' . time() . '.txt');
                $command = env('POPPLER_PATH') . ' ' . str_replace('/', '\\', $path) . ' ' . str_replace('/', '\\', $outputFile);

                // Log the command for debugging
                \Log::info('PDF Command: ' . $command);

                $output = shell_exec($command);
                \Log::info('Shell exec output: ' . ($output ?? 'No output'));

                if (file_exists($outputFile)) {
                    $pdfContent = file_get_contents($outputFile);
                    unlink($outputFile); // Clean up
                }
            }

            if (empty($pdfContent)) {
                throw new Exception('PDF processing failed - no text extracted');
            }

            return $pdfContent;
        } catch (Exception $e) {
            \Log::error('PDF processing error: ' . $e->getMessage());
            \Log::error('PDF path: ' . $path);
            throw new Exception('PDF processing failed: ' . $e->getMessage());
        }
    }

    private function extractDocxContent(string $path): string
    {
        // Using PHPWord to extract text from DOCX
        $phpWord = IOFactory::load($path);
        $content = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $content .= $element->getText() . "\n";
                }
            }
        }

        return $content;
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document): View
    {
        // Check if the user can access this document using the new access service
        if (!$this->documentAccessService->canViewDocument($document)) {
            abort(403, 'Access Denied: You are not authorized to view this document. Please contact your administrator if you believe this is an error.');
        }

        // Get workflows and organize by step order for display
        $workflows = DocumentWorkflow::with(['sender', 'recipient', 'recipientOffice'])
            ->where('document_id', $document->id)
            ->orderBy('step_order')
            ->get();

        $docRoute = [];

        // Group recipients by step order
        foreach ($workflows as $workflow) {
            // Add user recipient if available
            if ($workflow->recipient) {
                $recipientName = trim(($workflow->recipient->first_name ?? '') . ' ' . ($workflow->recipient->last_name ?? ''));
                $docRoute[$workflow->step_order][] = [
                    'name' => $recipientName,
                    'type' => 'user',
                    'workflow' => $workflow
                ];
            }

            // Add office recipient if available
            if ($workflow->recipient_office && isset($workflow->recipientOffice->name)) {
                $docRoute[$workflow->step_order][] = [
                    'name' => $workflow->recipientOffice->name,
                    'type' => 'office',
                    'workflow' => $workflow
                ];
            }

            // If neither recipient nor office, add placeholder
            if ((!$workflow->recipient && !$workflow->recipient_office) ||
                ($workflow->recipient_office && !isset($workflow->recipientOffice->name))
            ) {
                $docRoute[$workflow->step_order][] = [
                    'name' => 'Unassigned',
                    'type' => 'none',
                    'workflow' => $workflow
                ];
            }
        }

        // Check if we should also fetch recipients from the document_recipients table
        if (empty($docRoute)) {
            $documentRecipients = $document->recipients()->with('offices')->get();

            if ($documentRecipients->isNotEmpty()) {
                foreach ($documentRecipients as $index => $recipient) {
                    $recipientName = trim(($recipient->first_name ?? '') . ' ' . ($recipient->last_name ?? ''));
                    $docRoute[1][] = [
                        'name' => $recipientName,
                        'type' => 'user',
                        'workflow' => null
                    ];
                }
            }
        }

        $attachments = Document::with('attachments.uploader')->findOrFail($document->id)->attachments;
        $auditLogs = DocumentAudit::where('document_id', $document->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $document->load('eSignatures.user');
        $document->load('originatingOffice');

        return view('documents.show', compact('document', 'auditLogs', 'attachments', 'docRoute', 'workflows'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document): View
    {
        // Check if the user can edit this document
        if (!$this->documentAccessService->canEditDocument($document)) {
            abort(403, 'Access Denied: You are not authorized to edit this document. Only the document owner or authorized personnel may make modifications.');
        }

        // Load attachments relationship
        $document->load('attachments');

        // Retrieve necessary data for the view
        $userOffice = auth()->user()->offices->pluck('name', 'id');
        $categories = DocumentCategory::all()->pluck('category', 'id');

        // Get all offices from the user's company for Custom Offices selection
        $currentUserCompany = auth()->user()->companies()->first();
        $offices = Office::where('company_id', $currentUserCompany->id ?? null)->orderBy('name')->get();

        // Pass only the necessary variables to the view
        return view('documents.edit', compact('document', 'categories', 'userOffice', 'offices'));
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required',
            'category'       => 'nullable|integer',
            'classification' => 'nullable|in:Public,Office Only,Custom Offices,Private', // A-04 FIX: restrict to known values.
            'from_office'    => 'required|exists:offices,id',
            'main_document'  => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,docx|max:10240',
            'attachments.*'  => 'file|mimes:jpeg,png,jpg,gif,pdf,docx|max:10240',
        ]);

        // Custom validation for Custom Offices classification
        if ($request->classification === 'Custom Offices') {
            if (!$request->has('allowed_offices') || empty($request->allowed_offices)) {
                return redirect()->back()
                    ->with('error', 'Please select at least one office for Custom Offices classification.')
                    ->withInput();
            }
        }

        try {
            \Log::info('Starting document update process', ['document_id' => $document->id]);

            // Update document basic information
            $document->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category ?? null,
            ]);
            \Log::info('Document basic info updated', ['document_id' => $document->id]);

            // Update status only if document is being forwarded
            if ($request->forward == '1') {
                $document->status()->update([
                    'status' => 'forwarded',
                ]);
                \Log::info('Document status updated to forwarded', ['document_id' => $document->id]);
            } else {
                // Keep original status if not forwarding
                \Log::info('Document status unchanged (not forwarding)', ['document_id' => $document->id]);
            }

            // If document had rejected or returned workflows, reset them to pending so receivers can receive again
            $workflowsToReset = \App\Models\DocumentWorkflow::where('document_id', $document->id)
                ->whereIn('status', ['rejected', 'returned'])
                ->get();

            foreach ($workflowsToReset as $workflow) {
                $workflow->status = 'pending';
                $workflow->received_at = null; // Reset received timestamp
                $workflow->save();
                \Log::info('Reset workflow to pending for re-receipt', [
                    'document_id' => $document->id,
                    'workflow_id' => $workflow->id,
                    'recipient_id' => $workflow->recipient_id
                ]);

                // Notify the receiver that the document has been updated and is ready for re-receipt
                \App\Models\Notifications::create([
                    'user_id' => $workflow->recipient_id,
                    'type' => 'document_updated_for_rereceipt',
                    'data' => json_encode([
                        'document_id' => $document->id,
                        'message' => 'A document has been updated and is ready for re-receipt.',
                        'title' => $document->title,
                        'updater' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    ]),
                ]);
            }

            // Handle file upload if new file is provided
            if ($request->hasFile('main_document')) {
                Storage::disk('public')->delete($document->path);
                $companyId = auth()->user()->companies()->first()->id ?? 'default';
                $companyPath = $companyId;

                $file = $request->file('main_document');
                // A-02 FIX: random storage name.
                $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                \Log::info('Uploading new file', ['fileName' => $fileName]);
                $filePath = $file->storeAs($companyPath . '/documents', $fileName, 'public');

                $document->update(['path' => $filePath]);
                \Log::info('Document file updated', ['document_id' => $document->id]);
            }

            // Update document categories
            if ($request->has('category')) {
                $document->categories()->detach();
                $document->categories()->attach([$request->category]);
                \Log::info('Document category updated', ['document_id' => $document->id, 'category_id' => $request->category]);
            }

            // Update document transaction if forwarding is enabled
            if ($request->has('to_office') && $request->forward == '1') {
                $document->transaction()->updateOrCreate(
                    ['doc_id' => $document->id],
                    [
                        'from_office' => $request->from_office,
                        'to_office' => $request->to_office,
                    ]
                );
                \Log::info('Document transaction updated', ['document_id' => $document->id]);
            }

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                \Log::info('Processing new attachments', ['document_id' => $document->id]);
                foreach ($request->file('attachments') as $attachment) {
                    $companyId = auth()->user()->companies()->first()->id ?? 'default';
                    $companyPath = $companyId;
                    // A-02 FIX: random storage name; preserve original for display.
                    $attachmentDisplayName = basename($attachment->getClientOriginalName());
                    $attachmentName        = Str::random(40) . '.' . $attachment->getClientOriginalExtension();
                    \Log::info('Uploading attachment', ['attachmentName' => $attachmentName]);
                    $attachmentPath = $attachment->storeAs($companyPath . '/attachments', $attachmentName, 'public');

                    DocumentAttachment::create([
                        'document_id' => $document->id,
                        'filename' => $attachmentDisplayName,
                        'path' => $attachmentPath,
                        'storage_size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                    \Log::info('Attachment record created', ['document_id' => $document->id, 'attachmentName' => $attachmentName]);
                }
            }

            // Update document status if archive is checked
            if ($request->archive == '1') {
                $document->status()->update(['status' => 'archived']);
                \Log::info('Document status updated to archived', ['document_id' => $document->id]);
            }

            // Log document update
            $this->logDocumentAction($document, 'updated', $document->status->status, 'Document updated');
            \Log::info('Document update action logged', ['document_id' => $document->id]);

            // Notify all users who have received or forwarded this document (except current user)
            $workflowUsers =
                \App\Models\DocumentWorkflow::where('document_id', $document->id)
                    ->where(function($q) {
                        $q->whereNotNull('recipient_id')->orWhereNotNull('sender_id');
                    })
                    ->get(['recipient_id', 'sender_id']);
            $userIds = collect();
            foreach ($workflowUsers as $row) {
                if ($row->recipient_id && $row->recipient_id != auth()->id()) {
                    $userIds->push($row->recipient_id);
                }
                if ($row->sender_id && $row->sender_id != auth()->id()) {
                    $userIds->push($row->sender_id);
                }
            }
            $userIds = $userIds->unique();
            foreach ($userIds as $uid) {
                \App\Models\Notifications::create([
                    'user_id' => $uid,
                    'type' => 'document_updated',
                    'data' => json_encode([
                        'document_id' => $document->id,
                        'message' => 'A document you were involved with was updated.',
                        'title' => $document->title,
                        'updater' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    ]),
                ]);
            }

            // Handle forward if enabled
            if ($request->forward == '1') {
                \Log::info('Redirecting to forward route', ['document_id' => $document->id]);
                return redirect()->route('documents.forward', $document->id)
                    ->with('success', 'Document updated successfully');
            } else {
                \Log::info('Redirecting to index route', ['document_id' => $document->id]);
                return redirect()->route('documents.index')
                    ->with('success', 'Document updated successfully');
            }
        } catch (Exception $e) {
            \Log::error('Document update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating the document. Please try again.') // A-05 FIX.
                ->withInput();
        }
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document): RedirectResponse
    {
        if (!auth()->user()->can('delete', $document)) {
            return redirect()->route('documents.index')
                ->with('error', 'Access Denied: You are not authorized to delete this document. Please contact your administrator for assistance.');
        }

        $this->logDocumentAction($document, 'deleted');
        Storage::disk('public')->delete($document->path);
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully');
    }

    /**
     * Restore a document from archive
     */
    public function restore(Document $document): RedirectResponse
    {
        if (!auth()->user()->can('restore', $document)) {
            return redirect()->route('documents.index')
                ->with('error', 'Access Denied: You are not authorized to restore this document. Please contact your administrator for assistance.');
        }

        $document->unarchive();

        return redirect()->route('documents.index')
            ->with('success', 'Document restored successfully');
    }

    public function deleteAttachment(Request $request, $documentId)
    {
        $attachmentId = $request->query('attachment_id');
        $attachment = DocumentAttachment::findOrFail($attachmentId);
        $document = Document::findOrFail($documentId);
        $real_status = $document->status()->get();

        // Delete the file from storage
        Storage::disk('public')->delete($attachment->path);

        // Delete the record from the database
        $attachment->delete();

        $document->status()->update(['status' => $real_status[0]->status]);

        return redirect()->back()->with('success', 'Attachment deleted successfully.');
    }

    public function deleteMultipleAttachments(Request $request, Document $document)
    {
        $request->validate([
            'attachment_ids' => 'required|array',
            'attachment_ids.*' => 'required|integer'
        ]);

        $attachmentIds = $request->attachment_ids;
        $real_status = $document->status()->get();

        // Get all valid attachments for this document
        $attachments = DocumentAttachment::whereIn('id', $attachmentIds)
                                     ->where('document_id', $document->id)
                                     ->get();

        foreach ($attachments as $attachment) {
            // Delete the file from storage
            Storage::disk('public')->delete($attachment->path);

            // Delete the record from the database
            $attachment->delete();
        }

        $document->status()->update(['status' => $real_status[0]->status]);

        return redirect()->back()->with('success', count($attachments) . ' attachments deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        // A-06 FIX: validate presence and size before any processing.
        $request->validate([
            'image' => 'required|string',
        ]);

        $rawData = $request->input('image');

        // Expect a data URI: "data:<mime>;base64,<data>"
        if (!str_contains($rawData, ';') || !str_contains($rawData, ',')) {
            return Response::json(['error' => 'Invalid image format.'], 422);
        }

        [$meta, $encoded] = explode(',', $rawData, 2);
        $binaryData = base64_decode($encoded, strict: true);

        if ($binaryData === false) {
            return Response::json(['error' => 'Invalid base64 data.'], 422);
        }

        // A-06 FIX: enforce size cap (5 MB) to prevent disk exhaustion.
        $maxBytes = 5 * 1024 * 1024;
        if (strlen($binaryData) > $maxBytes) {
            return Response::json(['error' => 'Image exceeds the maximum allowed size of 5 MB.'], 422);
        }

        // A-06 FIX: verify the decoded data is genuinely an image.
        $imageInfo = @getimagesizefromstring($binaryData);
        if ($imageInfo === false) {
            return Response::json(['error' => 'Uploaded data is not a valid image.'], 422);
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($imageInfo['mime'], $allowedMimes, true)) {
            return Response::json(['error' => 'Image type not allowed.'], 422);
        }

        // Safe extension derived from actual MIME type, not user input.
        $ext      = image_type_to_extension($imageInfo[2], include_dot: false);
        $filename = 'uploads/' . Str::random(40) . '.' . $ext;

        Storage::put($filename, $binaryData);

        return Response::json([
            'success'  => 'Image uploaded successfully',
            'filename' => $filename,
            'path'     => asset('storage/' . $filename),
        ]);
    }

    public function downloadFile($id)
    {
        try {
            // Resolve the target: try Document first, then fall back to DocumentAttachment.
            // (The original `findOrFail() ?? findOrFail()` pattern was broken because
            //  findOrFail() always throws on miss — it never returns null.)
            $document    = Document::find($id);
            $attachment  = null;

            if (!$document) {
                $attachment = DocumentAttachment::with('document')->findOrFail($id);
                $document   = $attachment->document;
            }

            if (!$document) {
                abort(404, 'File not found.');
            }

            // A-01 FIX: Authorize before serving the file.
            if (!$this->documentAccessService->canViewDocument($document)) {
                abort(403, 'Access Denied: You are not authorized to download this file.');
            }

            $target   = $attachment ?? $document;
            $filePath = storage_path('app/public/' . $target->path);

            if (!$target->path || !file_exists($filePath)) {
                return redirect()->back()->with('error', 'File not found or inaccessible.');
            }

            return response()->download($filePath);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'The requested file does not exist.');
        } catch (\Exception $e) {
            Log::error('Download error for ID ' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing your download request.');
        }
    }

    public function generateTrackingNumber($officeId, $length = 5)
    {
        // Retrieve the office abbreviation
        $office = Office::findOrFail($officeId);
        $prefix = strtoupper(substr($office->name, 0, 3)) . '-DOC';

        do {
            // Define the characters to use for the random part
            $characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $charactersLength = strlen($characters);
            $randomString     = '';

            // A-07 FIX: use random_int() instead of rand() — cryptographically secure.
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }

            // Combine the prefix with the random string and a timestamp
            $trackingNumber = $prefix . '-' . $randomString . '-' . date('Y');

            // Check if this tracking number already exists
            $exists = DocumentTrackingNumber::where('tracking_number', $trackingNumber)->exists();
        } while ($exists);

        return $trackingNumber;
    }

    public function generateTrackingSlip($docid = 0, $uploaderid = 0, $tracking_number = 5)
    {
        $document = Document::findOrFail($docid);
        $uploader = User::findOrFail($uploaderid);

        $qr = new QRCode;

        $data = $qr->render($tracking_number);

        return $data;
    }

    public function scanQr($image)
    {
        try {
            $qrReader = new \Zxing\QrReader($image);
            $content = $qrReader->text();
            return $content;
        } catch (\Throwable $e) {
            // oopsies!
            return $e->getMessage();
        }
    }

    //auditing
    private function logDocumentAction($document, $action, $status = null, $details = null)
    {
        DocumentAudit::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'status' => $status ?? $document->status->status,
            'details' => $details,
        ]);
    }

    public function audit()
    {
        $auditLogs = DocumentAudit::with(['document', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('documents.audit', compact('auditLogs'));
    }

    public function cancelWorkflow(Document $document)
    {
        // Cancel all pending workflows
        DocumentWorkflow::where('document_id', $document->id)
            ->whereIn('status', ['pending', 'received'])
            ->update(['status' => 'cancelled']);

        // Update document status
        $document->status()->update(['status' => 'cancelled']);

        // Log action
        DocumentAudit::logDocumentAction(
            $document->id,
            auth()->id(),
            'workflow',
            'cancelled',
            'Document workflow cancelled'
        );

        return redirect()->route('documents.index')
            ->with('success', 'Document workflow has been cancelled.');
    }

  /**
 * Display the document receiving index page.
 * Shows documents forwarded to user from any sender (admin or regular user)
 */
public function receiveIndex(): View
{
    $currentUserId = auth()->id();
    $userOfficeIds = auth()->user()->offices->pluck('id')->toArray();

    // Build query to get documents that can be received by the current user
    $documentsQuery = Document::with([
        'user',
        'status',
        'workflow',
        'transaction.fromOffice',
        'transaction.toOffice',
        'documentWorkflow.sender',
        'documentWorkflow.recipient'
    ]);

    // Primary condition: Documents forwarded to this user (from any user)
    $documentsQuery->where(function($query) use ($currentUserId, $userOfficeIds) {
        // 1. Documents with workflows where current user is the recipient
        $query->whereHas('documentWorkflow', function($workflowQuery) use ($currentUserId) {
            $workflowQuery->where('recipient_id', $currentUserId)
                         ->whereIn('status', ['pending', 'received']);
            // FIXED: Removed company-admin restriction to allow user-to-user forwarding
        })

        // 2. OR documents sent to user's office (fallback for office-based routing)
        ->orWhere(function($officeQuery) use ($userOfficeIds) {
            $officeQuery->whereHas('transaction', function($transQuery) use ($userOfficeIds) {
                $transQuery->whereIn('to_office', $userOfficeIds);
            });
            // FIXED: Removed company-admin restriction to allow office-based routing from any user
        });
    });

    // Exclude completed and recalled documents
    $documentsQuery->whereHas('status', function($query) {
        $query->whereNotIn('status', ['complete', 'recalled']);
    });

    // Order by latest first
    $documents = $documentsQuery->latest()->paginate(10);

    return view('documents.receive', compact('documents'));
}

/**
 * Confirm receipt of a document.
 * Reformed to handle admin-sent documents properly
 */
public function receiveConfirm(Document $document)
{
    try {
        $currentUserId = auth()->id();

        // Check if document has been recalled
        if ($document->status && $document->status->status === 'recalled') {
            return redirect()->route('documents.receive.index')
                ->with('error', 'This document has been recalled by the sender and cannot be received.');
        }

        // Find the specific workflow for this user (if exists)
        $userWorkflow = DocumentWorkflow::where('document_id', $document->id)
            ->where('recipient_id', $currentUserId)
            ->whereIn('status', ['pending', 'received'])
            ->first();

        if ($userWorkflow) {
            // Update the specific workflow status to received
            $userWorkflow->receive(); // This will now also sync document status

            // Log the action
            DocumentAudit::logDocumentAction(
                $document->id,
                $currentUserId,
                'workflow',
                'received',
                'Document received via workflow by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name
            );
        } else {
            // If no specific workflow found, check if document has any workflow and update main document status
            $hasWorkflow = DocumentWorkflow::where('document_id', $document->id)->exists();

            if (!$hasWorkflow) {
                // Create a workflow entry for this receipt
                $workflow = DocumentWorkflow::create([
                    'document_id' => $document->id,
                    'sender_id' => $document->uploader,
                    'recipient_id' => $currentUserId,
                    'step_order' => 1,
                    'status' => 'received',
                    'received_at' => now(),
                    'tracking_number' => 'RCV-' . time() . '-' . $document->id,
                ]);

                // This will automatically sync document status
                $workflow->receive();
            } else {
                // Update document status directly if workflow exists but user not in it
                $document->status()->update(['status' => 'received']);
            }

            // Log the action
            DocumentAudit::logDocumentAction(
                $document->id,
                $currentUserId,
                'document',
                'received',
                'Document received directly by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name
            );
        }

        return redirect()->route('documents.receive.index')
            ->with('success', 'Document has been successfully received.');

    } catch (\Exception $e) {
        Log::error('Failed to receive document ID ' . $document->id . ': ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'An error occurred while receiving the document. Please try again.'); // A-05 FIX.
    }
}

/**
     * Recall a document, pause workflow, and notify recipients.
     */
    public function recallDocument(Request $request, Document $document)
    {
        // Only the document owner can recall
        if ($document->uploader !== auth()->id()) {
            return redirect()->back()->with('error', 'Access Denied: You are not authorized to recall this document. Only the document owner may perform this action.');
        }

        // Pause workflow: set a status or flag (e.g., 'recalled')
        $document->status()->update(['status' => 'recalled']);

        // Pause all associated workflows
        $workflows = \App\Models\DocumentWorkflow::where('document_id', $document->id)->get();
        foreach ($workflows as $workflow) {
            $workflow->pause();
        }

        // Notify all recipients in the workflow
        foreach ($workflows as $workflow) {
            if ($workflow->recipient_id) {
                \App\Models\Notifications::create([
                    'user_id' => $workflow->recipient_id,
                    'type' => 'document_recall',
                    'data' => json_encode([
                        'message' => 'A document you are a recipient of has been recalled and the workflow is paused.',
                        'title' => $document->title,
                        'document_id' => $document->id,
                        'recalled_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    ]),
                ]);
            }
        }

        // Log the recall action
        \App\Models\DocumentAudit::logDocumentAction(
            $document->id,
            auth()->id(),
            'recall',
            'recalled',
            'Document recalled and workflow paused'
        );

        return redirect()->back()->with('success', 'Document has been recalled and workflow paused. Recipients have been notified.');
    }

    /**
     * Resume a recalled document and its workflow.
     */
    public function resumeDocument(Request $request, Document $document)
    {
        // Only the document owner can resume
        if ($document->uploader !== auth()->id()) {
            return redirect()->back()->with('error', 'Access Denied: You are not authorized to resume this document. Only the document owner may perform this action.');
        }

        // Check if the document was recalled
        if ($document->status->status !== 'recalled') {
            return redirect()->back()->with('error', 'This document is not in a recalled state.');
        }

        // Set document status back to active
        $document->status()->update(['status' => 'forwarded']);

        // Resume all associated workflows
        $workflows = \App\Models\DocumentWorkflow::where('document_id', $document->id)->get();
        foreach ($workflows as $workflow) {
            $workflow->resume();
        }

        // Notify all recipients in the workflow
        foreach ($workflows as $workflow) {
            if ($workflow->recipient_id) {
                \App\Models\Notifications::create([
                    'user_id' => $workflow->recipient_id,
                    'type' => 'document_resumed',
                    'data' => json_encode([
                        'message' => 'A document that was previously recalled has been resumed. The workflow is now active again.',
                        'title' => $document->title,
                        'document_id' => $document->id,
                        'resumed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    ]),
                ]);
            }
        }

        // Log the resume action
        \App\Models\DocumentAudit::logDocumentAction(
            $document->id,
            auth()->id(),
            'resume',
            'forwarded',
            'Document workflow resumed'
        );

        return redirect()->back()->with('success', 'Document workflow has been resumed. Recipients have been notified.');
    }

    /**
     * Archive a document
     *
     * @param Document $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archiveDocument(Document $document)
    {
        // Check if the user has permission to archive the document
        if (auth()->user()->id !== $document->uploader && !auth()->user()->hasRole('company-admin')) {
            return redirect()->route('documents.index')
                ->with('error', 'Access Denied: You are not authorized to archive this document. Only the document owner or company administrators may perform this action.');
        }

        // Update the document status to archived
        $document->status()->update(['status' => 'archived']);

        // Create an audit log
        DocumentAudit::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'Archived',
            'status' => 'Archived',
            'details' => 'Document was archived'
        ]);

        // Log the action
        \Log::info('Document archived', ['document_id' => $document->id, 'user_id' => auth()->id()]);

        return redirect()->route('documents.index')
            ->with('success', 'Document has been archived successfully.');
    }
}
