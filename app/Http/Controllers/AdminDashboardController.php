<?php

namespace App\Http\Controllers;

use App\Models\CompanyAccount;
use App\Models\Document;
use App\Models\CompanySubscription;
use App\Models\DocumentAudit;
use App\Models\DocumentWorkflow;
use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the super-admin dashboard with comprehensive SaaS KPIs.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            if (auth()->user()->hasRole('company-admin')) {
                return redirect()->route('dashboard');
            }
            abort(403, 'Unauthorized access. You need super-admin privileges.');
        }

        // ── Core counts ──────────────────────────────────────────────
        $totalCompanies       = CompanyAccount::count();
        $totalUsers           = User::count();
        $totalDocuments       = Document::count();
        $activeSubscriptions  = CompanySubscription::where('status', 'active')->count();
        $totalSubscriptions   = CompanySubscription::count();
        $totalTeams           = Office::count();

        // ── Revenue ──────────────────────────────────────────────────
        $totalRevenue = CompanySubscription::where('status', 'active')
            ->join('plans', 'company_subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price') / 100;

        $mrr = (CompanySubscription::where('status', 'active')
            ->join('plans', 'company_subscriptions.plan_id', '=', 'plans.id')
            ->selectRaw("SUM(CASE WHEN plans.billing_cycle = 'yearly' THEN plans.price / 12 ELSE plans.price END) as mrr")
            ->value('mrr') ?? 0) / 100;

        // ── Growth rates (vs last month) ─────────────────────────────
        $lastMonth = Carbon::now()->subMonth();
        $companyGrowth      = $this->growthRate(CompanyAccount::class, $lastMonth, $totalCompanies);
        $userGrowth         = $this->growthRate(User::class, $lastMonth, $totalUsers);
        $documentGrowth     = $this->growthRate(Document::class, $lastMonth, $totalDocuments);
        $subscriptionGrowth = $this->subGrowthRate($lastMonth, $activeSubscriptions);

        // ── Today's stats ────────────────────────────────────────────
        $newUsersToday    = User::whereDate('created_at', today())->count();
        $newDocsToday     = Document::whereDate('created_at', today())->count();
        $newCompaniesToday = CompanyAccount::whereDate('created_at', today())->count();

        // ── Monthly trend (12 months) ────────────────────────────────
        $monthlyStats = $this->getMonthlyStats(12);

        // ── Subscription plan distribution (doughnut) ────────────────
        $planDistribution = CompanySubscription::where('status', 'active')
            ->join('plans', 'company_subscriptions.plan_id', '=', 'plans.id')
            ->select('plans.plan_name', DB::raw('COUNT(*) as total'))
            ->groupBy('plans.plan_name')
            ->pluck('total', 'plan_name');

        // ── Document status breakdown (pie) ──────────────────────────
        $documentStatuses = DB::table('document_status')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Top companies by users ───────────────────────────────────
        $topCompaniesByUsers = CompanyAccount::withCount('employees')
            ->orderByDesc('employees_count')
            ->take(10)
            ->get();

        // ── Top companies by documents ───────────────────────────────
        $topCompaniesByDocs = CompanyAccount::select('company_accounts.*')
            ->selectSub(
                DB::table('company_users')
                    ->join('documents', 'company_users.user_id', '=', 'documents.uploader')
                    ->whereColumn('company_users.company_id', 'company_accounts.id')
                    ->selectRaw('COUNT(documents.id)'),
                'documents_count'
            )
            ->orderByDesc('documents_count')
            ->take(10)
            ->get();

        // ── Recent activities (last 15) ──────────────────────────────
        $recentActivities = $this->getRecentActivities(15);

        // ── Expiring subscriptions (next 30 days) ────────────────────
        $expiringSubscriptions = CompanySubscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date')
            ->take(10)
            ->get();

        // ── Recent subscriptions ─────────────────────────────────────
        $recentSubscriptions = CompanySubscription::with(['company', 'plan'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // ── Companies list for audit drill-down ──────────────────────
        $companiesList = CompanyAccount::withCount(['employees', 'offices', 'subscriptions'])
            ->orderBy('company_name')
            ->get();

        // ── All users for admin user management table ────────────────
        $usersTable = User::with(['companies', 'roles'])
            ->latest()
            ->paginate(15, ['*'], 'users_page');

        // ── All documents for admin document table ───────────────────
        $documentsTable = Document::with(['user', 'status', 'trackingNumber', 'categories'])
            ->latest()
            ->paginate(15, ['*'], 'docs_page');

        // ── Plans table ──────────────────────────────────────────────
        $plansTable = Plan::withCount('subscriptions')->get();

        // ── Subscriptions table ──────────────────────────────────────
        $subscriptionsTable = CompanySubscription::with(['company', 'plan'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'subs_page');

        // ── Companies table ──────────────────────────────────────────
        $companiesTable = CompanyAccount::withCount(['employees', 'offices', 'subscriptions'])
            ->orderBy('company_name')
            ->paginate(15, ['*'], 'companies_page');

        return view('admin.dashboard', compact(
            'totalCompanies', 'totalUsers', 'totalDocuments',
            'activeSubscriptions', 'totalSubscriptions', 'totalTeams',
            'totalRevenue', 'mrr',
            'companyGrowth', 'userGrowth', 'documentGrowth', 'subscriptionGrowth',
            'newUsersToday', 'newDocsToday', 'newCompaniesToday',
            'monthlyStats', 'planDistribution', 'documentStatuses',
            'topCompaniesByUsers', 'topCompaniesByDocs',
            'recentActivities', 'expiringSubscriptions', 'recentSubscriptions',
            'companiesList',
            'usersTable', 'documentsTable', 'companiesTable', 'plansTable', 'subscriptionsTable'
        ));
    }

    /**
     * Export the dashboard as a PDF report.
     */
    public function exportPdf(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $totalCompanies      = CompanyAccount::count();
        $totalUsers          = User::count();
        $totalDocuments      = Document::count();
        $activeSubscriptions = CompanySubscription::where('status', 'active')->count();

        $totalRevenue = CompanySubscription::where('status', 'active')
            ->join('plans', 'company_subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price') / 100;

        $mrr = (CompanySubscription::where('status', 'active')
            ->join('plans', 'company_subscriptions.plan_id', '=', 'plans.id')
            ->selectRaw("SUM(CASE WHEN plans.billing_cycle = 'yearly' THEN plans.price / 12 ELSE plans.price END) as mrr")
            ->value('mrr') ?? 0) / 100;

        $monthlyStats = $this->getMonthlyStats(12);

        $topCompaniesByUsers = CompanyAccount::withCount('employees')
            ->orderByDesc('employees_count')->take(10)->get();

        $topCompaniesByDocs = CompanyAccount::select('company_accounts.*')
            ->selectSub(
                DB::table('company_users')
                    ->join('documents', 'company_users.user_id', '=', 'documents.uploader')
                    ->whereColumn('company_users.company_id', 'company_accounts.id')
                    ->selectRaw('COUNT(documents.id)'),
                'documents_count'
            )
            ->orderByDesc('documents_count')->take(10)->get();

        $expiringSubscriptions = CompanySubscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date')->take(10)->get();

        $generatedAt = now()->format('F d, Y h:i A');
        $generatedBy = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        $pdf = Pdf::loadView('admin.dashboard-pdf', compact(
            'totalCompanies', 'totalUsers', 'totalDocuments', 'activeSubscriptions',
            'totalRevenue', 'mrr', 'monthlyStats',
            'topCompaniesByUsers', 'topCompaniesByDocs', 'expiringSubscriptions',
            'generatedAt', 'generatedBy'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream('SuperAdmin_Dashboard_' . now()->format('Y-m-d_His') . '.pdf');
    }

    /**
     * Export a data table as Excel.
     */
    public function exportExcel(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:users,documents,companies,subscriptions',
        ]);

        $type = $request->input('type');
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Super Admin Export: ' . ucfirst($type));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('F d, Y h:i A'));
        $row = 4;

        switch ($type) {
            case 'users':
                $sheet->setTitle('Users');
                $headers = ['ID', 'Name', 'Email', 'Company', 'Roles', 'Joined'];
                $this->writeRow($sheet, $row++, $headers, true);
                User::with(['companies', 'roles'])->chunk(200, function ($users) use ($sheet, &$row) {
                    foreach ($users as $u) {
                        $this->writeRow($sheet, $row++, [
                            $u->id,
                            $u->first_name . ' ' . $u->last_name,
                            $u->email,
                            $u->companies->pluck('company_name')->join(', '),
                            $u->roles->pluck('name')->join(', '),
                            $u->created_at?->format('Y-m-d'),
                        ]);
                    }
                });
                break;

            case 'documents':
                $sheet->setTitle('Documents');
                $headers = ['ID', 'Title', 'Uploader', 'Company', 'Status', 'Created'];
                $this->writeRow($sheet, $row++, $headers, true);
                Document::with(['user.companies', 'status'])->chunk(200, function ($docs) use ($sheet, &$row) {
                    foreach ($docs as $d) {
                        $this->writeRow($sheet, $row++, [
                            $d->id,
                            $d->title,
                            $d->user ? $d->user->first_name . ' ' . $d->user->last_name : 'N/A',
                            $d->user && $d->user->companies->first() ? $d->user->companies->first()->company_name : 'N/A',
                            $d->status->status ?? 'N/A',
                            $d->created_at?->format('Y-m-d'),
                        ]);
                    }
                });
                break;

            case 'companies':
                $sheet->setTitle('Companies');
                $headers = ['ID', 'Company Name', 'Owner', 'Users', 'Teams', 'Subscription', 'Created'];
                $this->writeRow($sheet, $row++, $headers, true);
                CompanyAccount::with(['user', 'subscriptions.plan'])->withCount(['employees', 'offices'])->chunk(200, function ($companies) use ($sheet, &$row) {
                    foreach ($companies as $c) {
                        $activeSub = $c->subscriptions->where('status', 'active')->first();
                        $this->writeRow($sheet, $row++, [
                            $c->id,
                            $c->company_name,
                            $c->user ? $c->user->first_name . ' ' . $c->user->last_name : 'N/A',
                            $c->employees_count,
                            $c->offices_count,
                            $activeSub && $activeSub->plan ? $activeSub->plan->plan_name : 'None',
                            $c->created_at?->format('Y-m-d'),
                        ]);
                    }
                });
                break;

            case 'subscriptions':
                $sheet->setTitle('Subscriptions');
                $headers = ['ID', 'Company', 'Plan', 'Status', 'Start Date', 'End Date', 'Auto Renew'];
                $this->writeRow($sheet, $row++, $headers, true);
                CompanySubscription::with(['company', 'plan'])->chunk(200, function ($subs) use ($sheet, &$row) {
                    foreach ($subs as $s) {
                        $this->writeRow($sheet, $row++, [
                            $s->id,
                            $s->company->company_name ?? 'N/A',
                            $s->plan->plan_name ?? 'N/A',
                            ucfirst($s->status),
                            $s->start_date,
                            $s->end_date ?? 'N/A',
                            $s->auto_renew ? 'Yes' : 'No',
                        ]);
                    }
                });
                break;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'SuperAdmin_' . ucfirst($type) . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/public/temp/' . $filename);

        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }

        $writer->save($tempPath);
        return response()->download($tempPath, $filename)->deleteFileAfterSend();
    }

    /**
     * Site-wide audit data endpoint for super admin.
     */
    public function audit(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        return view('admin.audit', $this->gatherAuditData($request));
    }

    // ── Private helpers ──────────────────────────────────────────────

    private function growthRate(string $model, Carbon $since, int $current): float
    {
        $previous = $model::where('created_at', '<', $since)->count();
        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : 0;
    }

    private function subGrowthRate(Carbon $since, int $current): float
    {
        $previous = CompanySubscription::where('status', 'active')->where('created_at', '<', $since)->count();
        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : 0;
    }

    private function getMonthlyStats(int $months = 12): array
    {
        $labels = [];
        $companiesData = [];
        $usersData = [];
        $documentsData = [];
        $subscriptionsData = [];
        $revenueData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $start = $month->copy()->startOfMonth();
            $end   = $month->copy()->endOfMonth();

            $companiesData[]     = CompanyAccount::whereBetween('created_at', [$start, $end])->count();
            $usersData[]         = User::whereBetween('created_at', [$start, $end])->count();
            $documentsData[]     = Document::whereBetween('created_at', [$start, $end])->count();
            $subscriptionsData[] = CompanySubscription::whereBetween('created_at', [$start, $end])->count();

            $revenueData[] = ((float) CompanySubscription::withoutGlobalScopes()
                ->where('status', 'active')
                ->where('start_date', '<=', $end)
                ->where(function ($q) use ($start) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $start);
                })
                ->join('plans', 'company_subscriptions.plan_id', '=', 'plans.id')
                ->selectRaw("SUM(CASE WHEN plans.billing_cycle = 'yearly' THEN plans.price / 12 ELSE plans.price END) as mrr")
                ->value('mrr') ?? 0) / 100;
        }

        return compact('labels', 'companiesData', 'usersData', 'documentsData', 'subscriptionsData', 'revenueData');
    }

    private function getRecentActivities(int $limit = 15)
    {
        $userActivities = User::with('companies')->latest()->take($limit)->get()->map(function ($user) {
            $company = $user->companies->first();
            return [
                'id'           => $user->id,
                'type'         => 'user',
                'action'       => 'User registered',
                'user_name'    => $user->first_name . ' ' . $user->last_name,
                'company_name' => $company->company_name ?? 'No Company',
                'company_id'   => $company->id ?? null,
                'created_at'   => $user->created_at,
            ];
        });

        $docActivities = Document::with('user.companies')->latest()->take($limit)->get()->map(function ($doc) {
            $user = $doc->user;
            $company = $user?->companies->first();
            return [
                'id'           => $doc->id,
                'type'         => 'document',
                'action'       => 'Document uploaded',
                'user_name'    => $user ? $user->first_name . ' ' . $user->last_name : 'Unknown',
                'company_name' => $company->company_name ?? 'No Company',
                'company_id'   => $company->id ?? null,
                'created_at'   => $doc->created_at,
            ];
        });

        return $userActivities->merge($docActivities)->sortByDesc('created_at')->take($limit)->values();
    }

    private function writeRow($sheet, int $row, array $values, bool $bold = false): void
    {
        $col = 'A';
        foreach ($values as $val) {
            $sheet->setCellValue($col . $row, $val);
            $col++;
        }
        if ($bold) {
            $lastCol = chr(ord('A') + count($values) - 1);
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F1F5F9');
        }
    }

    /**
     * Export site-wide audit data as PDF.
     */
    public function auditExportPdf(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $auditData = $this->gatherAuditData($request);

        $pdf = Pdf::loadView('admin.audit-pdf', $auditData)
            ->setPaper('a4', 'landscape');

        $filename = 'Site_Audit_' . ($auditData['selectedCompany']->company_name ?? 'All_Companies') . '_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export site-wide audit data as Excel.
     */
    public function auditExportExcel(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $auditData = $this->gatherAuditData($request);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheetIndex = 0;

        $title = 'Site-Wide Audit: ' . ($auditData['selectedCompany']->company_name ?? 'All Companies');
        $dateRange = $auditData['startDate'] . ' — ' . $auditData['endDate'];
        $generatedAt = now()->format('F d, Y h:i A');
        $generatedBy = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        // --- Audit Logs Sheet ---
        if ($auditData['auditData'] && $auditData['auditData']['audit_logs']->count()) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Audit Logs');
            $this->writeExcelHeader($sheet, $title, $dateRange, $generatedAt);
            $row = 5;
            $this->writeRow($sheet, $row, ['Date', 'User', 'Action', 'Document', 'Details'], true);
            $row++;
            foreach ($auditData['auditData']['audit_logs'] as $log) {
                $this->writeRow($sheet, $row, [
                    $log->created_at->format('M d, Y H:i'),
                    $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System',
                    $log->action ?? 'N/A',
                    $log->document->title ?? 'N/A',
                    $log->details ?? '-',
                ]);
                $row++;
            }
            $this->autoSizeColumns($sheet, 5);
            $sheetIndex++;
        }

        // --- Uploaded Documents Sheet ---
        if ($auditData['auditData'] && $auditData['auditData']['uploaded_documents']->count()) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Uploaded Documents');
            $this->writeExcelHeader($sheet, $title, $dateRange, $generatedAt);
            $row = 5;
            $this->writeRow($sheet, $row, ['Date', 'Title', 'Uploader', 'Status', 'Tracking #'], true);
            $row++;
            foreach ($auditData['auditData']['uploaded_documents'] as $doc) {
                $this->writeRow($sheet, $row, [
                    $doc->created_at->format('M d, Y'),
                    $doc->title,
                    $doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'N/A',
                    $doc->status->status ?? 'N/A',
                    $doc->trackingNumber->tracking_number ?? '-',
                ]);
                $row++;
            }
            $this->autoSizeColumns($sheet, 5);
            $sheetIndex++;
        }

        // --- Workflows Sheet ---
        if ($auditData['auditData'] && $auditData['auditData']['workflows']->count()) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Workflows');
            $this->writeExcelHeader($sheet, $title, $dateRange, $generatedAt);
            $row = 5;
            $this->writeRow($sheet, $row, ['Date', 'Document', 'Sender', 'Recipient', 'Status'], true);
            $row++;
            foreach ($auditData['auditData']['workflows'] as $wf) {
                $this->writeRow($sheet, $row, [
                    $wf->created_at->format('M d, Y H:i'),
                    $wf->document->title ?? 'N/A',
                    $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A',
                    $wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'N/A',
                    $wf->status ?? 'N/A',
                ]);
                $row++;
            }
            $this->autoSizeColumns($sheet, 5);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Site_Audit_' . str_replace(' ', '_', $auditData['selectedCompany']->company_name ?? 'All_Companies') . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/public/temp/' . $filename);

        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }

        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend();
    }

    /**
     * Gather audit data from request parameters (reusable for exports).
     */
    private function gatherAuditData(Request $request): array
    {
        $companies = CompanyAccount::withCount(['employees', 'offices'])->orderBy('company_name')->get();
        $companyId = $request->input('company_id');
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        $auditData = null;
        $selectedCompany = null;

        if ($request->filled('company_id') || $request->input('scope') === 'all') {
            $scope = $request->input('scope', 'company');

            if ($scope === 'all') {
                $userIds = User::pluck('id');
                $selectedCompany = (object) ['company_name' => 'All Companies (Site-Wide)'];
            } else {
                $selectedCompany = CompanyAccount::findOrFail($companyId);
                $userIds = $selectedCompany->employees()->pluck('users.id');
            }

            $auditData = [
                'audit_logs' => DocumentAudit::with(['document', 'user'])
                    ->whereIn('user_id', $userIds)
                    ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                    ->orderByDesc('created_at')
                    ->take(500)
                    ->get(),
                'uploaded_documents' => Document::with(['user', 'status', 'trackingNumber'])
                    ->whereIn('uploader', $userIds)
                    ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                    ->orderByDesc('created_at')
                    ->take(500)
                    ->get(),
                'workflows' => DocumentWorkflow::with(['document', 'sender', 'recipient'])
                    ->where(function ($q) use ($userIds) {
                        $q->whereIn('sender_id', $userIds)
                          ->orWhereIn('recipient_id', $userIds);
                    })
                    ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                    ->orderByDesc('created_at')
                    ->take(500)
                    ->get(),
            ];
        }

        return compact('companies', 'companyId', 'startDate', 'endDate', 'auditData', 'selectedCompany');
    }

    private function writeExcelHeader($sheet, string $title, string $dateRange, string $generatedAt): void
    {
        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', $dateRange);
        $sheet->setCellValue('A3', 'Generated: ' . $generatedAt);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(11);
        $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748B');
    }

    private function autoSizeColumns($sheet, int $count): void
    {
        foreach (range('A', chr(ord('A') + $count - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
