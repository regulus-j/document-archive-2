<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Report — {{ $target_label }}</title>
    <style>
        @page { size: A4 landscape; margin: 15mm 12mm; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container { padding: 0; }

        /* Header */
        .header {
            border-bottom: 3px solid {{ $companyColor }};
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: table;
            width: 100%;
        }
        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 100px;
        }
        .header-logo img {
            max-height: 45px;
            max-width: 95px;
        }
        .header-info {
            display: table-cell;
            vertical-align: middle;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: {{ $companyColor }};
            margin-bottom: 2px;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .report-meta {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 2px;
        }

        /* Summary */
        .summary-bar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 16px;
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 4px 8px;
        }
        .summary-count {
            font-size: 18px;
            font-weight: bold;
            color: {{ $companyColor }};
        }
        .summary-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Section header */
        .section-header {
            background: {{ $companyColor }};
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 8px 12px;
            margin-top: 14px;
            margin-bottom: 0;
            border-radius: 4px 4px 0 0;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-wrap { word-break: break-word; max-width: 220px; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-indigo { background: #e0e7ff; color: #3730a3; }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .badge-slate  { background: #f1f5f9; color: #334155; }
        .no-data { text-align: center; color: #94a3b8; padding: 12px; font-style: italic; }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }

        /* Page break */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="header-logo">
                @if(!empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" alt="Logo">
                @endif
            </div>
            <div class="header-info">
                <div class="company-name">{{ $companyName }}</div>
                <div class="report-title">Audit Report: {{ $target_label }}</div>
                <div class="report-meta">
                    Period: {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}
                </div>
                <div class="report-meta">Generated on: {{ $generated_at }} &nbsp;|&nbsp; Generated by: {{ $generated_by }}</div>
                <div class="report-meta">Audit Type: {{ $audit_target === 'user' ? 'Individual User' : 'Office / Team' }}</div>
            </div>
        </div>

        {{-- Summary Bar --}}
        <div class="summary-bar">
            @if(isset($audit_logs))
            <div class="summary-item">
                <div class="summary-count">{{ $audit_logs->count() }}</div>
                <div class="summary-label">Actions</div>
            </div>
            @endif
            @if(isset($uploaded_documents))
            <div class="summary-item">
                <div class="summary-count">{{ $uploaded_documents->count() }}</div>
                <div class="summary-label">Uploaded</div>
            </div>
            @endif
            @if(isset($received_workflows))
            <div class="summary-item">
                <div class="summary-count">{{ $received_workflows->count() }}</div>
                <div class="summary-label">Received</div>
            </div>
            @endif
            @if(isset($attachments_added))
            <div class="summary-item">
                <div class="summary-count">{{ $attachments_added->count() }}</div>
                <div class="summary-label">Attachments</div>
            </div>
            @endif
            @if(isset($reviewed_workflows))
            <div class="summary-item">
                <div class="summary-count">{{ $reviewed_workflows->count() }}</div>
                <div class="summary-label">Reviewed</div>
            </div>
            @endif
        </div>

        {{-- ========== Actions Log ========== --}}
        @if(isset($audit_logs))
        <div class="section-header">Actions Log ({{ $audit_logs->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if($audit_target === 'office')<th style="width: 14%;">User</th>@endif
                    <th style="width: {{ $audit_target === 'office' ? '18' : '22' }}%;">Document</th>
                    <th style="width: 12%;">Action</th>
                    <th style="width: 10%;">Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audit_logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    @if($audit_target === 'office')<td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'N/A' }}</td>@endif
                    <td>{{ $log->document->title ?? 'Document #' . $log->document_id }}</td>
                    <td><span class="badge badge-indigo">{{ ucfirst($log->action) }}</span></td>
                    <td>{{ ucfirst($log->status ?? '-') }}</td>
                    <td class="text-wrap">{{ $log->details ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="no-data">No actions recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- ========== Documents Uploaded ========== --}}
        @if(isset($uploaded_documents))
        <div class="section-header">Documents Uploaded ({{ $uploaded_documents->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if($audit_target === 'office')<th style="width: 14%;">Uploaded By</th>@endif
                    <th>Title</th>
                    <th style="width: 12%;">Tracking #</th>
                    <th style="width: 14%;">Category</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($uploaded_documents as $doc)
                <tr>
                    <td>{{ $doc->created_at->format('M d, Y h:i A') }}</td>
                    @if($audit_target === 'office')<td>{{ $doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'N/A' }}</td>@endif
                    <td>{{ $doc->title }}</td>
                    <td>{{ $doc->trackingNumber->tracking_number ?? '-' }}</td>
                    <td>{{ $doc->categories->pluck('category')->join(', ') ?: ($doc->category ?? '-') }}</td>
                    <td><span class="badge badge-green">{{ ucfirst($doc->status->status ?? 'N/A') }}</span></td>
                </tr>
                @empty
                <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="no-data">No documents uploaded.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- ========== Documents Received ========== --}}
        @if(isset($received_workflows))
        <div class="section-header">Documents Received ({{ $received_workflows->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if($audit_target === 'office')<th style="width: 14%;">Received By</th>@endif
                    <th>Document</th>
                    <th style="width: 14%;">Sent By</th>
                    <th style="width: 14%;">Purpose</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($received_workflows as $wf)
                <tr>
                    <td>{{ $wf->created_at->format('M d, Y h:i A') }}</td>
                    @if($audit_target === 'office')<td>{{ $wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'Office' }}</td>@endif
                    <td>{{ $wf->document->title ?? 'Document #' . $wf->document_id }}</td>
                    <td>{{ $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A' }}</td>
                    <td>{{ ucfirst($wf->purpose ?? '-') }}</td>
                    <td><span class="badge badge-blue">{{ ucfirst($wf->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="no-data">No documents received.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- ========== Attachments Added ========== --}}
        @if(isset($attachments_added))
        <div class="section-header">Attachments Added ({{ $attachments_added->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if($audit_target === 'office')<th style="width: 14%;">Added By</th>@endif
                    <th>Document</th>
                    <th style="width: 18%;">Filename</th>
                    <th style="width: 12%;">Type</th>
                    <th style="width: 8%;">Size</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attachments_added as $att)
                <tr>
                    <td>{{ $att->created_at->format('M d, Y h:i A') }}</td>
                    @if($audit_target === 'office')<td>{{ $att->uploader ? $att->uploader->first_name . ' ' . $att->uploader->last_name : 'N/A' }}</td>@endif
                    <td>{{ $att->document->title ?? 'Document #' . $att->document_id }}</td>
                    <td class="text-wrap">{{ $att->filename }}</td>
                    <td>{{ $att->mime_type ?? '-' }}</td>
                    <td>{{ $att->storage_size ? number_format($att->storage_size / 1024, 1) . ' KB' : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="no-data">No attachments added.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- ========== Documents Reviewed ========== --}}
        @if(isset($reviewed_workflows))
        <div class="section-header">Documents Reviewed ({{ $reviewed_workflows->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    @if($audit_target === 'office')<th style="width: 14%;">Reviewed By</th>@endif
                    <th>Document</th>
                    <th style="width: 14%;">Sent By</th>
                    <th style="width: 10%;">Decision</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviewed_workflows as $wf)
                @php
                    $badgeMap = [
                        'approved' => 'badge-green',
                        'rejected' => 'badge-red',
                        'returned' => 'badge-yellow',
                        'commented' => 'badge-blue',
                        'acknowledged' => 'badge-purple',
                    ];
                @endphp
                <tr>
                    <td>{{ $wf->created_at->format('M d, Y h:i A') }}</td>
                    @if($audit_target === 'office')<td>{{ $wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'Office' }}</td>@endif
                    <td>{{ $wf->document->title ?? 'Document #' . $wf->document_id }}</td>
                    <td>{{ $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A' }}</td>
                    <td><span class="badge {{ $badgeMap[$wf->status] ?? 'badge-slate' }}">{{ ucfirst($wf->status) }}</span></td>
                    <td class="text-wrap">{{ $wf->remarks ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="no-data">No reviewed documents.</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>This audit report was automatically generated from the {{ $companyName }} Document Archive System.</p>
            <p>&copy; {{ date('Y') }} {{ $companyName }} — All rights reserved</p>
        </div>
    </div>
</body>
</html>
