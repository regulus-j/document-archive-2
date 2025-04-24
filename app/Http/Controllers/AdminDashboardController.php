<?php

namespace App\Http\Controllers;

use App\Models\CompanyAccount;
use App\Models\Document;
use App\Models\CompanySubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with real data.
     */
    public function index()
    {
        // Check for proper role authorization first
        if (!auth()->user()->hasRole('super-admin')) {
            // Redirect company admins to regular dashboard
            if (auth()->user()->hasRole('company-admin')) {
                return redirect()->route('dashboard');
            }
            
            // Handle unauthorized access
            abort(403, 'Unauthorized access. You need super-admin privileges.');
        }
        
        // Get counts for dashboard stats
        $totalCompanies = CompanyAccount::count();
        $totalUsers = User::count();
        $totalDocuments = Document::count();
        $activeSubscriptions = CompanySubscription::where('status', 'active')->count();

        // Additional statistics for enhanced dashboard
        $documents = Document::all();
        $totalStorage = 0;
        
        // Calculate total storage by checking actual file sizes
        foreach ($documents as $document) {
            $docPath = $document->path ?? '';
            if ($docPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($docPath)) {
                $totalStorage += \Illuminate\Support\Facades\Storage::disk('public')->size($docPath);
            }
        }
        
        // Also add sizes of document attachments
        $attachments = \App\Models\DocumentAttachment::all();
        foreach ($attachments as $attachment) {
            $attachPath = $attachment->path ?? '';
            if ($attachPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($attachPath)) {
                $totalStorage += \Illuminate\Support\Facades\Storage::disk('public')->size($attachPath);
            }
        }
        
        $formattedTotalStorage = $this->formatBytes($totalStorage);
        $avgDocumentSize = $totalDocuments > 0 ? $this->formatBytes($totalStorage / $totalDocuments) : '0 B';
        $totalOffices = \App\Models\Office::count();
        $documentsCreatedThisWeek = Document::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()])->count();
        $pendingWorkflows = \App\Models\DocumentWorkflow::where('status', 'pending')->count();
        $completedWorkflows = \App\Models\DocumentWorkflow::whereIn('status', ['approved', 'rejected'])->count();
        $expiredSubscriptions = CompanySubscription::where('status', 'expired')
            ->whereBetween('end_date', [Carbon::now()->subMonth(), Carbon::now()])
            ->count();
        
        // Get most active companies based on document count
        $mostActiveCompanies = CompanyAccount::withCount(['documents' => function($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            }])
            ->withCount('employees') // Add this to get the employee count
            ->orderByDesc('documents_count')
            ->take(5)
            ->get();
            
        // Get subscription distribution by plan
        $subscriptionsByPlan = CompanySubscription::with('plan')
            ->select('plan_id', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('plan_id')
            ->orderByDesc('total')
            ->get()
            ->map(function($subscription) {
                return [
                    'plan_name' => $subscription->plan ? $subscription->plan->plan_name : 'Unknown Plan',
                    'count' => $subscription->total,
                ];
            });

        // Calculate renewal rates without using the non-existent renewal_count column
        // Instead, look for subscriptions that were created within the last month but have earlier start dates
        // This suggests they're likely renewals rather than brand new subscriptions
        $renewedSubscriptions = CompanySubscription::where('created_at', '>=', Carbon::now()->subMonth())
            ->whereRaw('DATE(created_at) > DATE(start_date)')
            ->count();
            
        $expiredLastMonth = CompanySubscription::where('status', 'expired')
            ->whereBetween('end_date', [Carbon::now()->subMonth(), Carbon::now()])
            ->count();
            
        $renewalRate = $expiredLastMonth > 0 ? round(($renewedSubscriptions / $expiredLastMonth) * 100, 1) : 0;

        // Calculate growth rates compared to last month
        $lastMonth = Carbon::now()->subMonth();
        
        $companiesLastMonth = CompanyAccount::where('created_at', '<', $lastMonth)->count();
        $usersLastMonth = User::where('created_at', '<', $lastMonth)->count();
        $documentsLastMonth = Document::where('created_at', '<', $lastMonth)->count();
        $subscriptionsLastMonth = CompanySubscription::where('status', 'active')
            ->where('created_at', '<', $lastMonth)
            ->count();

        // Calculate growth percentages
        $companyGrowth = $companiesLastMonth > 0 
            ? round((($totalCompanies - $companiesLastMonth) / $companiesLastMonth) * 100, 1) 
            : 0;
        
        $userGrowth = $usersLastMonth > 0 
            ? round((($totalUsers - $usersLastMonth) / $usersLastMonth) * 100, 1) 
            : 0;
        
        $documentGrowth = $documentsLastMonth > 0 
            ? round((($totalDocuments - $documentsLastMonth) / $documentsLastMonth) * 100, 1) 
            : 0;
        
        $subscriptionGrowth = $subscriptionsLastMonth > 0 
            ? round((($activeSubscriptions - $subscriptionsLastMonth) / $subscriptionsLastMonth) * 100, 1) 
            : 0;

        // Get today's stats
        $today = Carbon::today();
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newDocumentsToday = Document::whereDate('created_at', $today)->count();

        // Calculate target for daily progress (based on average of last 30 days)
        $last30DaysUsers = User::whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->count();
        $last30DaysDocs = Document::whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->count();

        $dailyUserTarget = ceil($last30DaysUsers / 30) * 2; // Double the average for an aspirational target
        $dailyDocTarget = ceil($last30DaysDocs / 30) * 2;

        // Calculate progress percentages
        $userProgress = $dailyUserTarget > 0 ? min(100, round(($newUsersToday / $dailyUserTarget) * 100)) : 0;
        $docProgress = $dailyDocTarget > 0 ? min(100, round(($newDocumentsToday / $dailyDocTarget) * 100)) : 0;

        // Get latest companies
        $latestCompanies = CompanyAccount::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch recent activities - FIX: Properly access company relationships
        $recentUserActivities = User::with('companies')->latest()->take(5)->get()->map(function($user) {
            // Use companies() relationship instead of company
            $userCompany = $user->companies()->first();
            $companyName = $userCompany ? $userCompany->company_name : 'No Company';
            $companyId = $userCompany ? $userCompany->id : null;
            
            return [
                'id' => $user->id,
                'user_id' => $user->id, // Add explicit user_id for view links
                'company_name' => $companyName,
                'company_id' => $companyId,
                'action' => 'User registered',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'created_at' => $user->created_at,
                'type' => 'user'
            ];
        });

        $recentDocumentActivities = Document::with('user.companies')->latest()->take(5)->get()->map(function($document) {
            $user = $document->user;
            // Use companies() relationship instead of company
            $userCompany = $user && $user->companies()->first() ? $user->companies()->first() : null;
            $companyName = $userCompany ? $userCompany->company_name : 'No Company';
            $companyId = $userCompany ? $userCompany->id : null;
            
            return [
                'id' => $document->id,
                'document_id' => $document->id, // Add explicit document_id for view links
                'company_name' => $companyName,
                'company_id' => $companyId,
                'action' => 'Document uploaded',
                'user_name' => $user ? ($user->first_name . ' ' . $user->last_name) : 'Unknown',
                'created_at' => $document->created_at,
                'type' => 'document'
            ];
        });

        // Convert both collections to arrays of objects and merge
        $combinedActivities = $recentUserActivities->toBase()->merge($recentDocumentActivities);
        $recentActivities = $combinedActivities->sortByDesc('created_at')->take(10)->values();

        // Get monthly statistics for the growth chart
        $monthlyStats = $this->getMonthlyStats();

        return view('admin.dashboard', compact(
            'totalCompanies', 
            'totalUsers', 
            'totalDocuments', 
            'activeSubscriptions',
            'companyGrowth',
            'userGrowth',
            'documentGrowth',
            'subscriptionGrowth',
            'newUsersToday',
            'newDocumentsToday',
            'userProgress',
            'docProgress',
            'dailyUserTarget',
            'dailyDocTarget',
            'latestCompanies',
            'monthlyStats',
            'recentActivities',
            'formattedTotalStorage',
            'avgDocumentSize',
            'totalOffices',
            'documentsCreatedThisWeek',
            'pendingWorkflows',
            'completedWorkflows',
            'expiredSubscriptions',
            'mostActiveCompanies',
            'subscriptionsByPlan',
            'renewalRate'
        ));
    }

    /**
     * Get monthly statistics for the growth chart.
     */
    private function getMonthlyStats()
    {
        $months = [];
        $companiesData = [];
        $usersData = [];
        $documentsData = [];
        $subscriptionsData = [];

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M');
            
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            // Companies created in this month
            $companiesData[] = CompanyAccount::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            
            // Users created in this month
            $usersData[] = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            
            // Documents created in this month
            $documentsData[] = Document::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            
            // Subscriptions created in this month
            $subscriptionsData[] = CompanySubscription::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        return [
            'months' => $months,
            'companiesData' => $companiesData,
            'usersData' => $usersData,
            'documentsData' => $documentsData,
            'subscriptionsData' => $subscriptionsData
        ];
    }

    /**
     * Format bytes to a human-readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
