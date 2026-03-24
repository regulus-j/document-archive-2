<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Document;
use App\Models\DocumentWorkflow;
use App\Models\CompanyAccount;
use App\Models\DocumentAudit;
use App\Models\Office;
use App\Models\User;
use App\Models\Log;
use App\Models\DocumentAttachment;
use App\Models\DocumentCategory;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Resolve the current user's company first so every query below is tenant-scoped.
        $company = CompanyAccount::where('user_id', auth()->id())->first()
            ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
        $companyId = $company?->id;

        // IDs of every user that belongs to this company (employees + owner).
        $companyUserIds = $company
            ? $company->employees()->pluck('users.id')->push($company->user_id)->filter()->unique()
            : collect([auth()->id()]);

        // Scope the reports list to this company's users only.
        $query = Report::with('user')
            ->whereIn('user_id', $companyUserIds)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->user, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('generated_at', $date);
            });

        $reports = $query->latest('generated_at')->paginate(10);

        // Pick up any generated report data from session (from the generate action)
        $data = session('generated_data');
        $reportType = session('generated_report_type');

        // Analytics

        $startDate = $request->input('start_date') ?: now()->subMonth()->format('Y-m-d');
        $endDate = $request->input('end_date') ?: now()->format('Y-m-d');
        $userId = $request->input('user_id');
        $officeId = $request->input('office_id');
        $displayType = $request->input('display_type', 'table'); // Default to table view

        if ($displayType === 'pdf') {
            return $this->exportAnalyticsToPdf($startDate, $endDate, $userId, $officeId);
        }

        // Scope to documents uploaded by members of this company.
        // Note: documents.company_id is not reliably populated (legacy data has NULL),
        // so we scope via the uploader column which is always set.
        $companyDocIds = Document::whereIn('uploader', $companyUserIds)->select('id');

        $averageTimeToReceiveMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
            ->whereIn('document_id', $companyDocIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at')
            ->when($userId, fn($q) => $q->where('recipient_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('recipient.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->value('avg_time') ?? 0;

        $averageTimeToReviewMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
            ->whereIn('document_id', $companyDocIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at')
            ->whereNotNull('updated_at')
            ->whereRaw('received_at <= updated_at')
            ->when($userId, fn($q) => $q->where('recipient_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('recipient.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->value('avg_time') ?? 0;

        // Format time values as hours:minutes:seconds
        $averageTimeToReceive = $this->formatTimeInMinutes($averageTimeToReceiveMinutes);
        $averageTimeToReview = $this->formatTimeInMinutes($averageTimeToReviewMinutes);
        
        // For chart data, we need the raw minutes
        $averageTimeToReceiveRaw = round($averageTimeToReceiveMinutes, 2);
        $averageTimeToReviewRaw = round($averageTimeToReviewMinutes, 2);

        $averageDocsForwarded = DocumentWorkflow::whereIn('document_id', $companyDocIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('sender_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('sender.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->count();

        $documentsUploaded = Document::whereIn('uploader', $companyUserIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('uploader', $userId))
            ->count();

        // Get collections for dropdowns
        if (!$company) {
            \Log::warning('No company found for user: ' . auth()->id());
            // Handle the case where no company exists
        }

        $users = $company ? $company->employees()->paginate(5) : collect();
        $offices = $company ? Office::where('company_id', $company->id)->get() : collect();

        // Get additional data for charts - monthly trends
        $monthlyData = $this->getMonthlyAnalyticsChartData($startDate, $endDate, $userId, $officeId);

        return view('reports.index', compact('reports', 'averageTimeToReceive',
            'averageTimeToReview',
            'averageTimeToReceiveRaw',
            'averageTimeToReviewRaw',
            'averageDocsForwarded',
            'documentsUploaded',
            'startDate',
            'endDate',
            'userId',
            'officeId',
            'users',
            'offices',
            'displayType',
            'monthlyData',
            'data',
            'reportType'));
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        return view('reports.show', compact('report'));
    }

    public function download(Report $report, $format = 'pdf')
    {
        $this->authorize('view', $report);

        $filename = Str::slug($report->name) . '_' . $report->generated_at->format('Y-m-d');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('report'));
            return $pdf->download($filename . '.pdf');
        } elseif ($format === 'word') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addText($report->name, ['bold' => true, 'size' => 16]);
            $section->addText('Generated at: ' . $report->generated_at->format('Y-m-d H:i:s'));
            $section->addText('Type: ' . ucfirst($report->type));
            $section->addText('Description: ' . $report->description);
            $section->addText('Report Data:');
            $section->addText(json_encode($report->data, JSON_PRETTY_PRINT));

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            return response()->streamDownload(function () use ($objWriter) {
                $objWriter->save('php://output');
            }, $filename . '.docx');
        }

        return back()->with('error', 'Invalid download format');
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);

        $report->delete();

        // Log the deletion
        Log::create([
            'action' => 'delete',
            'description' => "Deleted report: {$report->name}",
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('reports.index')
            ->with('success', 'Report deleted successfully');
    }

    /**
     * Display analytics data or export to PDF
     * 
     * @param Request $request
     * @return View|Response
     */
    public function analytics(Request $request)
    {
        // Handle PDF export directly
        $displayType = $request->input('display_type', 'table');
        if ($displayType === 'pdf') {
            $startDate = $request->input('start_date') ?: now()->subMonth()->format('Y-m-d');
            $endDate = $request->input('end_date') ?: now()->format('Y-m-d');
            return $this->exportAnalyticsToPdf($startDate, $endDate, $request->input('user_id'), $request->input('office_id'));
        }

        // Redirect all other analytics requests to the unified index page
        return redirect()->route('reports.index', $request->only(['start_date', 'end_date', 'user_id', 'office_id', 'display_type']));
    }

    /**
     * Resolve company branding (logo as base64 data URI, accent color, company name)
     * for the currently authenticated user's company.
     *
     * @param CompanyAccount|null $company  Pass an already-resolved company to avoid a second query.
     * @return array{logoDataUri:string|null, companyColor:string, companyName:string}
     */
    private function getCompanyBranding(?CompanyAccount $company = null): array
    {
        if (!$company) {
            // Prefer the company owned by the user; fall back to any company they belong to
            $company = CompanyAccount::where('user_id', auth()->id())->first()
                ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
        }

        $logoDataUri = null;
        if ($company && $company->logo) {
            $path = Storage::disk('public')->path($company->logo);
            if (file_exists($path)) {
                $mime = mime_content_type($path);
                $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        return [
            'logoDataUri'  => $logoDataUri,
            'companyColor' => $company ? $company->colorHex() : '#2563eb',
            'companyName'  => $company ? $company->company_name : 'Document Archive',
        ];
    }

    /**
     * Export analytics data to PDF
     */
    private function exportAnalyticsToPdf($startDate, $endDate, $userId = null, $officeId = null)
    {
        // Resolve company so every query is tenant-scoped.
        $company = CompanyAccount::where('user_id', auth()->id())->first()
            ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
        $companyUserIds = $company
            ? $company->employees()->pluck('users.id')->push($company->user_id)->filter()->unique()
            : collect([auth()->id()]);
        $companyDocIds = Document::whereIn('uploader', $companyUserIds)->select('id');

        // Get the same analytics data as in the analytics method
        $averageTimeToReceiveMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
            ->whereIn('document_id', $companyDocIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at')
            ->when($userId, fn($q) => $q->where('recipient_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('recipient.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->value('avg_time') ?? 0;

        $averageTimeToReviewMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
            ->whereIn('document_id', $companyDocIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at')
            ->whereNotNull('updated_at')
            ->whereRaw('received_at <= updated_at')
            ->when($userId, fn($q) => $q->where('recipient_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('recipient.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->value('avg_time') ?? 0;

        $averageTimeToReceive = $this->formatTimeInMinutes($averageTimeToReceiveMinutes);
        $averageTimeToReview = $this->formatTimeInMinutes($averageTimeToReviewMinutes);
        
        $averageDocsForwarded = DocumentWorkflow::whereIn('document_id', $companyDocIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('sender_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('sender.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->count();

        $documentsUploaded = Document::whereIn('uploader', $companyUserIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('uploader', $userId))
            ->count();
            
        // Get user and office details if specified
        $userDetails = null;
        $officeDetails = null;
        
        if ($userId) {
            $userDetails = User::find($userId);
        }
        
        if ($officeId) {
            $officeDetails = Office::find($officeId);
        }
        
        // Get monthly trend data for the table and charts
        $monthlyData = $this->getMonthlyAnalyticsData($startDate, $endDate, $userId, $officeId);
        $monthlyChartData = $this->getMonthlyAnalyticsChartData($startDate, $endDate, $userId, $officeId);
        
        // Get company branding for the PDF header
        $branding = $this->getCompanyBranding();

        // Create PDF
        $pdf = PDF::loadView('reports.analytics_pdf', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userDetails' => $userDetails,
            'officeDetails' => $officeDetails,
            'averageTimeToReceive' => $averageTimeToReceive,
            'averageTimeToReview' => $averageTimeToReview,
            'averageDocsForwarded' => $averageDocsForwarded,
            'documentsUploaded' => $documentsUploaded,
            'monthlyData' => $monthlyData,
            'charts' => $this->buildAnalyticsChartUrls(
                $monthlyChartData,
                $averageDocsForwarded,
                $documentsUploaded,
                $branding['companyColor']
            ),
            'generatedAt' => now()->format('F j, Y h:i A'),
            'generatedBy' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'logoDataUri'  => $branding['logoDataUri'],
            'companyColor' => $branding['companyColor'],
            'companyName'  => $branding['companyName'],
        ])
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => true]);
        
        $fileName = 'analytics_report_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $pdf->download($fileName);
    }

    private function buildAnalyticsChartUrls(array $monthlyChartData, int $docsForwarded, int $docsUploaded, string $accentColor): array
    {
        $lineChart = [
            'type' => 'line',
            'data' => [
                'labels' => $monthlyChartData['months'],
                'datasets' => [
                    [
                        'label' => 'Docs Forwarded',
                        'data' => $monthlyChartData['docsForwarded'],
                        'borderColor' => $accentColor,
                        'backgroundColor' => 'rgba(99,102,241,0.15)',
                        'fill' => true,
                        'tension' => 0.35,
                        'pointRadius' => 2,
                    ],
                    [
                        'label' => 'Docs Uploaded',
                        'data' => $monthlyChartData['docsUploaded'],
                        'borderColor' => '#94a3b8',
                        'backgroundColor' => 'rgba(148,163,184,0.12)',
                        'fill' => true,
                        'tension' => 0.35,
                        'pointRadius' => 2,
                    ],
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'top'],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];

        $barChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $monthlyChartData['months'],
                'datasets' => [
                    [
                        'label' => 'Avg Time to Receive (min)',
                        'data' => $monthlyChartData['receiveTimes'],
                        'backgroundColor' => 'rgba(99,102,241,0.2)',
                        'borderColor' => $accentColor,
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Avg Time to Review (min)',
                        'data' => $monthlyChartData['reviewTimes'],
                        'backgroundColor' => 'rgba(148,163,184,0.25)',
                        'borderColor' => '#94a3b8',
                        'borderWidth' => 1,
                    ],
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'top'],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];

        $doughnutChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Forwarded', 'Uploaded'],
                'datasets' => [
                    [
                        'data' => [$docsForwarded, $docsUploaded],
                        'backgroundColor' => [
                            $accentColor,
                            '#cbd5f5',
                        ],
                        'borderColor' => ['#ffffff', '#ffffff'],
                        'borderWidth' => 2,
                    ],
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                ],
                'cutout' => '60%',
            ],
        ];

        return [
            'monthlyTrends' => $this->buildQuickChartUrl($lineChart, 900, 320),
            'processingTimes' => $this->buildQuickChartUrl($barChart, 900, 320),
            'distribution' => $this->buildQuickChartUrl($doughnutChart, 420, 320),
        ];
    }

    private function buildQuickChartUrl(array $config, int $width, int $height): string
    {
        $query = http_build_query([
            'c' => json_encode($config),
            'w' => $width,
            'h' => $height,
            'backgroundColor' => 'white',
        ]);

        return 'https://quickchart.io/chart?' . $query;
    }

    /**
     * Format time in minutes to a human-readable format
     */
    private function formatTimeInMinutes($minutes)
    {
        if ($minutes <= 0) {
            return "0 minutes";
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . " hour" . ($hours != 1 ? "s" : "") . " " . 
                  $remainingMinutes . " minute" . ($remainingMinutes != 1 ? "s" : "");
        }
        
        return $minutes . " minute" . ($minutes != 1 ? "s" : "");
    }
    
    /**
     * Get monthly analytics data for charts
     */
    private function getMonthlyAnalyticsChartData($startDate, $endDate, $userId = null, $officeId = null)
    {
        // Convert strings to Carbon instances
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        // Get company for scoping (owner OR employee)
        $company = CompanyAccount::where('user_id', auth()->id())->first()
            ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
        $companyUserIds = $company
            ? $company->employees()->pluck('users.id')->push($company->user_id)->filter()->unique()
            : collect([auth()->id()]);
        $companyDocIds = Document::whereIn('uploader', $companyUserIds)->select('id');
        
        // Prepare data arrays
        $months = [];
        $receiveTimes = [];
        $reviewTimes = [];
        $docsForwarded = [];
        $docsUploaded = [];
        
        // Generate data for each month in the range
        $current = $start->copy()->startOfMonth();
        while ($current <= $end) {
            $monthLabel = $current->format('M Y');
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Average time to receive (company-scoped)
            $receiveTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
                ->whereIn('document_id', $companyDocIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotNull('received_at')
                ->when($userId, fn($q) => $q->where('recipient_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('recipient.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->value('avg_time') ?? 0;
            
            // Average time to review (company-scoped)
            $reviewTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
                ->whereIn('document_id', $companyDocIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotNull('received_at')
                ->whereNotNull('updated_at')
                ->whereRaw('received_at <= updated_at')
                ->when($userId, fn($q) => $q->where('recipient_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('recipient.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->value('avg_time') ?? 0;
            
            // Documents forwarded (company-scoped)
            $forwardedCount = DocumentWorkflow::whereIn('document_id', $companyDocIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('sender_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('sender.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->count();
            
            // Documents uploaded (company-scoped)
            $uploadedCount = Document::whereIn('uploader', $companyUserIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('uploader', $userId))
                ->count();
            
            // Add data to arrays
            $months[] = $monthLabel;
            $receiveTimes[] = round($receiveTime, 2);
            $reviewTimes[] = round($reviewTime, 2);
            $docsForwarded[] = $forwardedCount;
            $docsUploaded[] = $uploadedCount;
            
            // Move to next month
            $current->addMonth();
        }
        
        return [
            'months' => $months,
            'receiveTimes' => $receiveTimes,
            'reviewTimes' => $reviewTimes,
            'docsForwarded' => $docsForwarded,
            'docsUploaded' => $docsUploaded
        ];
    }

    private function getMonthlyAnalyticsData($startDate, $endDate, $userId = null, $officeId = null)
    {
        // Convert strings to Carbon instances
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        // Resolve company for tenant scoping (owner OR employee)
        $company = CompanyAccount::where('user_id', auth()->id())->first()
            ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
        $companyUserIds = $company
            ? $company->employees()->pluck('users.id')->push($company->user_id)->filter()->unique()
            : collect([auth()->id()]);
        $companyDocIds = Document::whereIn('uploader', $companyUserIds)->select('id');
        
        // Prepare the result array
        $result = [];
        
        // Generate data for each month in the range
        $current = $start->copy()->startOfMonth();
        while ($current <= $end) {
            $monthLabel = $current->format('M Y');
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Get all the metrics for this month (all company-scoped)
            $receiveTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
                ->whereIn('document_id', $companyDocIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotNull('received_at')
                ->when($userId, fn($q) => $q->where('recipient_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('recipient.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->value('avg_time') ?? 0;
            
            $reviewTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
                ->whereIn('document_id', $companyDocIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotNull('received_at')
                ->whereNotNull('updated_at')
                ->whereRaw('received_at <= updated_at')
                ->when($userId, fn($q) => $q->where('recipient_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('recipient.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->value('avg_time') ?? 0;
            
            $forwardedCount = DocumentWorkflow::whereIn('document_id', $companyDocIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('sender_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('sender.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->count();
            
            $uploadedCount = Document::whereIn('uploader', $companyUserIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('uploader', $userId))
                ->count();
            
            // Store in the result array with the EXACT keys expected by the template
            $result[$monthLabel] = [
                'receive_time' => $this->formatTimeInMinutes($receiveTime),
                'review_time' => $this->formatTimeInMinutes($reviewTime),
                'docs_forwarded' => $forwardedCount,
                'docs_uploaded' => $uploadedCount
            ];
            
            $current->addMonth();
        }
        
        return $result;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'export_format' => 'nullable|string|in:pdf,excel,none',
        ]);

        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');
        $officeId = $request->input('office_id');
        $exportFormat = $request->input('export_format', 'none');

        // Ensure only one of userId or officeId is set
        if ($userId && $officeId) {
            return redirect()->back()->with('error', 'Please select either a user or an office, not both.');
        }

        // Resolve company for tenant scoping.
        $company = CompanyAccount::where('user_id', auth()->id())->first()
            ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
        $companyUserIds = $company
            ? $company->employees()->pluck('users.id')->push($company->user_id)->filter()->unique()
            : collect([auth()->id()]);
        $companyDocIds = Document::whereIn('uploader', $companyUserIds)->select('id');

        // Initialize data to avoid undefined variable error
        $data = collect();

        // Generate report based on type
        switch ($reportType) {
            case 'audit_history':
                $data = DocumentAudit::with(['document', 'user'])
                    ->whereIn('document_id', $companyDocIds)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->when($userId, fn($q) => $q->where('user_id', $userId))
                    ->when($officeId, fn($q) =>
                        $q->whereHas('user.offices', fn($o) =>
                            $o->where('offices.id', $officeId)
                        )
                    )
                    ->get();
                break;
            case 'company_performance':
                $data = DocumentWorkflow::whereIn('document_id', $companyDocIds)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->when($userId, fn($q) => $q->where('recipient_id', $userId))
                    ->when($officeId, fn($q) =>
                        $q->whereHas('recipient.offices', fn($o) =>
                            $o->where('offices.id', $officeId)
                        )
                    )
                    ->get();
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type selected.');
        }

        // Handle export formats
        if ($exportFormat !== 'none' && $data->count() > 0) {
            $reportTitle = ucfirst(str_replace('_', ' ', $reportType)) . ' Report';
            $dateRange = "From $startDate to $endDate";
            
            if ($exportFormat === 'pdf') {
                return $this->exportToPdf($data, $reportTitle, $dateRange, $reportType);
            } elseif ($exportFormat === 'excel') {
                return $this->exportToExcel($data, $reportTitle, $dateRange, $reportType);
            }
        }

        // Redirect back to index with generated data in session
        return redirect()->route('reports.index', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ])->with('generated_data', $data)
          ->with('generated_report_type', $reportType);
    }

    /**
     * Export report data to Excel
     * 
     * @param \Illuminate\Support\Collection $data
     * @param string $title
     * @param string $dateRange
     * @param string $reportType
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportToExcel($data, $title, $dateRange, $reportType)
    {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set metadata
        $spreadsheet->getProperties()
            ->setCreator(auth()->user()->first_name . ' ' . auth()->user()->last_name)
            ->setLastModifiedBy(auth()->user()->first_name . ' ' . auth()->user()->last_name)
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription("$title - $dateRange")
            ->setKeywords("reports, $reportType")
            ->setCategory('Reports');
        
        // Add header with title and date range
        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', $dateRange);
        $sheet->setCellValue('A3', 'Generated on: ' . now()->format('Y-m-d H:i:s'));
        
        // Apply styling to header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(10);
        
        // Add some space before data
        $rowIndex = 5;
        
        // Add column headers based on report type
        if ($reportType === 'audit_history') {
            $sheet->setCellValue('A' . $rowIndex, 'ID');
            $sheet->setCellValue('B' . $rowIndex, 'Document');
            $sheet->setCellValue('C' . $rowIndex, 'User');
            $sheet->setCellValue('D' . $rowIndex, 'Action');
            $sheet->setCellValue('E' . $rowIndex, 'Status');
            $sheet->setCellValue('F' . $rowIndex, 'Details');
            $sheet->setCellValue('G' . $rowIndex, 'Created At');
        } 
        elseif ($reportType === 'company_performance') {
            $sheet->setCellValue('A' . $rowIndex, 'ID');
            $sheet->setCellValue('B' . $rowIndex, 'Document ID');
            $sheet->setCellValue('C' . $rowIndex, 'Sender');
            $sheet->setCellValue('D' . $rowIndex, 'Recipient');
            $sheet->setCellValue('E' . $rowIndex, 'Status');
            $sheet->setCellValue('F' . $rowIndex, 'Created At');
            $sheet->setCellValue('G' . $rowIndex, 'Received At');
        }
        
        $sheet->getStyle('A'.$rowIndex.':G'.$rowIndex)->getFont()->setBold(true);
        $rowIndex++;
        
        // Add data rows
        foreach ($data as $item) {
            if ($reportType === 'audit_history') {
                $sheet->setCellValue('A' . $rowIndex, $item->id);
                $sheet->setCellValue('B' . $rowIndex, $item->document ? $item->document->title : "Document #{$item->document_id}");
                $sheet->setCellValue('C' . $rowIndex, $item->user ? ($item->user->first_name . ' ' . $item->user->last_name) : "User #{$item->user_id}");
                $sheet->setCellValue('D' . $rowIndex, ucfirst($item->action));
                $sheet->setCellValue('E' . $rowIndex, ucfirst($item->status));
                $sheet->setCellValue('F' . $rowIndex, $item->details);
                $sheet->setCellValue('G' . $rowIndex, $item->created_at->format('Y-m-d H:i:s'));
            } 
            elseif ($reportType === 'company_performance') {
                $sheet->setCellValue('A' . $rowIndex, $item->id);
                $sheet->setCellValue('B' . $rowIndex, $item->document_id);
                $sheet->setCellValue('C' . $rowIndex, $item->sender ? ($item->sender->first_name . ' ' . $item->sender->last_name) : "User #{$item->sender_id}");
                $sheet->setCellValue('D' . $rowIndex, $item->recipient ? ($item->recipient->first_name . ' ' . $item->recipient->last_name) : 
                    ($item->recipientOffice ? $item->recipientOffice->name : "Office #{$item->recipient_office}"));
                $sheet->setCellValue('E' . $rowIndex, ucfirst($item->status));
                $sheet->setCellValue('F' . $rowIndex, $item->created_at->format('Y-m-d H:i:s'));
                $sheet->setCellValue('G' . $rowIndex, $item->received_at ? Carbon::parse($item->received_at)->format('Y-m-d H:i:s') : 'N/A');
            }
            
            $rowIndex++;
        }
        
        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = Str::slug($title) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $tempFilePath = storage_path('app/public/temp/' . $fileName);
        
        // Ensure directory exists
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }
        
        $writer->save($tempFilePath);
        
        // Create a report record
        Report::create([
            'name' => $title,
            'description' => "Generated for date range: $dateRange",
            'type' => $reportType,
            'user_id' => auth()->id(),
            'generated_at' => now(),
            'data' => $data->toJson()
        ]);
        
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend();
    }
    
    /**
     * Export report data to PDF
     * 
     * @param \Illuminate\Support\Collection $data
     * @param string $title
     * @param string $dateRange
     * @param string $reportType
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($data, $title, $dateRange, $reportType)
    {
        $branding = $this->getCompanyBranding();
        $generatedBy = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $generatedAt = now()->format('F j, Y h:i A');

        $pdf = Pdf::loadView('reports.pdf_export', [
            'data'         => $data,
            'title'        => $title,
            'dateRange'    => $dateRange,
            'reportType'   => $reportType,
            'generatedBy'  => $generatedBy,
            'generatedAt'  => $generatedAt,
            'logoDataUri'  => $branding['logoDataUri'],
            'companyColor' => $branding['companyColor'],
            'companyName'  => $branding['companyName'],
        ])->setPaper('a4', 'landscape');
        $fileName = Str::slug($title) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        // Create a report record
        Report::create([
            'name' => $title,
            'description' => "Generated for date range: $dateRange",
            'type' => $reportType,
            'user_id' => auth()->id(),
            'generated_at' => now(),
            'data' => $data->toJson()
        ]);
        
        return $pdf->download($fileName);
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:financial,analytics,performance',
            'data' => 'required|json',
        ]);

        $report = Report::create([
            ...$validated,
            'user_id' => auth()->id(),
            'generated_at' => now(),
        ]);

        // Log the creation
        Log::create([
            'action' => 'create',
            'description' => "Created report: {$report->name}",
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Report generated successfully');
    }

    /**
     * Company storage and user performance dashboard
     * Provides comprehensive information for company admins
     */
    public function companyDashboard(Request $request)
    {
        // Get the logged in user's company
        $user = auth()->user();
        if (!$user->hasRole('company-admin')) {
            return redirect()->route('dashboard')->with('error', 'Access Denied: You are not authorized to access the company dashboard. This page is restricted to company administrators only.');
        }

        $company = $user->companies()->first();
        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'No company found for your account.');
        }
        
        // Date range filter - ensure we have valid dates
        $startDate = $request->filled('start_date') ? $request->input('start_date') : now()->subMonths(3)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->input('end_date') : now()->format('Y-m-d');
        
        // Check for export requests
        $exportFormat = $request->input('export_format');
        
        // Get company users
        $companyUsers = User::whereHas('companies', function($query) use ($company) {
            $query->where('company_accounts.id', $company->id);
        })->get();
        
        // Get company offices
        $companyOffices = Office::where('company_id', $company->id)->get();
        
        // Get storage metrics
        $storageMetrics = $this->getStorageMetrics($company->id);
        
        // Get user performance metrics
        $userPerformanceMetrics = $this->getUserPerformanceMetrics($companyUsers->pluck('id')->toArray(), $startDate, $endDate);
        
        // Get office performance metrics
        $officePerformanceMetrics = $this->getOfficePerformanceMetrics($companyOffices->pluck('id')->toArray(), $startDate, $endDate);
        
        // Get document category distribution
        $categoryDistribution = $this->getDocumentCategoryDistribution($company->id);
        
        // Get document status distribution
        $statusDistribution = $this->getDocumentStatusDistribution($company->id);
        
        // Get document volume trends
        $documentTrends = $this->getDocumentVolumeTrends($company->id, $startDate, $endDate);

        // NEW: Richer analytics data
        $periodComparison = $this->getPeriodComparison($company->id, $startDate, $endDate);
        $workflowCompletion = $this->getWorkflowCompletionMetrics($company->id, $startDate, $endDate);
        $workflowBottlenecks = $this->getWorkflowBottlenecks($company->id, $startDate, $endDate);
        $documentAging = $this->getDocumentAging($company->id);
        $recentActivity = $this->getRecentActivity($company->id, 10);
        
        // Handle exports if requested
        if ($exportFormat === 'pdf') {
            return $this->exportCompanyDashboardToPdf(
                $company, 
                $companyUsers, 
                $companyOffices, 
                $storageMetrics, 
                $userPerformanceMetrics, 
                $officePerformanceMetrics,
                $categoryDistribution,
                $statusDistribution,
                $documentTrends,
                $startDate,
                $endDate
            );
        } elseif ($request->has('export_table')) {
            $tableType = $request->input('export_table');
            return $this->exportCompanyTableToExcel(
                $tableType,
                $company->company_name,
                $userPerformanceMetrics,
                $officePerformanceMetrics,
                $storageMetrics,
                $startDate,
                $endDate
            );
        }
        
        return view('reports.company-dashboard', compact(
            'company',
            'companyUsers',
            'companyOffices',
            'storageMetrics',
            'userPerformanceMetrics',
            'officePerformanceMetrics',
            'categoryDistribution',
            'statusDistribution',
            'documentTrends',
            'startDate',
            'endDate',
            'periodComparison',
            'workflowCompletion',
            'workflowBottlenecks',
            'documentAging',
            'recentActivity'
        ));
    }
    
    /**
     * Export company dashboard to PDF
     */
    private function exportCompanyDashboardToPdf($company, $companyUsers, $companyOffices, $storageMetrics, 
                                                $userPerformanceMetrics, $officePerformanceMetrics, 
                                                $categoryDistribution, $statusDistribution, $documentTrends,
                                                $startDate, $endDate)
    {
        // Create PDF with the company dashboard data
        $branding = $this->getCompanyBranding($company);
        $pdf = PDF::loadView('reports.company_dashboard_pdf', [
            'company' => $company,
            'companyUsers' => $companyUsers,
            'companyOffices' => $companyOffices,
            'storageMetrics' => $storageMetrics,
            'userPerformanceMetrics' => $userPerformanceMetrics,
            'officePerformanceMetrics' => $officePerformanceMetrics,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('F j, Y h:i A'),
            'generatedBy' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'logoDataUri'  => $branding['logoDataUri'],
            'companyColor' => $branding['companyColor'],
        ]);
        
        $fileName = 'company_dashboard_' . $company->company_name . '_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        
        // Create a report record for tracking
        Report::create([
            'name' => $company->company_name . ' Dashboard Report',
            'description' => "Company performance dashboard for date range: $startDate to $endDate",
            'type' => 'company_dashboard',
            'user_id' => auth()->id(),
            'generated_at' => now(),
            'data' => json_encode([
                'company_id' => $company->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'metrics_summary' => [
                    'total_documents' => $storageMetrics['document_count'],
                    'total_attachments' => $storageMetrics['attachment_count'],
                    'total_storage' => $storageMetrics['formatted_total_size'],
                    'user_count' => count($companyUsers),
                    'office_count' => count($companyOffices),
                ]
            ])
        ]);
        
        return $pdf->download($fileName);
    }
    
    /**
     * Export specific company dashboard table to Excel
     */
    private function exportCompanyTableToExcel($tableType, $companyName, $userPerformanceMetrics, 
                                              $officePerformanceMetrics, $storageMetrics, 
                                              $startDate, $endDate)
    {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(auth()->user()->first_name . ' ' . auth()->user()->last_name)
            ->setLastModifiedBy(auth()->user()->first_name . ' ' . auth()->user()->last_name)
            ->setTitle($companyName . ' - ' . ucfirst($tableType) . ' Report')
            ->setSubject('Company Dashboard ' . ucfirst($tableType) . ' Report')
            ->setDescription("Report generated from company dashboard for date range $startDate to $endDate")
            ->setKeywords('company, dashboard, ' . $tableType)
            ->setCategory('Reports');
            
        // Add header
        $sheet->setCellValue('A1', $companyName . ' - ' . ucfirst($tableType) . ' Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $startDate . ' to ' . $endDate);
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('Y-m-d H:i:s'));
        
        // Style header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(10);
        
        // Add space before table
        $rowIndex = 5;
        
        // Create table based on type
        if ($tableType === 'user_performance') {
            // User Performance table headers
            $sheet->setCellValue('A'.$rowIndex, 'User');
            $sheet->setCellValue('B'.$rowIndex, 'Uploads');
            $sheet->setCellValue('C'.$rowIndex, 'Forwarded');
            $sheet->setCellValue('D'.$rowIndex, 'Processed');
            $sheet->setCellValue('E'.$rowIndex, 'Total Handled');
            $sheet->setCellValue('F'.$rowIndex, 'Avg Response Time');
            $sheet->setCellValue('G'.$rowIndex, 'Avg Processing Time');
            $sheet->setCellValue('H'.$rowIndex, 'Approval Rate (%)');
            $sheet->setCellValue('I'.$rowIndex, 'Performance Score');
            
            $sheet->getStyle('A'.$rowIndex.':I'.$rowIndex)->getFont()->setBold(true);
            $rowIndex++;
            
            // Add data rows
            foreach ($userPerformanceMetrics as $metric) {
                $sheet->setCellValue('A'.$rowIndex, $metric['user']->first_name . ' ' . $metric['user']->last_name);
                $sheet->setCellValue('B'.$rowIndex, $metric['uploads_count']);
                $sheet->setCellValue('C'.$rowIndex, $metric['forwarded_count']);
                $sheet->setCellValue('D'.$rowIndex, $metric['processed_count']);
                $sheet->setCellValue('E'.$rowIndex, $metric['total_documents_handled']);
                $sheet->setCellValue('F'.$rowIndex, $metric['avg_response_time']);
                $sheet->setCellValue('G'.$rowIndex, $metric['avg_processing_time']);
                $sheet->setCellValue('H'.$rowIndex, $metric['approval_rate']);
                $sheet->setCellValue('I'.$rowIndex, $metric['performance_score']);
                $rowIndex++;
            }
            
            // Auto-size columns
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
        } elseif ($tableType === 'office_performance') {
            // Office Performance table headers
            $sheet->setCellValue('A'.$rowIndex, 'Office');
            $sheet->setCellValue('B'.$rowIndex, 'Users');
            $sheet->setCellValue('C'.$rowIndex, 'Documents Originated');
            $sheet->setCellValue('D'.$rowIndex, 'Documents Received');
            $sheet->setCellValue('E'.$rowIndex, 'Workflows Processed');
            $sheet->setCellValue('F'.$rowIndex, 'Avg Processing Time');
            $sheet->setCellValue('G'.$rowIndex, 'Efficiency Score');
            
            $sheet->getStyle('A'.$rowIndex.':G'.$rowIndex)->getFont()->setBold(true);
            $rowIndex++;
            
            // Add data rows
            foreach ($officePerformanceMetrics as $metric) {
                $sheet->setCellValue('A'.$rowIndex, $metric['office']->name);
                $sheet->setCellValue('B'.$rowIndex, $metric['user_count']);
                $sheet->setCellValue('C'.$rowIndex, $metric['documents_originated']);
                $sheet->setCellValue('D'.$rowIndex, $metric['documents_received']);
                $sheet->setCellValue('E'.$rowIndex, $metric['workflows_processed']);
                $sheet->setCellValue('F'.$rowIndex, $metric['avg_processing_time']);
                $sheet->setCellValue('G'.$rowIndex, $metric['efficiency_score']);
                $rowIndex++;
            }
            
            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
        } elseif ($tableType === 'user_storage') {
            // User Storage table headers
            $sheet->setCellValue('A'.$rowIndex, 'User');
            $sheet->setCellValue('B'.$rowIndex, 'Documents');
            $sheet->setCellValue('C'.$rowIndex, 'Storage Used (Bytes)');
            $sheet->setCellValue('D'.$rowIndex, 'Storage Used (Formatted)');
            
            $sheet->getStyle('A'.$rowIndex.':D'.$rowIndex)->getFont()->setBold(true);
            $rowIndex++;
            
            // Add data rows
            foreach ($storageMetrics['user_storage'] as $userStorage) {
                $sheet->setCellValue('A'.$rowIndex, $userStorage['user']->first_name . ' ' . $userStorage['user']->last_name);
                $sheet->setCellValue('B'.$rowIndex, $userStorage['count']);
                $sheet->setCellValue('C'.$rowIndex, $userStorage['size']);
                $sheet->setCellValue('D'.$rowIndex, $userStorage['formatted_size']);
                $rowIndex++;
            }
            
            // Auto-size columns
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
        } elseif ($tableType === 'office_storage') {
            // Office Storage table headers
            $sheet->setCellValue('A'.$rowIndex, 'Office');
            $sheet->setCellValue('B'.$rowIndex, 'Documents');
            $sheet->setCellValue('C'.$rowIndex, 'Storage Used (Bytes)');
            $sheet->setCellValue('D'.$rowIndex, 'Storage Used (Formatted)');
            
            $sheet->getStyle('A'.$rowIndex.':D'.$rowIndex)->getFont()->setBold(true);
            $rowIndex++;
            
            // Add data rows
            foreach ($storageMetrics['office_storage'] as $officeStorage) {
                $sheet->setCellValue('A'.$rowIndex, $officeStorage['office']->name);
                $sheet->setCellValue('B'.$rowIndex, $officeStorage['count']);
                $sheet->setCellValue('C'.$rowIndex, $officeStorage['size']);
                $sheet->setCellValue('D'.$rowIndex, $officeStorage['formatted_size']);
                $rowIndex++;
            }
            
            // Auto-size columns
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = Str::slug($companyName . '-' . $tableType . '-report') . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $tempFilePath = storage_path('app/public/temp/' . $fileName);
        
        // Ensure directory exists
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }
        
        $writer->save($tempFilePath);
        
        // Create a report record
        Report::create([
            'name' => $companyName . ' - ' . ucfirst($tableType) . ' Report',
            'description' => "Generated for date range: $startDate to $endDate",
            'type' => 'company_' . $tableType,
            'user_id' => auth()->id(),
            'generated_at' => now(),
            'data' => json_encode([
                'table_type' => $tableType,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
        ]);
        
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend();
    }
    
    /**
     * Calculate storage usage metrics for a company
     */
    private function getStorageMetrics($companyId)
    {
        // Get all users in this company
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');
        
        // Get all documents uploaded by these users - Fixed column name from user_id to uploader
        $documents = Document::whereIn('uploader', $userIds)->get();
        $documentIds = $documents->pluck('id')->toArray();
        
        // Get all attachments for these documents
        $attachments = DocumentAttachment::whereIn('document_id', $documentIds)->get();
        
        // Calculate storage metrics
        $totalSize = 0;
        $documentSizes = [];
        $userStorage = [];
        $officeStorage = [];
        $uniqueOffices = [];
        
        // Calculate document sizes
        foreach ($documents as $document) {
            $docPath = $document->path ?? '';
            if ($docPath && Storage::disk('public')->exists($docPath)) {
                $size = Storage::disk('public')->size($docPath);
                $documentSizes[$document->id] = $size;
                $totalSize += $size;
                
                // Add to user storage
                $userId = $document->uploader; // Fixed column name from user_id to uploader
                if (!isset($userStorage[$userId])) {
                    $userStorage[$userId] = [
                        'size' => 0,
                        'count' => 0,
                        'user' => User::find($userId)
                    ];
                }
                $userStorage[$userId]['size'] += $size;
                $userStorage[$userId]['count']++;
                
                // Add to office storage - use document's direct from_office first, then transaction, then uploader's office
                $officeId = $document->from_office;
                if (!$officeId && $document->transaction && $document->transaction->from_office) {
                    $officeId = $document->transaction->from_office;
                }
                if (!$officeId && isset($userStorage[$userId]) && $userStorage[$userId]['user']) {
                    $uploaderOffice = $userStorage[$userId]['user']->offices()->first();
                    $officeId = $uploaderOffice ? $uploaderOffice->id : null;
                }
                if ($officeId) {
                    $uniqueOffices[$officeId] = true;
                    
                    if (!isset($officeStorage[$officeId])) {
                        $officeStorage[$officeId] = [
                            'size' => 0,
                            'count' => 0,
                            'office' => Office::find($officeId)
                        ];
                    }
                    $officeStorage[$officeId]['size'] += $size;
                    $officeStorage[$officeId]['count']++;
                }
            }
        }
        
        // Add attachment sizes
        foreach ($attachments as $attachment) {
            $attachPath = $attachment->path ?? '';
            if ($attachPath && Storage::disk('public')->exists($attachPath)) {
                $size = Storage::disk('public')->size($attachPath);
                $totalSize += $size;
                
                // Find the document and add to its size
                $docId = $attachment->document_id;
                if (isset($documentSizes[$docId])) {
                    $documentSizes[$docId] += $size;
                    
                    // Add to user storage
                    $document = Document::find($docId);
                    if ($document) {
                        $userId = $document->uploader;
                        if (isset($userStorage[$userId])) {
                            $userStorage[$userId]['size'] += $size;
                        }
                        
                        // Add to office storage
                        $officeId = $document->from_office;
                        if (!$officeId && $document->transaction && $document->transaction->from_office) {
                            $officeId = $document->transaction->from_office;
                        }
                        if ($officeId && isset($officeStorage[$officeId])) {
                            $officeStorage[$officeId]['size'] += $size;
                        }
                    }
                }
            }
        }
        
        // Format sizes to human-readable format
        foreach ($userStorage as &$user) {
            $user['formatted_size'] = $this->formatBytes($user['size']);
        }
        
        foreach ($officeStorage as &$office) {
            $office['formatted_size'] = $this->formatBytes($office['size']);
        }
        
        // Sort by size (largest first)
        usort($userStorage, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        usort($officeStorage, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        return [
            'total_size' => $totalSize,
            'formatted_total_size' => $this->formatBytes($totalSize),
            'document_count' => count($documents),
            'attachment_count' => count($attachments),
            'average_size' => count($documents) > 0 ? $totalSize / count($documents) : 0,
            'formatted_average_size' => count($documents) > 0 ? $this->formatBytes($totalSize / count($documents)) : '0 B',
            'user_storage' => $userStorage,
            'office_storage' => $officeStorage
        ];
    }
    
    /**
     * Calculate user performance metrics
     */
    private function getUserPerformanceMetrics($userIds, $startDate, $endDate)
    {
        $userMetrics = [];
        
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;
            
            // Documents uploaded - Fixed column name from user_id to uploader
            $uploadsCount = Document::where('uploader', $userId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Documents forwarded (sent workflows)
            $forwardedCount = DocumentWorkflow::where('sender_id', $userId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Documents processed (received workflows)
            $processedCount = DocumentWorkflow::where('recipient_id', $userId)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Average processing time for received documents
            $avgProcessingMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
                ->where('recipient_id', $userId)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('received_at')
                ->whereNotNull('updated_at')
                ->whereRaw('received_at <= updated_at')
                ->value('avg_time') ?? 0;
                
            // Average response time (time to first view a document)
            $avgResponseMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
                ->where('recipient_id', $userId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('received_at')
                ->value('avg_time') ?? 0;
                
            // Approval rate
            $totalReviewed = DocumentWorkflow::where('recipient_id', $userId)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $totalApproved = DocumentWorkflow::where('recipient_id', $userId)
                ->where('status', 'approved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $approvalRate = $totalReviewed > 0 ? ($totalApproved / $totalReviewed) * 100 : 0;
            
            $userMetrics[] = [
                'user' => $user,
                'uploads_count' => $uploadsCount,
                'forwarded_count' => $forwardedCount,
                'processed_count' => $processedCount,
                'total_documents_handled' => $uploadsCount + $forwardedCount + $processedCount,
                'avg_processing_time' => $this->formatTimeInMinutes($avgProcessingMinutes),
                'avg_response_time' => $this->formatTimeInMinutes($avgResponseMinutes),
                'avg_processing_minutes' => $avgProcessingMinutes, // Raw value for sorting
                'avg_response_minutes' => $avgResponseMinutes,    // Raw value for sorting
                'approval_rate' => round($approvalRate, 1),
                'performance_score' => $this->calculatePerformanceScore($avgResponseMinutes, $avgProcessingMinutes, $approvalRate, $processedCount)
            ];
        }
        
        // Sort by performance score (highest first)
        usort($userMetrics, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });
        
        return $userMetrics;
    }
    
    /**
     * Calculate office performance metrics
     */
    private function getOfficePerformanceMetrics($officeIds, $startDate, $endDate)
    {
        $officeMetrics = [];
        
        foreach ($officeIds as $officeId) {
            $office = Office::find($officeId);
            if (!$office) continue;
            
            // Get users in this office
            $officeUsers = User::whereHas('offices', function($query) use ($officeId) {
                $query->where('offices.id', $officeId);
            })->pluck('id');
            
            // If no users in this office, skip it
            if ($officeUsers->isEmpty()) continue;
            
            // Documents sent from this office
            $documentsOriginatedCount = Document::whereHas('transaction', function($query) use ($officeId) {
                $query->where('from_office', $officeId);
            })->whereBetween('created_at', [$startDate, $endDate])->count();
            
            // Documents sent to this office
            $documentsReceivedCount = Document::whereHas('transaction', function($query) use ($officeId) {
                $query->where('to_office', $officeId);
            })->whereBetween('created_at', [$startDate, $endDate])->count();
            
            // Workflows processed by this office's users
            $workflowsProcessedCount = DocumentWorkflow::whereIn('recipient_id', $officeUsers)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Average processing time for this office
            $avgProcessingMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
                ->whereIn('recipient_id', $officeUsers)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('received_at')
                ->whereNotNull('updated_at')
                ->whereRaw('received_at <= updated_at')
                ->value('avg_time') ?? 0;
                
            $officeMetrics[] = [
                'office' => $office,
                'documents_originated' => $documentsOriginatedCount,
                'documents_received' => $documentsReceivedCount,
                'workflows_processed' => $workflowsProcessedCount,
                'avg_processing_time' => $this->formatTimeInMinutes($avgProcessingMinutes),
                'avg_processing_minutes' => $avgProcessingMinutes, // Raw value for sorting
                'user_count' => $officeUsers->count(),
                'efficiency_score' => $this->calculateOfficeEfficiencyScore($avgProcessingMinutes, $workflowsProcessedCount, $officeUsers->count())
            ];
        }
        
        // Sort by efficiency score (highest first)
        usort($officeMetrics, function($a, $b) {
            return $b['efficiency_score'] <=> $a['efficiency_score'];
        });
        
        return $officeMetrics;
    }
    
    /**
     * Get document category distribution
     */
    private function getDocumentCategoryDistribution($companyId)
    {
        // Get users in this company
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');
        
        // Get documents uploaded by these users - Fixed column name from user_id to uploader
        $documents = Document::whereIn('uploader', $userIds)->pluck('id');
        
        // Get category counts using the pivot table
        $categoryCounts = DB::table('document_category')
            ->join('document_categories', 'document_category.category_id', '=', 'document_categories.id')
            ->whereIn('document_category.doc_id', $documents)
            ->select('document_categories.category', DB::raw('count(*) as count'))
            ->groupBy('document_categories.category')
            ->orderBy('count', 'desc')
            ->get();
            
        return $categoryCounts;
    }
    
    /**
     * Get document status distribution
     */
    private function getDocumentStatusDistribution($companyId)
    {
        // Get users in this company
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');
        
        // Get status counts - Fixed column name from user_id to uploader
        $statusCounts = DB::table('document_status')
            ->join('documents', 'document_status.doc_id', '=', 'documents.id')
            ->whereIn('documents.uploader', $userIds)
            ->select('document_status.status', DB::raw('count(*) as count'))
            ->groupBy('document_status.status')
            ->orderBy('count', 'desc')
            ->get();
            
        return $statusCounts;
    }
    
    /**
     * Get document volume trends over time
     */
    private function getDocumentVolumeTrends($companyId, $startDate, $endDate)
    {
        // Get users in this company
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');
        
        // Convert strings to Carbon instances
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Prepare data arrays
        $months = [];
        $documentCounts = [];
        $workflowCounts = [];
        
        // Generate data for each month in the range
        $current = $start->copy()->startOfMonth();
        while ($current <= $end) {
            $monthLabel = $current->format('M Y');
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Documents created - Fixed column name from user_id to uploader
            $docsCount = Document::whereIn('uploader', $userIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            // Workflows created
            $workflowCount = DocumentWorkflow::whereIn('sender_id', $userIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $months[] = $monthLabel;
            $documentCounts[] = $docsCount;
            $workflowCounts[] = $workflowCount;
            
            $current->addMonth();
        }
        
        return [
            'months' => $months,
            'document_counts' => $documentCounts,
            'workflow_counts' => $workflowCounts
        ];
    }
    
    /**
     * Calculate performance score for a user
     * Higher score is better
     */
    private function calculatePerformanceScore($responseMinutes, $processingMinutes, $approvalRate, $documentsProcessed)
    {
        // Normalize values (lower time is better)
        $responseScore = max(0, 100 - min(100, $responseMinutes / 10)); // 10 mins → 99, 1000 mins → 0
        $processingScore = max(0, 100 - min(100, $processingMinutes / 20)); // 20 mins → 99, 2000 mins → 0
        
        // Volume bonus (more is better, but with diminishing returns)
        $volumeBonus = min(100, sqrt($documentsProcessed) * 10); // 1 doc → 10, 100 docs → 100
        
        // Combine scores with weights
        $score = ($responseScore * 0.3) +        // 30% weight on response time
                ($processingScore * 0.3) +       // 30% weight on processing time
                ($approvalRate * 0.2) +          // 20% weight on approval rate
                ($volumeBonus * 0.2);            // 20% weight on volume
                
        return round($score, 1);
    }
    
    /**
     * Calculate efficiency score for an office
     * Higher score is better
     */
    private function calculateOfficeEfficiencyScore($processingMinutes, $workflowsProcessed, $userCount)
    {
        // Avoid division by zero
        if ($userCount == 0) return 0;
        
        // Normalize processing time (lower is better)
        $processingScore = max(0, 100 - min(100, $processingMinutes / 20)); // 20 mins → 99, 2000 mins → 0
        
        // Workflows per user (more is better, with diminishing returns)
        $workflowsPerUser = $workflowsProcessed / $userCount;
        $volumeScore = min(100, sqrt($workflowsPerUser) * 25); // 1 per user → 25, 16 per user → 100
        
        // Combine scores with weights
        $score = ($processingScore * 0.6) +     // 60% weight on processing time
                 ($volumeScore * 0.4);          // 40% weight on volume per user
                 
        return round($score, 1);
    }
    
    /**
     * Get workflow bottleneck analysis - identifies where documents get stuck
     */
    private function getWorkflowBottlenecks($companyId, $startDate, $endDate)
    {
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');

        // Pending workflows grouped by recipient
        $pendingByUser = DocumentWorkflow::whereIn('recipient_id', $userIds)
            ->where('status', 'pending')
            ->select('recipient_id', DB::raw('COUNT(*) as pending_count'), DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, NOW())) as avg_wait_hours'))
            ->groupBy('recipient_id')
            ->orderByDesc('pending_count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $user = User::find($item->recipient_id);
                return [
                    'user' => $user,
                    'pending_count' => $item->pending_count,
                    'avg_wait_hours' => round($item->avg_wait_hours, 1),
                    'avg_wait_formatted' => $this->formatHoursToReadable($item->avg_wait_hours),
                ];
            });

        // Pending workflows grouped by office
        $officeIds = Office::where('company_id', $companyId)->pluck('id');
        $pendingByOffice = DocumentWorkflow::whereIn('recipient_office', $officeIds)
            ->where('status', 'pending')
            ->select('recipient_office', DB::raw('COUNT(*) as pending_count'), DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, NOW())) as avg_wait_hours'))
            ->groupBy('recipient_office')
            ->orderByDesc('pending_count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $office = Office::find($item->recipient_office);
                return [
                    'office' => $office,
                    'pending_count' => $item->pending_count,
                    'avg_wait_hours' => round($item->avg_wait_hours, 1),
                    'avg_wait_formatted' => $this->formatHoursToReadable($item->avg_wait_hours),
                ];
            });

        // Longest pending individual workflows
        $oldestPending = DocumentWorkflow::whereIn('sender_id', $userIds)
            ->orWhereIn('recipient_id', $userIds)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get()
            ->map(function($wf) {
                $hours = now()->diffInHours($wf->created_at);
                return [
                    'workflow' => $wf,
                    'document' => $wf->document,
                    'sender' => $wf->sender,
                    'recipient' => $wf->recipient,
                    'recipient_office' => $wf->recipientOffice,
                    'waiting_hours' => $hours,
                    'waiting_formatted' => $this->formatHoursToReadable($hours),
                    'created_at' => $wf->created_at->format('M d, Y H:i'),
                ];
            });

        return [
            'pending_by_user' => $pendingByUser,
            'pending_by_office' => $pendingByOffice,
            'oldest_pending' => $oldestPending,
            'total_pending' => DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })->where('status', 'pending')->count(),
        ];
    }

    /**
     * Get document aging analysis - shows age distribution of pending documents
     */
    private function getDocumentAging($companyId)
    {
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');

        $now = now();

        // Count pending workflows in time buckets
        $lessThan24h = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->where('status', 'pending')
            ->where('created_at', '>=', $now->copy()->subDay())
            ->count();

        $oneToThreeDays = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->where('status', 'pending')
            ->whereBetween('created_at', [$now->copy()->subDays(3), $now->copy()->subDay()])
            ->count();

        $threeToSevenDays = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->where('status', 'pending')
            ->whereBetween('created_at', [$now->copy()->subDays(7), $now->copy()->subDays(3)])
            ->count();

        $sevenToThirtyDays = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->where('status', 'pending')
            ->whereBetween('created_at', [$now->copy()->subDays(30), $now->copy()->subDays(7)])
            ->count();

        $overThirtyDays = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->where('status', 'pending')
            ->where('created_at', '<', $now->copy()->subDays(30))
            ->count();

        return [
            'buckets' => [
                ['label' => '< 24 hours', 'count' => $lessThan24h, 'color' => '#10B981'],
                ['label' => '1-3 days', 'count' => $oneToThreeDays, 'color' => '#3B82F6'],
                ['label' => '3-7 days', 'count' => $threeToSevenDays, 'color' => '#F59E0B'],
                ['label' => '7-30 days', 'count' => $sevenToThirtyDays, 'color' => '#F97316'],
                ['label' => '> 30 days', 'count' => $overThirtyDays, 'color' => '#EF4444'],
            ],
            'total_pending' => $lessThan24h + $oneToThreeDays + $threeToSevenDays + $sevenToThirtyDays + $overThirtyDays,
            'critical_count' => $sevenToThirtyDays + $overThirtyDays,
        ];
    }

    /**
     * Get period-over-period comparison metrics
     */
    private function getPeriodComparison($companyId, $startDate, $endDate)
    {
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $periodDays = $start->diffInDays($end);

        // Previous period
        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($periodDays);

        // Current period metrics
        $currentDocs = Document::whereIn('uploader', $userIds)->whereBetween('created_at', [$startDate, $endDate])->count();
        $currentWorkflows = DocumentWorkflow::whereIn('sender_id', $userIds)->whereBetween('created_at', [$startDate, $endDate])->count();
        $currentProcessed = DocumentWorkflow::whereIn('recipient_id', $userIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $currentAvgProcessing = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
            ->whereIn('recipient_id', $userIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at')->whereNotNull('updated_at')
            ->whereRaw('received_at <= updated_at')
            ->value('avg_time') ?? 0;

        // Previous period metrics
        $prevDocs = Document::whereIn('uploader', $userIds)->whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $prevWorkflows = DocumentWorkflow::whereIn('sender_id', $userIds)->whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $prevProcessed = DocumentWorkflow::whereIn('recipient_id', $userIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $prevAvgProcessing = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
            ->whereIn('recipient_id', $userIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->whereNotNull('received_at')->whereNotNull('updated_at')
            ->whereRaw('received_at <= updated_at')
            ->value('avg_time') ?? 0;

        return [
            'documents' => [
                'current' => $currentDocs,
                'previous' => $prevDocs,
                'change' => $prevDocs > 0 ? round((($currentDocs - $prevDocs) / $prevDocs) * 100, 1) : ($currentDocs > 0 ? 100 : 0),
            ],
            'workflows' => [
                'current' => $currentWorkflows,
                'previous' => $prevWorkflows,
                'change' => $prevWorkflows > 0 ? round((($currentWorkflows - $prevWorkflows) / $prevWorkflows) * 100, 1) : ($currentWorkflows > 0 ? 100 : 0),
            ],
            'processed' => [
                'current' => $currentProcessed,
                'previous' => $prevProcessed,
                'change' => $prevProcessed > 0 ? round((($currentProcessed - $prevProcessed) / $prevProcessed) * 100, 1) : ($currentProcessed > 0 ? 100 : 0),
            ],
            'avg_processing' => [
                'current' => round($currentAvgProcessing, 1),
                'previous' => round($prevAvgProcessing, 1),
                'change' => $prevAvgProcessing > 0 ? round((($currentAvgProcessing - $prevAvgProcessing) / $prevAvgProcessing) * 100, 1) : 0,
                'current_formatted' => $this->formatTimeInMinutes($currentAvgProcessing),
                'previous_formatted' => $this->formatTimeInMinutes($prevAvgProcessing),
            ],
            'period_label' => $start->format('M d') . ' - ' . $end->format('M d, Y'),
            'prev_period_label' => $prevStart->format('M d') . ' - ' . $prevEnd->format('M d, Y'),
        ];
    }

    /**
     * Get workflow completion rate and throughput metrics
     */
    private function getWorkflowCompletionMetrics($companyId, $startDate, $endDate)
    {
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');

        $totalWorkflows = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedWorkflows = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->whereIn('status', ['approved', 'rejected', 'received'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $approvedWorkflows = DocumentWorkflow::whereIn('recipient_id', $userIds)
            ->where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $rejectedWorkflows = DocumentWorkflow::whereIn('recipient_id', $userIds)
            ->where('status', 'rejected')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $pendingWorkflows = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completionRate = $totalWorkflows > 0 ? round(($completedWorkflows / $totalWorkflows) * 100, 1) : 0;
        $approvalRate = ($approvedWorkflows + $rejectedWorkflows) > 0 
            ? round(($approvedWorkflows / ($approvedWorkflows + $rejectedWorkflows)) * 100, 1) : 0;

        // Average end-to-end turnaround (created to final status)
        $avgTurnaround = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->whereIn('recipient_id', $userIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereRaw('created_at < updated_at')
            ->value('avg_hours') ?? 0;

        return [
            'total' => $totalWorkflows,
            'completed' => $completedWorkflows,
            'approved' => $approvedWorkflows,
            'rejected' => $rejectedWorkflows,
            'pending' => $pendingWorkflows,
            'completion_rate' => $completionRate,
            'approval_rate' => $approvalRate,
            'avg_turnaround_hours' => round($avgTurnaround, 1),
            'avg_turnaround_formatted' => $this->formatHoursToReadable($avgTurnaround),
        ];
    }

    /**
     * Get recent activity feed for the company
     */
    private function getRecentActivity($companyId, $limit = 10)
    {
        $userIds = User::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_accounts.id', $companyId);
        })->pluck('id');

        $recentWorkflows = DocumentWorkflow::where(function($q) use ($userIds) {
                $q->whereIn('sender_id', $userIds)->orWhereIn('recipient_id', $userIds);
            })
            ->with(['sender', 'recipient', 'document'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($wf) {
                return [
                    'type' => 'workflow',
                    'status' => $wf->status,
                    'document_title' => $wf->document->title ?? 'Unknown Document',
                    'sender_name' => $wf->sender ? ($wf->sender->first_name . ' ' . $wf->sender->last_name) : 'Unknown',
                    'recipient_name' => $wf->recipient ? ($wf->recipient->first_name . ' ' . $wf->recipient->last_name) : ($wf->recipientOffice->name ?? 'Unknown'),
                    'timestamp' => $wf->updated_at,
                    'time_ago' => $wf->updated_at->diffForHumans(),
                ];
            });

        return $recentWorkflows;
    }

    /**
     * Format hours to a human-readable format
     */
    private function formatHoursToReadable($hours)
    {
        if ($hours <= 0) return '0 hours';
        
        if ($hours < 1) {
            $minutes = round($hours * 60);
            return $minutes . ' min' . ($minutes != 1 ? 's' : '');
        }
        
        if ($hours < 24) {
            $h = floor($hours);
            $m = round(($hours - $h) * 60);
            return $h . 'h ' . $m . 'm';
        }

        $days = floor($hours / 24);
        $remainingHours = round($hours - ($days * 24));
        return $days . 'd ' . $remainingHours . 'h';
    }

    /**
     * Format bytes to human-readable format
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

    /**
     * Office lead dashboard for office-specific statistics
     * Provides comprehensive information for office leads
     */
    public function officeLeadDashboard(Request $request)
    {
        // Get the logged in user
        $user = auth()->user();
        
        // Check if user is an office lead
        $office = Office::where('office_lead', $user->id)->first();
        if (!$office) {
            return redirect()->route('dashboard')->with('error', 'You are not assigned as an office lead.');
        }
        
        // Date range filter
        $startDate = $request->filled('start_date') ? $request->input('start_date') : now()->subMonths(1)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->input('end_date') : now()->format('Y-m-d');
        
        // Check for export requests
        $exportFormat = $request->input('export_format');
        
        // Get office members
        $officeMembers = $office->users()->get();
        $memberIds = $officeMembers->pluck('id')->toArray();
        
        // Get office document statistics
        $documentsUploaded = Document::whereIn('uploader', $memberIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $documentsUploadedToday = Document::whereIn('uploader', $memberIds)
            ->whereDate('created_at', today())
            ->count();
        
        $pendingWorkflows = DocumentWorkflow::whereIn('sender_id', $memberIds)
            ->orWhereIn('recipient_id', $memberIds)
            ->where('status', 'pending')
            ->count();
        
        // Get document workflow statistics
        $workflowStats = $this->getOfficeWorkflowStatistics($office->id, $memberIds, $startDate, $endDate);
        
        // Get member performance metrics
        $memberPerformanceMetrics = $this->getMemberPerformanceMetrics($memberIds, $startDate, $endDate);
        
        // Get document category distribution for this office
        $categoryDistribution = $this->getOfficeDocumentCategoryDistribution($memberIds);
        
        // Get document status distribution for this office
        $statusDistribution = $this->getOfficeDocumentStatusDistribution($memberIds);
        
        // Get document volume trends for this office
        $documentTrends = $this->getOfficeDocumentVolumeTrends($memberIds, $startDate, $endDate);
        
        // Handle exports if requested
        if ($exportFormat === 'pdf') {
            return $this->exportOfficeLeadDashboardToPdf(
                $office, 
                $officeMembers,
                $documentsUploaded,
                $documentsUploadedToday,
                $pendingWorkflows,
                $workflowStats,
                $memberPerformanceMetrics,
                $categoryDistribution,
                $statusDistribution,
                $documentTrends,
                $startDate,
                $endDate
            );
        }
        
        return view('reports.office-lead-dashboard', compact(
            'office',
            'officeMembers',
            'documentsUploaded',
            'documentsUploadedToday',
            'pendingWorkflows',
            'workflowStats',
            'memberPerformanceMetrics',
            'categoryDistribution',
            'statusDistribution',
            'documentTrends',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export office lead dashboard to PDF
     */
    private function exportOfficeLeadDashboardToPdf($office, $officeMembers, $documentsUploaded, $documentsUploadedToday,
                                                   $pendingWorkflows, $workflowStats, $memberPerformanceMetrics,
                                                   $categoryDistribution, $statusDistribution, $documentTrends,
                                                   $startDate, $endDate)
    {
        // Create PDF with the office dashboard data
        $branding = $this->getCompanyBranding();
        $pdf = PDF::loadView('reports.office_lead_dashboard_pdf', [
            'office' => $office,
            'officeMembers' => $officeMembers,
            'documentsUploaded' => $documentsUploaded,
            'documentsUploadedToday' => $documentsUploadedToday,
            'pendingWorkflows' => $pendingWorkflows,
            'workflowStats' => $workflowStats,
            'memberPerformanceMetrics' => $memberPerformanceMetrics,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('F j, Y h:i A'),
            'generatedBy' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'logoDataUri'  => $branding['logoDataUri'],
            'companyColor' => $branding['companyColor'],
        ]);
        
        $fileName = 'office_lead_dashboard_' . $office->name . '_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        
        // Create a report record for tracking
        Report::create([
            'name' => $office->name . ' Office Dashboard Report',
            'description' => "Office performance dashboard for date range: $startDate to $endDate",
            'type' => 'office_dashboard',
            'user_id' => auth()->id(),
            'generated_at' => now(),
            'data' => json_encode([
                'office_id' => $office->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'metrics_summary' => [
                    'total_documents' => $documentsUploaded,
                    'today_documents' => $documentsUploadedToday,
                    'pending_workflows' => $pendingWorkflows,
                    'member_count' => count($officeMembers),
                ]
            ])
        ]);
        
        return $pdf->download($fileName);
    }

    /**
     * Get workflow statistics for an office
     */
    private function getOfficeWorkflowStatistics($officeId, $memberIds, $startDate, $endDate)
    {
        // Calculate workflows sent from this office
        $workflowsSent = DocumentWorkflow::whereIn('sender_id', $memberIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Calculate workflows received by this office
        $workflowsReceived = DocumentWorkflow::whereIn('recipient_id', $memberIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        // Calculate workflows targeting the office as a whole
        $workflowsToOffice = DocumentWorkflow::where('recipient_office', $officeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Calculate workflows approved
        $workflowsApproved = DocumentWorkflow::whereIn('recipient_id', $memberIds)
            ->where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Calculate workflows rejected
        $workflowsRejected = DocumentWorkflow::whereIn('recipient_id', $memberIds)
            ->where('status', 'rejected')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Calculate average processing time
        $avgProcessingTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
            ->whereIn('recipient_id', $memberIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at')
            ->whereNotNull('updated_at')
            ->whereRaw('received_at <= updated_at')
            ->value('avg_time') ?? 0;
        
        return [
            'workflows_sent' => $workflowsSent,
            'workflows_received' => $workflowsReceived,
            'workflows_to_office' => $workflowsToOffice,
            'workflows_approved' => $workflowsApproved,
            'workflows_rejected' => $workflowsRejected,
            'avg_processing_time' => $this->formatTimeInMinutes($avgProcessingTime),
            'avg_processing_minutes' => $avgProcessingTime,
            'approval_rate' => ($workflowsApproved + $workflowsRejected > 0) 
                ? round(($workflowsApproved / ($workflowsApproved + $workflowsRejected)) * 100, 1) 
                : 0
        ];
    }

    /**
     * Get performance metrics for office members
     */
    private function getMemberPerformanceMetrics($memberIds, $startDate, $endDate)
    {
        $memberMetrics = [];
        
        foreach ($memberIds as $memberId) {
            $member = User::find($memberId);
            if (!$member) continue;
            
            // Documents uploaded
            $uploadsCount = Document::where('uploader', $memberId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Documents forwarded (sent workflows)
            $forwardedCount = DocumentWorkflow::where('sender_id', $memberId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Documents processed (received workflows)
            $processedCount = DocumentWorkflow::where('recipient_id', $memberId)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Average processing time for received documents
            $avgProcessingMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
                ->where('recipient_id', $memberId)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('received_at')
                ->whereNotNull('updated_at')
                ->whereRaw('received_at <= updated_at')
                ->value('avg_time') ?? 0;
                
            // Average response time (time to first view a document)
            $avgResponseMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
                ->where('recipient_id', $memberId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('received_at')
                ->value('avg_time') ?? 0;
                
            // Approval rate
            $totalReviewed = DocumentWorkflow::where('recipient_id', $memberId)
                ->whereIn('status', ['approved', 'rejected'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $totalApproved = DocumentWorkflow::where('recipient_id', $memberId)
                ->where('status', 'approved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $approvalRate = $totalReviewed > 0 ? ($totalApproved / $totalReviewed) * 100 : 0;
            
            $memberMetrics[] = [
                'member' => $member,
                'uploads_count' => $uploadsCount,
                'forwarded_count' => $forwardedCount,
                'processed_count' => $processedCount,
                'total_documents_handled' => $uploadsCount + $forwardedCount + $processedCount,
                'avg_processing_time' => $this->formatTimeInMinutes($avgProcessingMinutes),
                'avg_response_time' => $this->formatTimeInMinutes($avgResponseMinutes),
                'avg_processing_minutes' => $avgProcessingMinutes, // Raw value for sorting
                'avg_response_minutes' => $avgResponseMinutes,    // Raw value for sorting
                'approval_rate' => round($approvalRate, 1),
                'performance_score' => $this->calculatePerformanceScore($avgResponseMinutes, $avgProcessingMinutes, $approvalRate, $processedCount)
            ];
        }
        
        // Sort by performance score (highest first)
        usort($memberMetrics, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });
        
        return $memberMetrics;
    }

    /**
     * Get document category distribution for an office
     */
    private function getOfficeDocumentCategoryDistribution($memberIds)
    {
        // Get documents uploaded by office members
        $documents = Document::whereIn('uploader', $memberIds)->pluck('id');
        
        // Get category counts using the pivot table
        $categoryCounts = DB::table('document_category')
            ->join('document_categories', 'document_category.category_id', '=', 'document_categories.id')
            ->whereIn('document_category.doc_id', $documents)
            ->select('document_categories.category', DB::raw('count(*) as count'))
            ->groupBy('document_categories.category')
            ->orderBy('count', 'desc')
            ->get();
            
        return $categoryCounts;
    }

    /**
     * Get document status distribution for an office
     */
    private function getOfficeDocumentStatusDistribution($memberIds)
    {
        // Get status counts
        $statusCounts = DB::table('document_status')
            ->join('documents', 'document_status.doc_id', '=', 'documents.id')
            ->whereIn('documents.uploader', $memberIds)
            ->select('document_status.status', DB::raw('count(*) as count'))
            ->groupBy('document_status.status')
            ->orderBy('count', 'desc')
            ->get();
            
        return $statusCounts;
    }

    /**
     * Get document volume trends over time for an office
     */
    private function getOfficeDocumentVolumeTrends($memberIds, $startDate, $endDate)
    {
        // Convert strings to Carbon instances
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Prepare data arrays
        $months = [];
        $documentCounts = [];
        $workflowCounts = [];
        
        // Generate data for each month in the range
        $current = $start->copy()->startOfMonth();
        while ($current <= $end) {
            $monthLabel = $current->format('M Y');
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Documents created
            $docsCount = Document::whereIn('uploader', $memberIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            // Workflows created
            $workflowCount = DocumentWorkflow::whereIn('sender_id', $memberIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $months[] = $monthLabel;
            $documentCounts[] = $docsCount;
            $workflowCounts[] = $workflowCount;
            
            $current->addMonth();
        }
        
        return [
            'months' => $months,
            'document_counts' => $documentCounts,
            'workflow_counts' => $workflowCounts
        ];
    }

    /**
     * Office user dashboard view - accessible to all office members
     * Similar to officeLeadDashboard but without requiring office lead status
     */
    public function officeUserDashboard(Request $request)
    {
        // Get the logged in user
        $user = auth()->user();
        
        // Get the user's office(s)
        $offices = $user->offices;
        if ($offices->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'You are not assigned to any office.');
        }
        
        // Use the first office the user belongs to
        $office = $offices->first();
        
        // Date range filter
        $startDate = $request->filled('start_date') ? $request->input('start_date') : now()->subMonths(1)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->input('end_date') : now()->format('Y-m-d');
        
        // Check for export requests
        $exportFormat = $request->input('export_format');
        
        // Get office members
        $officeMembers = $office->users()->get();
        $memberIds = $officeMembers->pluck('id')->toArray();
        
        // Get office document statistics
        $documentsUploaded = Document::whereIn('uploader', $memberIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $documentsUploadedToday = Document::whereIn('uploader', $memberIds)
            ->whereDate('created_at', today())
            ->count();
        
        $pendingWorkflows = DocumentWorkflow::whereIn('sender_id', $memberIds)
            ->orWhereIn('recipient_id', $memberIds)
            ->where('status', 'pending')
            ->count();
        
        // Get document workflow statistics
        $workflowStats = $this->getOfficeWorkflowStatistics($office->id, $memberIds, $startDate, $endDate);
        
        // Get member performance metrics
        $memberPerformanceMetrics = $this->getMemberPerformanceMetrics($memberIds, $startDate, $endDate);
        
        // Get document category distribution for this office
        $categoryDistribution = $this->getOfficeDocumentCategoryDistribution($memberIds);
        
        // Get document status distribution for this office
        $statusDistribution = $this->getOfficeDocumentStatusDistribution($memberIds);
        
        // Get document volume trends for this office
        $documentTrends = $this->getOfficeDocumentVolumeTrends($memberIds, $startDate, $endDate);
        
        // Handle exports if requested
        if ($exportFormat === 'pdf') {
            return $this->exportOfficeLeadDashboardToPdf(
                $office, 
                $officeMembers,
                $documentsUploaded,
                $documentsUploadedToday,
                $pendingWorkflows,
                $workflowStats,
                $memberPerformanceMetrics,
                $categoryDistribution,
                $statusDistribution,
                $documentTrends,
                $startDate,
                $endDate
            );
        }
        
        return view('reports.office-lead-dashboard', compact(
            'office',
            'officeMembers',
            'documentsUploaded',
            'documentsUploadedToday',
            'pendingWorkflows',
            'workflowStats',
            'memberPerformanceMetrics',
            'categoryDistribution',
            'statusDistribution',
            'documentTrends',
            'startDate',
            'endDate'
        ));
    }
}