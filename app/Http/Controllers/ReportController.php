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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('user')
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
        $users = User::all();

        return view('reports.index', compact('reports', 'users'));
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
            $pdf = Pdf::loadView('reports.pdf', compact('report'))
                ->setPaper('a4', 'landscape');
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
    public function analytics(Request $request)  // Remove the ": View" return type declaration
    {
        $startDate = $request->input('start_date') ?: now()->subMonth()->format('Y-m-d');
        $endDate = $request->input('end_date') ?: now()->format('Y-m-d');
        $userId = $request->input('user_id');
        $officeId = $request->input('office_id');
        $displayType = $request->input('display_type', 'table'); // Default to table view

        if ($displayType === 'pdf') {
            return $this->exportAnalyticsToPdf($startDate, $endDate, $userId, $officeId);
        }

        $averageTimeToReceiveMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at') // Ensure received_at is not null
            ->when($userId, fn($q) => $q->where('recipient_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('recipient.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->value('avg_time') ?? 0;

        $averageTimeToReviewMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('received_at') // Ensure received_at is not null
            ->whereNotNull('updated_at') // Ensure updated_at is not null
            ->whereRaw('received_at <= updated_at') // Ensure received_at is before updated_at
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

        $averageDocsForwarded = DocumentWorkflow::whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('sender_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('sender.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->count();

        $documentsUploaded = Document::whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->count();

        // Get collections for dropdowns
        $company = CompanyAccount::where('user_id', auth()->id())->first();
        if (!$company) {
            \Log::warning('No company found for user: ' . auth()->id());
            // Handle the case where no company exists
        }

        $users = $company ? $company->employees()->paginate(5) : collect();
        $offices = $company ? Office::where('company_id', $company->id)->get() : collect();

        // Get additional data for charts - monthly trends
        $monthlyData = $this->getMonthlyAnalyticsChartData($startDate, $endDate, $userId, $officeId);

        return view('reports.analytics', compact(
            'averageTimeToReceive',
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
            'monthlyData'
        ));
    }

    /**
     * Export analytics data to PDF
     */
    private function exportAnalyticsToPdf($startDate, $endDate, $userId = null, $officeId = null)
    {
        // Get the same analytics data as in the analytics method
        $averageTimeToReceiveMinutes = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
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
        
        $averageDocsForwarded = DocumentWorkflow::whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('sender_id', $userId))
            ->when($officeId, fn($q) =>
                $q->whereHas('sender.offices', fn($o) =>
                    $o->where('offices.id', $officeId)
                )
            )
            ->count();

        $documentsUploaded = Document::whereBetween('created_at', [$startDate, $endDate])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
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
        
        // Get monthly trend data for the chart
        $monthlyData = $this->getMonthlyAnalyticsData($startDate, $endDate, $userId, $officeId);
        
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
            'monthlyData' => $monthlyData,  // Make sure this is passed
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ])->setPaper('a4', 'landscape');
        
        $fileName = 'analytics_report_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $pdf->download($fileName);
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
            
            // Average time to receive
            $receiveTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotNull('received_at')
                ->when($userId, fn($q) => $q->where('recipient_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('recipient.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->value('avg_time') ?? 0;
            
            // Average time to review
            $reviewTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, received_at, updated_at)) as avg_time')
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
            
            // Documents forwarded
            $forwardedCount = DocumentWorkflow::whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('sender_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('sender.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->count();
            
            // Documents uploaded
            $uploadedCount = Document::whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('user_id', $userId))
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
        
        // Prepare the result array
        $result = [];
        
        // Generate data for each month in the range
        $current = $start->copy()->startOfMonth();
        while ($current <= $end) {
            $monthLabel = $current->format('M Y');
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Get all the metrics for this month
            $receiveTime = DocumentWorkflow::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, received_at)) as avg_time')
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
            
            $forwardedCount = DocumentWorkflow::whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('sender_id', $userId))
                ->when($officeId, fn($q) =>
                    $q->whereHas('sender.offices', fn($o) =>
                        $o->where('offices.id', $officeId)
                    )
                )
                ->count();
            
            $uploadedCount = Document::whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($userId, fn($q) => $q->where('user_id', $userId))
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
        ]);

        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');
        $officeId = $request->input('office_id');

        // Ensure only one of userId or officeId is set
        if ($userId && $officeId) {
            return redirect()->back()->with('error', 'Please select either a user or an office, not both.');
        }

        // Initialize data to avoid undefined variable error
        $data = collect();

        // Generate report based on type
        switch ($reportType) {
            case 'audit_history':
                $data = DocumentAudit::with(['user', 'document'])
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
                $data = DocumentWorkflow::with(['sender', 'recipient'])
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

        // Return view with generated data
        return view('reports.index', compact('data', 'reportType', 'startDate', 'endDate'));
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
}