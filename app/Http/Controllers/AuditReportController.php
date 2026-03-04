<?php

namespace App\Http\Controllers;

use App\Models\CompanyAccount;
use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\DocumentAudit;
use App\Models\DocumentWorkflow;
use App\Models\Office;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuditReportController extends Controller
{
    /**
     * Show the audit report builder page.
     */
    public function index(Request $request)
    {
        $company = $this->resolveCompany();
        $companyId = $company?->id;

        // Get company users for the searchable dropdown
        $users = $this->getCompanyUsers($company);
        $offices = $company ? Office::where('company_id', $companyId)->orderBy('name')->get() : collect();

        return view('reports.audit', compact('users', 'offices'));
    }

    /**
     * Generate the audit report.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'audit_target' => 'required|in:user,office',
            'user_id' => 'required_if:audit_target,user|nullable|integer',
            'office_id' => 'required_if:audit_target,office|nullable|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'filters' => 'required|array|min:1',
            'filters.*' => 'in:actions,uploads,received,attachments,reviewed',
        ]);

        $company = $this->resolveCompany();
        $companyUsers = $this->getCompanyUsers($company);
        $companyUserIds = $companyUsers->pluck('id');

        $auditTarget = $request->input('audit_target');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filters = $request->input('filters', []);
        $outputFormat = $request->input('output', 'view'); // 'view' or 'pdf'

        $targetLabel = '';
        $data = [];

        if ($auditTarget === 'user') {
            $userId = $request->input('user_id');
            $user = User::findOrFail($userId);
            $targetLabel = $user->full_name ?? ($user->first_name . ' ' . $user->last_name);

            // Ensure selected user belongs to this company
            if (!$companyUserIds->contains($userId)) {
                abort(403, 'User does not belong to your company.');
            }

            $data = $this->gatherUserAuditData($userId, $startDate, $endDate, $filters, $companyUserIds);
        } else {
            $officeId = $request->input('office_id');
            $office = Office::findOrFail($officeId);
            $targetLabel = $office->name;

            // Ensure office belongs to this company
            if ($company && $office->company_id != $company->id) {
                abort(403, 'Office does not belong to your company.');
            }

            // Get all users in this office
            $officeUserIds = $office->users()->pluck('users.id');
            $data = $this->gatherOfficeAuditData($officeId, $officeUserIds, $startDate, $endDate, $filters, $companyUserIds);
            $data['office_users'] = User::whereIn('id', $officeUserIds)->get();
        }

        $data['audit_target'] = $auditTarget;
        $data['target_label'] = $targetLabel;
        $data['target_id'] = $auditTarget === 'user' ? $request->input('user_id') : $request->input('office_id');
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['filters'] = $filters;
        $data['generated_at'] = now()->format('F d, Y h:i A');
        $data['generated_by'] = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        if ($outputFormat === 'pdf') {
            return $this->exportPdf($data);
        }

        if ($outputFormat === 'excel') {
            return $this->exportExcel($data);
        }

        // Reload users/offices for the form
        $users = $companyUsers;
        $offices = $company ? Office::where('company_id', $company->id)->orderBy('name')->get() : collect();

        return view('reports.audit', array_merge($data, [
            'users' => $users,
            'offices' => $offices,
            'hasResults' => true,
        ]));
    }

    /**
     * Gather audit data for a specific user.
     */
    private function gatherUserAuditData(int $userId, string $startDate, string $endDate, array $filters, $companyUserIds): array
    {
        $data = [];

        if (in_array('actions', $filters)) {
            $data['audit_logs'] = DocumentAudit::with(['document', 'user'])
                ->where('user_id', $userId)
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('uploads', $filters)) {
            $data['uploaded_documents'] = Document::with(['status', 'trackingNumber', 'categories'])
                ->where('uploader', $userId)
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('received', $filters)) {
            $data['received_workflows'] = DocumentWorkflow::with(['document', 'sender'])
                ->where('recipient_id', $userId)
                ->where('status', 'received')
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('attachments', $filters)) {
            $data['attachments_added'] = DocumentAttachment::with(['document', 'uploader'])
                ->where('uploaded_by', $userId)
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('reviewed', $filters)) {
            $data['reviewed_workflows'] = DocumentWorkflow::with(['document', 'sender'])
                ->where('recipient_id', $userId)
                ->whereIn('status', ['approved', 'rejected', 'returned', 'commented', 'acknowledged'])
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $data;
    }

    /**
     * Gather audit data for an office (aggregates all office users).
     */
    private function gatherOfficeAuditData(int $officeId, $officeUserIds, string $startDate, string $endDate, array $filters, $companyUserIds): array
    {
        $data = [];

        if (in_array('actions', $filters)) {
            $data['audit_logs'] = DocumentAudit::with(['document', 'user'])
                ->whereIn('user_id', $officeUserIds)
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('uploads', $filters)) {
            $data['uploaded_documents'] = Document::with(['status', 'trackingNumber', 'categories', 'user'])
                ->whereIn('uploader', $officeUserIds)
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('received', $filters)) {
            // Documents received by office (via recipient_office) or by office users
            $data['received_workflows'] = DocumentWorkflow::with(['document', 'sender', 'recipient'])
                ->where(function ($q) use ($officeId, $officeUserIds) {
                    $q->where('recipient_office', $officeId)
                      ->orWhereIn('recipient_id', $officeUserIds);
                })
                ->where('status', 'received')
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('attachments', $filters)) {
            $data['attachments_added'] = DocumentAttachment::with(['document', 'uploader'])
                ->whereIn('uploaded_by', $officeUserIds)
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('reviewed', $filters)) {
            $data['reviewed_workflows'] = DocumentWorkflow::with(['document', 'sender', 'recipient'])
                ->where(function ($q) use ($officeId, $officeUserIds) {
                    $q->where('recipient_office', $officeId)
                      ->orWhereIn('recipient_id', $officeUserIds);
                })
                ->whereIn('status', ['approved', 'rejected', 'returned', 'commented', 'acknowledged'])
                ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $data;
    }

    /**
     * Export audit data to landscape PDF.
     */
    private function exportPdf(array $data)
    {
        $branding = $this->getCompanyBranding();

        $pdf = Pdf::loadView('reports.audit_pdf', array_merge($data, $branding))
            ->setPaper('a4', 'landscape');

        $filename = 'Audit_Report_' . str_replace(' ', '_', $data['target_label']) . '_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export audit data to Excel.
     */
    private function exportExcel(array $data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheetIndex = 0;

        $title = 'Audit Report: ' . $data['target_label'];
        $dateRange = Carbon::parse($data['start_date'])->format('M d, Y') . ' — ' . Carbon::parse($data['end_date'])->format('M d, Y');
        $isOffice = $data['audit_target'] === 'office';

        $spreadsheet->getProperties()
            ->setCreator($data['generated_by'])
            ->setLastModifiedBy($data['generated_by'])
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription("$title - $dateRange");

        // --- Actions sheet ---
        if (!empty($data['audit_logs'])) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Actions');
            $this->writeExcelHeader($sheet, $title, $dateRange, $data['generated_at']);
            $row = 5;
            $cols = ['Date', 'Document', 'Action', 'Status', 'Details'];
            if ($isOffice) array_splice($cols, 1, 0, ['User']);
            $this->writeExcelRow($sheet, $row, $cols, true);
            $row++;
            foreach ($data['audit_logs'] as $log) {
                $rowData = [
                    $log->created_at->format('M d, Y h:i A'),
                    $log->document->title ?? 'Document #' . $log->document_id,
                    ucfirst($log->action),
                    ucfirst($log->status ?? '-'),
                    $log->details ?? '-',
                ];
                if ($isOffice) array_splice($rowData, 1, 0, [$log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'N/A']);
                $this->writeExcelRow($sheet, $row, $rowData);
                $row++;
            }
            $this->autoSizeColumns($sheet, count($cols));
            $sheetIndex++;
        }

        // --- Uploads sheet ---
        if (!empty($data['uploaded_documents'])) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Uploads');
            $this->writeExcelHeader($sheet, $title, $dateRange, $data['generated_at']);
            $row = 5;
            $cols = ['Date', 'Title', 'Tracking #', 'Category', 'Status'];
            if ($isOffice) array_splice($cols, 1, 0, ['Uploaded By']);
            $this->writeExcelRow($sheet, $row, $cols, true);
            $row++;
            foreach ($data['uploaded_documents'] as $doc) {
                $rowData = [
                    $doc->created_at->format('M d, Y h:i A'),
                    $doc->title,
                    $doc->trackingNumber->tracking_number ?? '-',
                    $doc->categories->pluck('category')->join(', ') ?: ($doc->category ?? '-'),
                    ucfirst($doc->status->status ?? 'N/A'),
                ];
                if ($isOffice) array_splice($rowData, 1, 0, [$doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'N/A']);
                $this->writeExcelRow($sheet, $row, $rowData);
                $row++;
            }
            $this->autoSizeColumns($sheet, count($cols));
            $sheetIndex++;
        }

        // --- Received sheet ---
        if (!empty($data['received_workflows'])) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Received');
            $this->writeExcelHeader($sheet, $title, $dateRange, $data['generated_at']);
            $row = 5;
            $cols = ['Date', 'Document', 'Sent By', 'Purpose', 'Status'];
            if ($isOffice) array_splice($cols, 1, 0, ['Received By']);
            $this->writeExcelRow($sheet, $row, $cols, true);
            $row++;
            foreach ($data['received_workflows'] as $wf) {
                $rowData = [
                    $wf->created_at->format('M d, Y h:i A'),
                    $wf->document->title ?? 'Document #' . $wf->document_id,
                    $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A',
                    ucfirst($wf->purpose ?? '-'),
                    ucfirst($wf->status),
                ];
                if ($isOffice) array_splice($rowData, 1, 0, [$wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'Office']);
                $this->writeExcelRow($sheet, $row, $rowData);
                $row++;
            }
            $this->autoSizeColumns($sheet, count($cols));
            $sheetIndex++;
        }

        // --- Attachments sheet ---
        if (!empty($data['attachments_added'])) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Attachments');
            $this->writeExcelHeader($sheet, $title, $dateRange, $data['generated_at']);
            $row = 5;
            $cols = ['Date', 'Document', 'Filename', 'Type', 'Size'];
            if ($isOffice) array_splice($cols, 1, 0, ['Added By']);
            $this->writeExcelRow($sheet, $row, $cols, true);
            $row++;
            foreach ($data['attachments_added'] as $att) {
                $rowData = [
                    $att->created_at->format('M d, Y h:i A'),
                    $att->document->title ?? 'Document #' . $att->document_id,
                    $att->filename,
                    $att->mime_type ?? '-',
                    $att->storage_size ? number_format($att->storage_size / 1024, 1) . ' KB' : '-',
                ];
                if ($isOffice) array_splice($rowData, 1, 0, [$att->uploader ? $att->uploader->first_name . ' ' . $att->uploader->last_name : 'N/A']);
                $this->writeExcelRow($sheet, $row, $rowData);
                $row++;
            }
            $this->autoSizeColumns($sheet, count($cols));
            $sheetIndex++;
        }

        // --- Reviewed sheet ---
        if (!empty($data['reviewed_workflows'])) {
            if ($sheetIndex > 0) $spreadsheet->createSheet($sheetIndex);
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle('Reviewed');
            $this->writeExcelHeader($sheet, $title, $dateRange, $data['generated_at']);
            $row = 5;
            $cols = ['Date', 'Document', 'Sent By', 'Decision', 'Remarks'];
            if ($isOffice) array_splice($cols, 1, 0, ['Reviewed By']);
            $this->writeExcelRow($sheet, $row, $cols, true);
            $row++;
            foreach ($data['reviewed_workflows'] as $wf) {
                $rowData = [
                    $wf->created_at->format('M d, Y h:i A'),
                    $wf->document->title ?? 'Document #' . $wf->document_id,
                    $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A',
                    ucfirst($wf->status),
                    $wf->remarks ?? '-',
                ];
                if ($isOffice) array_splice($rowData, 1, 0, [$wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'Office']);
                $this->writeExcelRow($sheet, $row, $rowData);
                $row++;
            }
            $this->autoSizeColumns($sheet, count($cols));
            $sheetIndex++;
        }

        // Default to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Write to temp file and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Audit_Report_' . Str::slug($data['target_label']) . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/public/temp/' . $filename);

        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }

        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend();
    }

    /** Write title rows to an Excel sheet. */
    private function writeExcelHeader($sheet, string $title, string $dateRange, string $generatedAt): void
    {
        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', $dateRange);
        $sheet->setCellValue('A3', 'Generated: ' . $generatedAt);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(11);
        $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748B');
    }

    /** Write a row of values to an Excel sheet. */
    private function writeExcelRow($sheet, int $row, array $values, bool $bold = false): void
    {
        $col = 'A';
        foreach ($values as $val) {
            $sheet->setCellValue($col . $row, $val);
            $col++;
        }
        if ($bold) {
            $lastCol = chr(ord('A') + count($values) - 1);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F1F5F9');
        }
    }

    /** Auto-size columns on a sheet. */
    private function autoSizeColumns($sheet, int $count): void
    {
        foreach (range('A', chr(ord('A') + $count - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Resolve the current user's company.
     */
    private function resolveCompany(): ?CompanyAccount
    {
        return CompanyAccount::where('user_id', auth()->id())->first()
            ?? CompanyAccount::whereHas('employees', fn($q) => $q->where('users.id', auth()->id()))->first();
    }

    /**
     * Get all users belonging to the resolved company.
     */
    private function getCompanyUsers(?CompanyAccount $company)
    {
        if (!$company) {
            return collect([auth()->user()]);
        }

        $companyUserIds = $company->employees()->pluck('users.id')->push($company->user_id)->filter()->unique();

        return User::whereIn('id', $companyUserIds)->orderBy('first_name')->get();
    }

    /**
     * Resolve company branding for PDF exports.
     */
    private function getCompanyBranding(): array
    {
        $company = $this->resolveCompany();

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
     * API endpoint: search users for the filterable dropdown.
     */
    public function searchUsers(Request $request)
    {
        $company = $this->resolveCompany();
        $search = $request->input('q', '');

        $users = $this->getCompanyUsers($company);

        if ($search) {
            $search = strtolower($search);
            $users = $users->filter(function ($user) use ($search) {
                return str_contains(strtolower($user->first_name . ' ' . $user->last_name), $search)
                    || str_contains(strtolower($user->email), $search);
            });
        }

        return response()->json($users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->first_name . ' ' . ($u->middle_name ? $u->middle_name . ' ' : '') . $u->last_name,
            'email' => $u->email,
        ])->values());
    }

    /**
     * API endpoint: search offices for the filterable dropdown.
     */
    public function searchOffices(Request $request)
    {
        $company = $this->resolveCompany();
        $search = $request->input('q', '');

        $offices = $company
            ? Office::where('company_id', $company->id)->orderBy('name')->get()
            : collect();

        if ($search) {
            $search = strtolower($search);
            $offices = $offices->filter(fn($o) => str_contains(strtolower($o->name), $search));
        }

        return response()->json($offices->map(fn($o) => [
            'id' => $o->id,
            'name' => $o->name,
        ])->values());
    }
}
