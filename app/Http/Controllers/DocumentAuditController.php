<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAudit;
use App\Services\DocumentAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocumentAuditController extends Controller
{
    protected DocumentAccessService $documentAccessService;

    public function __construct(DocumentAccessService $documentAccessService)
    {
        $this->documentAccessService = $documentAccessService;
    }

    /**
     * Export the full audit log for a document as a CSV file.
     */
    public function exportCsv(Document $document)
    {
        if (! $this->documentAccessService->canViewDocument($document)) {
            abort(403, 'You are not authorized to export this audit log.');
        }

        $auditLogs = DocumentAudit::where('document_id', $document->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $trackingNo = $document->trackingNumber->tracking_number ?? $document->id;
        $filename   = 'audit-' . str_replace(['/', '\\', ' '], '-', $trackingNo)
                      . '-' . now()->format('Y-m-d')
                      . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($auditLogs, $document) {
            $fh = fopen('php://output', 'w');

            // UTF-8 BOM so Excel correctly reads accented chars
            fwrite($fh, "\xEF\xBB\xBF");

            // Document meta header
            fputcsv($fh, ['DOCUMENT AUDIT LOG']);
            fputcsv($fh, ['Title:', $document->title]);
            fputcsv($fh, ['Tracking Number:', $document->trackingNumber->tracking_number ?? 'N/A']);
            fputcsv($fh, ['Exported At:', now()->format('Y-m-d H:i:s')]);
            fputcsv($fh, ['Exported By:', auth()->user()->first_name . ' ' . auth()->user()->last_name]);
            fputcsv($fh, []);

            // Column headers
            fputcsv($fh, ['#', 'Date & Time', 'User', 'Action', 'Status', 'Details']);

            foreach ($auditLogs as $i => $log) {
                $userName = $log->user
                    ? trim($log->user->first_name . ' ' . $log->user->last_name)
                    : 'System';

                fputcsv($fh, [
                    $i + 1,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $userName,
                    ucfirst($log->action ?? ''),
                    ucfirst($log->status ?? ''),
                    $log->details ?? '',
                ]);
            }

            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show a print-friendly full audit log for a document.
     */
    public function printView(Document $document)
    {
        if (! $this->documentAccessService->canViewDocument($document)) {
            abort(403, 'You are not authorized to view this audit log.');
        }

        $auditLogs = DocumentAudit::where('document_id', $document->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $document->load(['trackingNumber', 'status', 'user', 'originatingOffice', 'categories']);

        return view('documents.audit_print', compact('document', 'auditLogs'));
    }
}
