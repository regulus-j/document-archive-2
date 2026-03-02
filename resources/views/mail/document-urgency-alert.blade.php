<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { padding: 24px 32px; color: white; }
        .header.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .header.escalation { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .header.inactivity { background: linear-gradient(135deg, #6366f1, #4338ca); }
        .header.reroute { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.9; font-size: 14px; }
        .body { padding: 32px; }
        .info-grid { display: table; width: 100%; border-collapse: collapse; margin: 16px 0; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 8px 12px; font-weight: 600; color: #374151; font-size: 13px; width: 140px; border-bottom: 1px solid #f3f4f6; }
        .info-value { display: table-cell; padding: 8px 12px; color: #4b5563; font-size: 13px; border-bottom: 1px solid #f3f4f6; }
        .urgency-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .urgency-critical { background: #fef2f2; color: #991b1b; }
        .urgency-high { background: #fff7ed; color: #9a3412; }
        .urgency-medium { background: #fffbeb; color: #92400e; }
        .urgency-low { background: #f0fdf4; color: #166534; }
        .action-box { background: #fefce8; border-left: 4px solid #eab308; padding: 16px; border-radius: 0 8px 8px 0; margin: 20px 0; }
        .action-box.escalation { background: #fef2f2; border-left-color: #ef4444; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center; font-size: 12px; color: #9ca3af; }
        .btn { display: inline-block; padding: 10px 24px; background: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $alertType }}">
            @if($alertType === 'warning')
                <h1>⚠️ Attention Required</h1>
                <p>This document is approaching its response deadline.</p>
            @elseif($alertType === 'escalation')
                <h1>🚨 Urgent: Response Time Exceeded</h1>
                <p>This document has exceeded the allowed response time for its urgency level.</p>
            @elseif($alertType === 'inactivity')
                <h1>⏸️ Workflow Inactive</h1>
                <p>A workflow step for this document has been inactive for an extended period.</p>
            @elseif($alertType === 'reroute')
                <h1>🔄 Workflow Rerouted</h1>
                <p>A workflow step for this document has been reassigned.</p>
            @endif
        </div>

        <div class="body">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Document</div>
                    <div class="info-value">{{ $document->title }}</div>
                </div>
                @if($document->tracking_number ?? false)
                <div class="info-row">
                    <div class="info-label">Tracking #</div>
                    <div class="info-value">{{ $document->trackingNumber->tracking_number ?? 'N/A' }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Urgency Level</div>
                    <div class="info-value">
                        <span class="urgency-badge urgency-{{ $document->urgency_level ?? 'medium' }}">
                            {{ strtoupper($document->urgency_level ?? 'MEDIUM') }}
                        </span>
                    </div>
                </div>
                @if($document->urgency_reasoning)
                <div class="info-row">
                    <div class="info-label">Analysis</div>
                    <div class="info-value">{{ $document->urgency_reasoning }}</div>
                </div>
                @endif
                @if($workflow)
                <div class="info-row">
                    <div class="info-label">Assigned To</div>
                    <div class="info-value">{{ $workflow->recipientUser->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Workflow Status</div>
                    <div class="info-value">{{ ucfirst($workflow->status) }}</div>
                </div>
                @if($workflow->due_date)
                <div class="info-row">
                    <div class="info-label">Due Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($workflow->due_date)->format('M d, Y h:i A') }}</div>
                </div>
                @endif
                @endif
            </div>

            @if($alertType === 'escalation')
            <div class="action-box escalation">
                <strong>Immediate action required.</strong>
                <p style="margin: 8px 0 0; font-size: 13px;">This document has exceeded its maximum response time. Please process it immediately or reroute the workflow to an available member.</p>
            </div>
            @elseif($alertType === 'warning')
            <div class="action-box">
                <strong>Response deadline approaching.</strong>
                <p style="margin: 8px 0 0; font-size: 13px;">Please process this document before the response deadline to avoid escalation.</p>
            </div>
            @elseif($alertType === 'reroute')
            <div class="action-box" style="background: #eff6ff; border-left-color: #3b82f6;">
                <strong>Workflow step rerouted.</strong>
                @if(!empty($details['old_recipient']))
                <p style="margin: 8px 0 0; font-size: 13px;">
                    Reassigned from <strong>{{ $details['old_recipient'] }}</strong> to <strong>{{ $details['new_recipient'] ?? 'N/A' }}</strong>.
                    @if(!empty($details['reason'])) Reason: {{ $details['reason'] }} @endif
                </p>
                @endif
            </div>
            @endif

            @if(!empty($details['view_url']))
            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ $details['view_url'] }}" class="btn">View Document</a>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated notification from the Document Urgency Matrix system.</p>
            <p>{{ config('app.name') }} &bull; {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
