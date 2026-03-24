<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log — {{ $document->title }}</title>
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
            padding: 24px 32px;
        }

        /* ── Screen-only controls ── */
        .screen-only {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn-primary   { background: #4f46e5; color: #fff; }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Report header ── */
        .report-header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .report-header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .report-header p {
            font-size: 11px;
            color: #64748b;
        }

        /* ── Meta grid ── */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .meta-item label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .meta-item span {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        thead tr {
            background: #4f46e5;
            color: #fff;
        }

        thead th {
            padding: 9px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        tbody tr:nth-child(even)  { background: #f8fafc; }
        tbody tr:nth-child(odd)   { background: #fff; }
        tbody tr:hover            { background: #eff6ff; }

        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            line-height: 1.5;
        }

        .td-number  { width: 36px; text-align: center; color: #94a3b8; font-weight: 600; }
        .td-date    { white-space: nowrap; color: #475569; }
        .td-user    { font-weight: 600; }
        .td-details { color: #64748b; font-size: 11px; }

        /* ── Status badges ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.6;
        }
        .badge-pending    { background: #fef9c3; color: #854d0e; }
        .badge-approved   { background: #dcfce7; color: #166534; }
        .badge-rejected   { background: #fee2e2; color: #991b1b; }
        .badge-received   { background: #dbeafe; color: #1e40af; }
        .badge-forwarded  { background: #f3e8ff; color: #6b21a8; }
        .badge-returned   { background: #fef3c7; color: #92400e; }
        .badge-completed  { background: #e0e7ff; color: #3730a3; }
        .badge-created    { background: #d1fae5; color: #065f46; }
        .badge-cancelled  { background: #f1f5f9; color: #475569; }
        .badge-default    { background: #f1f5f9; color: #475569; }

        /* ── Footer ── */
        .report-footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-size: 13px;
        }

        /* ── Print styles ── */
        @media print {
            .screen-only { display: none !important; }

            body { padding: 10px 14px; }

            @page {
                margin: 15mm 12mm;
                size: A4 landscape;
            }

            thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge    { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            tbody tr { page-break-inside: avoid; }

            table { font-size: 10px; }
        }
    </style>
</head>
<body>

    {{-- ── Screen controls (hidden when printing) ── --}}
    <div class="screen-only">
        <button class="btn btn-primary" onclick="window.print()">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
        <a href="{{ route('documents.audit.export', $document->id) }}" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
        <a href="{{ route('documents.show', $document->id) }}" class="btn btn-secondary">
            ← Back to Document
        </a>
    </div>

    {{-- ── Report header ── --}}
    <div class="report-header">
        <h1>Document Audit Log</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }} &nbsp;&middot;&nbsp; {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
    </div>

    {{-- ── Document meta ── --}}
    <div class="meta-grid">
        <div class="meta-item">
            <label>Document Title</label>
            <span>{{ $document->title }}</span>
        </div>
        <div class="meta-item">
            <label>Tracking Number</label>
            <span>{{ $document->trackingNumber->tracking_number ?? 'N/A' }}</span>
        </div>
        <div class="meta-item">
            <label>Current Status</label>
            <span>{{ ucfirst($document->status?->status ?? 'N/A') }}</span>
        </div>
        <div class="meta-item">
            <label>Uploaded By</label>
            <span>{{ $document->user ? ($document->user->first_name . ' ' . $document->user->last_name) : 'N/A' }}</span>
        </div>
        <div class="meta-item">
            <label>From Office</label>
            <span>{{ $document->originatingOffice->name ?? 'N/A' }}</span>
        </div>
        <div class="meta-item">
            <label>Classification</label>
            <span>{{ $document->categories->first()->category ?? 'N/A' }}</span>
        </div>
    </div>

    {{-- ── Audit log table ── --}}
    @if($auditLogs->isEmpty())
        <div class="empty-state">No audit entries found for this document.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="td-number">#</th>
                    <th>Date &amp; Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Details / Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditLogs as $i => $log)
                    @php
                        $status = strtolower($log->status ?? '');
                        $badgeClass = match($status) {
                            'pending'   => 'badge-pending',
                            'approved'  => 'badge-approved',
                            'rejected'  => 'badge-rejected',
                            'received'  => 'badge-received',
                            'forwarded' => 'badge-forwarded',
                            'returned'  => 'badge-returned',
                            'completed' => 'badge-completed',
                            'created'   => 'badge-created',
                            'cancelled' => 'badge-cancelled',
                            default     => 'badge-default',
                        };

                        $actionLabel = match(strtolower($log->action ?? '')) {
                            'created'   => 'Created',
                            'updated'   => 'Updated',
                            'forwarded' => 'Forwarded',
                            'received'  => 'Received',
                            'reviewed'  => 'Reviewed',
                            'approved'  => 'Approved',
                            'rejected'  => 'Rejected',
                            'returned'  => 'Returned',
                            'cancelled' => 'Cancelled',
                            default     => ucfirst(str_replace(['_','-'], ' ', $log->action ?? 'Updated')),
                        };
                    @endphp
                    <tr>
                        <td class="td-number">{{ $i + 1 }}</td>
                        <td class="td-date">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span style="color:#94a3b8">{{ $log->created_at->format('g:i A') }}</span>
                        </td>
                        <td class="td-user">
                            {{ $log->user ? trim($log->user->first_name . ' ' . $log->user->last_name) : 'System' }}
                        </td>
                        <td>{{ $actionLabel }}</td>
                        <td>
                            @if($log->status)
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($log->status) }}</span>
                            @else
                                <span style="color:#94a3b8">—</span>
                            @endif
                        </td>
                        <td class="td-details">{{ $log->details ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── Summary line ── --}}
        <div style="margin-top:12px; font-size:11px; color:#64748b;">
            {{ $auditLogs->count() }} audit entr{{ $auditLogs->count() === 1 ? 'y' : 'ies' }} total
        </div>
    @endif

    {{-- ── Footer ── --}}
    <div class="report-footer">
        <span>Document Audit Log &nbsp;&middot;&nbsp; {{ config('app.name', 'Document Archive') }}</span>
        <span>Tracking No: {{ $document->trackingNumber->tracking_number ?? 'N/A' }}</span>
    </div>

    <script>
        // Auto-print when the page loads (only if opened via the print button link)
        if (window.location.search.includes('autoprint=1')) {
            window.addEventListener('load', () => window.print());
        }
    </script>
</body>
</html>
