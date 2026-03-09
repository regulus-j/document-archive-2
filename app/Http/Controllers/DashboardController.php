<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Models\CompanySubscription;
use App\Models\DocumentWorkflow;
use App\Models\Office;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Properly check roles and redirect to appropriate dashboard
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        $userCompany = $user->companies()->first();
        
        // Check if user is a super admin
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // ** Free Trial Check **
        $trialEndDate = DB::table('company_users')
            ->where('user_id', $user->id)
            ->value('trial_ends_at');

        if ($trialEndDate && now()->lessThan($trialEndDate)) {
            $activeSubscription = (object) ['status' => 'trial', 'ends_at' => $trialEndDate];
        } else {
            if (!$userCompany) {
                // Initialize all variables needed by dashboard-office-user.blade.php
                $totalDocuments = 0;
                $recentDocuments = collect();
                $pendingDocuments = 0;
                $todayDocuments = 0;
                $activeSubscription = null;
                $countPendingDocs = 0;
                $countRecentDocs = 0;
                $countCompanyUsers = 0;
                $incomingDocuments = 0; 
                $countOffices = "No Offices Found";
                
                return view('dashboard-office-user', compact(
                    'totalDocuments',
                    'recentDocuments',
                    'pendingDocuments',
                    'todayDocuments',
                    'activeSubscription',
                    'countPendingDocs',
                    'countRecentDocs',
                    'countCompanyUsers',
                    'incomingDocuments',
                    'countOffices'
                ))->with('info', 'Please set up your company profile first.');
            }

            $activeSubscription = CompanySubscription::where('company_id', $userCompany->id)
                ->where('status', 'active')
                ->first();

            if (!$activeSubscription) {
                return redirect()->route('plans.select')
                    ->with('info', 'Please select a plan and complete the payment to continue.');
            }
        }

        // Fetch dashboard data
        $incomingDocuments = DocumentWorkflow::where('recipient_id', $user->id)
            ->whereIn('status', ['pending', 'appeal_requested'])
            ->count();

        // Ensure userCompany is not null before accessing employees
        $countCompanyUsers = $userCompany ? $userCompany->employees()->count() : 0;

        $document = new Document;
        $recentTransactions = $document->transactions()->latest()->paginate(5);

        $totalDocuments = Document::whereIn(
            'uploader',
            $userCompany ? $userCompany->employees->pluck('id')->push($user->id)->unique() : collect()
        )->count();

        $recentDocuments = Document::with('user', 'office', 'categories')->latest()->take(5)->get();

        $pendingDocuments = DocumentWorkflow::where('status', 'pending')
            ->whereIn('document_id', function ($query) use ($userCompany, $user) {
                $query->select('id')
                    ->from('documents')
                    ->whereIn('uploader', $userCompany ? $userCompany->employees->pluck('id')->push($user->id)->unique() : collect());
            })
            ->count();

        $todayDocuments = Document::whereDate('created_at', today())->count();
        $countPendingDocs = $pendingDocuments;
        $countRecentDocs = $recentDocuments->count();
        $countOffices = $userCompany ? Office::where('company_id', $userCompany->id)->count() : "No Offices Found";

        $processedDocuments = DocumentWorkflow::where('recipient_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotIn('document_id', function ($query) use ($user) {
                $query->select('document_id')
                    ->from('document_workflows')
                    ->where('sender_id', $user->id);
            })
            ->count();

        // User performance: documents uploaded per user in the company
        $userPerformance = collect();
        $officePerformance = collect();
        $storageByOffice = collect();

        if ($userCompany) {
            $companyEmployeeIds = $userCompany->employees->pluck('id')->push($user->id)->unique();

            $userPerformance = User::whereIn('id', $companyEmployeeIds)
                ->withCount(['documents as total_docs'])
                ->get()
                ->map(function ($u) {
                    $processedCount = DocumentWorkflow::where('recipient_id', $u->id)
                        ->whereIn('status', ['approved', 'rejected'])
                        ->count();
                    $u->processed_docs = $processedCount;
                    return $u;
                });

            // Office performance: documents per office
            $offices = Office::where('company_id', $userCompany->id)->with('users')->get();

            $officePerformance = $offices->map(function ($office) {
                $officeUserIds = $office->users->pluck('id');
                $docCount = Document::whereIn('uploader', $officeUserIds)->count();
                $pendingCount = DocumentWorkflow::where('status', 'pending')
                    ->whereHas('document', function ($q) use ($officeUserIds) {
                        $q->whereIn('uploader', $officeUserIds);
                    })->count();
                $office->doc_count = $docCount;
                $office->pending_count = $pendingCount;
                return $office;
            });

            // Storage by office: sum file sizes of attachments for documents uploaded by office users
            $storageByOffice = $offices->map(function ($office) {
                $officeUserIds = $office->users->pluck('id');
                $attachments = DocumentAttachment::whereHas('document', function ($q) use ($officeUserIds) {
                    $q->whereIn('uploader', $officeUserIds);
                })->get(['path']);

                $totalBytes = 0;
                foreach ($attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment->path)) {
                        $totalBytes += Storage::disk('public')->size($attachment->path);
                    }
                }
                $office->storage_bytes = $totalBytes;
                $office->storage_formatted = $this->formatBytes($totalBytes);
                return $office;
            });
        }

        // **🚀 Correct Role Check for Admin**
        if ($user->hasRole('company-admin') && $userCompany) {
            return view('dashboard', compact(
                'recentTransactions',
                'totalDocuments',
                'recentDocuments',
                'pendingDocuments',
                'todayDocuments',
                'activeSubscription',
                'countPendingDocs',
                'countRecentDocs',
                'countCompanyUsers',
                'incomingDocuments',
                'countOffices',
                'totalDocuments',
                'userPerformance',
                'officePerformance',
                'storageByOffice',
            ));
        } else {
            return view('dashboard-office-user', compact(
                'totalDocuments',
                'recentDocuments',
                'pendingDocuments',
                'todayDocuments',
                'activeSubscription',
                'countPendingDocs',
                'countRecentDocs',
                'countCompanyUsers',
                'incomingDocuments',
                'countOffices',
            ));
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}