<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $report->name }}</title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        h1 { color: #3B82F6; }
        .metadata { margin-bottom: 20px; color: #666; }
        .data { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>{{ $report->name }}</h1>
    
    <div class="metadata">
        <p><strong>Generated:</strong> {{ $report->generated_at->format('Y-m-d H:i:s') }}</p>
        <p><strong>Type:</strong> {{ ucfirst($report->type) }}</p>
        <p><strong>Description:</strong> {{ $report->description }}</p>
    </div>
    
    <div class="data">
        <h2>Report Data</h2>
        <pre>{{ json_encode($report->data, JSON_PRETTY_PRINT) }}</pre>
    </div>
</body>
</html>