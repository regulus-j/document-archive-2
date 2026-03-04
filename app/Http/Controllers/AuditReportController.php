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
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['filters'] = $filters;
        $data['generated_at'] = now()->format('F d, Y h:i A');
        $data['generated_by'] = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        if ($outputFormat === 'pdf') {
            return $this->exportPdf($data);
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

        return $pdf->download($filename);
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
