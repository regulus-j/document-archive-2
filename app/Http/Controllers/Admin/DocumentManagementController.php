<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\DocumentAudit;

use App\Models\CompanyAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DocumentManagementController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:company-admin']);
    // }
    
    /**
     * Display document management dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a company associated with your account.');
        }
        // Get all users in this company
        $companyUserIds = User::whereHas('companies', function($query) use ($company) {
            $query->where('company_accounts.id', $company->id);
        })->pluck('id')->toArray();
        
        // Get document counts using the approach from ReportController::getStorageMetrics
        $totalDocuments = Document::whereIn('uploader', $companyUserIds)->count();

        // Count archived documents
        $archivedDocuments = Document::whereIn('uploader', $companyUserIds)
            ->whereHas('status', function($q) {
                $q->where('status', 'archived');
            })
            ->count();
        
        // Calculate storage usage
        $storageUsage = 0;
        $documents = Document::whereIn('uploader', $companyUserIds)->get();
        $documentIds = $documents->pluck('id')->toArray();
        
        // Calculate document sizes
        foreach ($documents as $document) {
            $docPath = $document->path ?? '';
            if ($docPath && Storage::disk('public')->exists($docPath)) {
                $size = Storage::disk('public')->size($docPath);
                $storageUsage += $size;
            }
        }
        
        // Add attachment sizes
        $attachments = DocumentAttachment::whereIn('document_id', $documentIds)->get();
        foreach ($attachments as $attachment) {
            $attachPath = $attachment->path ?? '';
            if ($attachPath && Storage::disk('public')->exists($attachPath)) {
                $size = Storage::disk('public')->size($attachPath);
                $storageUsage += $size;
            }
        }
        
        $storageUsageMB = $storageUsage ? round($storageUsage / 1024 / 1024, 2) : 0;
        

            
        // Recent audit logs - update to use the same approach
        $auditLogs = DocumentAudit::with(['document', 'user'])
            ->whereHas('document', function ($q) use ($companyUserIds) {
                $q->whereIn('uploader', $companyUserIds);
            })
            ->latest()
            ->take(10)
            ->get();
            
        return view('admin.document-management.index', compact(
            'totalDocuments', 
            'archivedDocuments', 
            'storageUsageMB', 
            
            'auditLogs'
        ));
    }
    
    /**
     * Display a listing of documents with search and filtering
     */
    public function documents(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Get all users in this company
        $companyUserIds = User::whereHas('companies', function($query) use ($company) {
            $query->where('company_accounts.id', $company->id);
        })->pluck('id')->toArray();
        
        // Modified query to use the uploader field with company users
        $query = Document::with(['user', 'status', 'categories', 'transaction.fromOffice', 'transaction.toOffice'])
                ->whereIn('uploader', $companyUserIds);
            
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply date filters
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }
        
        // Apply archived filter using status relationship
        if ($request->has('show_archived')) {
            if ($request->show_archived) {
                $query->whereHas('status', function($q) {
                    $q->where('status', 'archived');
                });
            } else {
                $query->whereHas('status', function($q) {
                    $q->where('status', '!=', 'archived');
                });
            }
        }
            
        $documents = $query->latest()->paginate(15);
        
        return view('admin.document-management.documents', compact('documents'));
    }
    
    /**
     * Show document details
     */
    public function show($id)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Get all users in this company for proper filtering
        $companyUserIds = User::whereHas('companies', function($query) use ($company) {
            $query->where('company_accounts.id', $company->id);
        })->pluck('id')->toArray();
        
        // Get the document with needed relationships
        $document = Document::with([
            'user', 
            'categories', 
            'attachments', 
            'status',
            'transaction.fromOffice',
            'transaction.toOffice',
            'documentWorkflow.sender',
            'documentWorkflow.recipient',
            'documentWorkflow.recipientOffice'
        ])->whereIn('uploader', $companyUserIds);
        
        $document = $document->findOrFail($id);
        
        // Get audit logs for this document
        $auditLogs = DocumentAudit::where('document_id', $document->id)
            ->with('user')
            ->latest()
            ->get();
            
        // Get workflows and organize by step order for display
        $workflows = $document->documentWorkflow()
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
            if ($workflow->recipient_office && $workflow->recipientOffice) {
                $docRoute[$workflow->step_order][] = [
                    'name' => $workflow->recipientOffice->name,
                    'type' => 'office',
                    'workflow' => $workflow
                ];
            }
            
            // If neither recipient nor office, add placeholder
            if ((!$workflow->recipient && !$workflow->recipient_office) || 
                ($workflow->recipient_office && !$workflow->recipientOffice)) {
                $docRoute[$workflow->step_order][] = [
                    'name' => 'Unassigned',
                    'type' => 'none',
                    'workflow' => $workflow
                ];
            }
        }
            
        return view('admin.document-management.show', compact('document', 'auditLogs', 'docRoute', 'workflows'));
    }
    
    /**
     * Delete a document
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Get the document with the proper relationship filter
        $documentQuery = Document::query();
        
        if (Schema::hasColumn('documents', 'company_id')) {
            $document = $documentQuery->where('company_id', $company->id)->findOrFail($id);
        } else {
            $document = $documentQuery->whereHas('user.companies', function($q) use ($company) {
                $q->where('companies.id', $company->id);
            })->findOrFail($id);
        }
        
        // Log the deletion
        DocumentAudit::logDocumentAction(
            $document->id,
            $user->id,
            'delete',
            'deleted',
            'Document deleted by company admin'
        );
        
        // Delete the document
        $document->delete();
        
        return redirect()->route('admin.document-management.documents')
            ->with('success', 'Document has been deleted.');
    }
    
    /**
     * Toggle archive status of a document
     */
    public function toggleArchive($id)
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        // Get the document with the proper relationship filter
        $documentQuery = Document::with('status');
        
        if (Schema::hasColumn('documents', 'company_id')) {
            $document = $documentQuery->where('company_id', $company->id)->findOrFail($id);
        } else {
            $document = $documentQuery->whereHas('user.companies', function($q) use ($company) {
                $q->where('companies.id', $company->id);
            })->findOrFail($id);
        }
        
        // Check the current status
        $currentStatus = $document->status ? $document->status->status : 'pending';
        $newStatus = $currentStatus === 'archived' ? 'pending' : 'archived';
        
        // Update the document status
        if ($document->status) {
            $document->status()->update(['status' => $newStatus]);
        } else {
            $document->status()->create(['status' => $newStatus]);
        }
        
        // Log the action
        DocumentAudit::logDocumentAction(
            $document->id,
            $user->id,
            'archive',
            $newStatus,
            "Document {$newStatus} by company admin"
        );
        
        return redirect()->route('admin.document-management.show', $document->id)
            ->with('success', "Document has been {$newStatus}.");
    }
    
    /**
     * Delete documents in bulk based on criteria
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        $user = Auth::user();
        $company = $user->companies()->first();
        
        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a company associated with your account.');
        }
        
        Log::info('Starting bulk delete operation', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ]);
        
        // Build query with proper company filter
        $query = Document::query();
        
        // Get all users in this company for proper filtering
        $companyUserIds = User::whereHas('companies', function($query) use ($company) {
            $query->where('company_accounts.id', $company->id);
        })->pluck('id')->toArray();
        
        Log::info('Company users found', ['count' => count($companyUserIds)]);
        
        // Find documents uploaded by users in this company
        $query->whereIn('uploader', $companyUserIds);
        
        // Apply date from filter if provided
        if ($request->date_from) {
            $fromDate = Carbon::parse($request->date_from)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }
        
        // Apply date to filter if provided
        if ($request->date_to) {
            $toDate = Carbon::parse($request->date_to)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        }
        
        // Exclude archived documents using direct query on the status relationship
        // We're checking:
        // 1. Documents with no status record
        // 2. Documents with a status that's not 'archived'
        $documentsToDelete = $query->where(function($q) {
            $q->whereDoesntHave('status')
              ->orWhereHas('status', function($subq) {
                  $subq->where('status', '!=', 'archived');
              });
        })->get();
        
        $count = $documentsToDelete->count();
        
        Log::info('Documents found for bulk deletion', [
            'count' => $count,
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings()
        ]);
        
        if ($count === 0) {
            return redirect()->route('admin.document-management.documents')
                ->with('info', 'No documents found matching the deletion criteria.');
        }
        
        $deletedCount = 0;
        
        // Process each document for proper deletion
        foreach ($documentsToDelete as $document) {
            try {
                // Log the deletion action
                DocumentAudit::logDocumentAction(
                    $document->id,
                    $user->id,
                    'bulk-delete',
                    'deleted',
                    'Document deleted as part of bulk operation by company admin'
                );
                
                // Delete document file from storage
                if ($document->path && Storage::disk('public')->exists($document->path)) {
                    Storage::disk('public')->delete($document->path);
                }
                
                // Delete attachment files from storage
                foreach ($document->attachments as $attachment) {
                    if ($attachment->path && Storage::disk('public')->exists($attachment->path)) {
                        Storage::disk('public')->delete($attachment->path);
                    }
                }
                
                // Actually delete the document record (this will use soft delete)
                $document->delete();
                $deletedCount++;
                
            } catch (\Exception $e) {
                Log::error('Error deleting document', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        Log::info('Bulk delete completed', [
            'found' => $count,
            'deleted' => $deletedCount
        ]);
    }
}
