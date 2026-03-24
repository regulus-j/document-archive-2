<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Set up database connection based on Laravel config
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'doc_archive',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Document Workflow Status Analysis ===\n\n";

// 1. Check document status distribution
echo "1. DOCUMENT STATUS DISTRIBUTION:\n";
$documentStatuses = Capsule::table('document_status')
    ->select('status', Capsule::raw('COUNT(*) as count'))
    ->groupBy('status')
    ->orderBy('count', 'desc')
    ->get();

foreach ($documentStatuses as $status) {
    echo "   - {$status->status}: {$status->count} documents\n";
}

echo "\n2. WORKFLOW STATUS DISTRIBUTION:\n";
$workflowStatuses = Capsule::table('document_workflows')
    ->select('status', Capsule::raw('COUNT(*) as count'))
    ->groupBy('status')
    ->orderBy('count', 'desc')
    ->get();

foreach ($workflowStatuses as $status) {
    echo "   - {$status->status}: {$status->count} workflows\n";
}

echo "\n3. DOCUMENTS WITHOUT STATUS:\n";
$documentsWithoutStatus = Capsule::table('documents')
    ->leftJoin('document_status', 'documents.id', '=', 'document_status.doc_id')
    ->whereNull('document_status.doc_id')
    ->count();

echo "   - Documents without status: {$documentsWithoutStatus}\n";

echo "\n4. DOCUMENTS WITHOUT WORKFLOWS:\n";
$documentsWithoutWorkflow = Capsule::table('documents')
    ->leftJoin('document_workflows', 'documents.id', '=', 'document_workflows.document_id')
    ->whereNull('document_workflows.document_id')
    ->count();

echo "   - Documents without workflows: {$documentsWithoutWorkflow}\n";

echo "\n5. STATUS MISMATCH ANALYSIS:\n";
$statusMismatches = Capsule::table('documents')
    ->join('document_status', 'documents.id', '=', 'document_status.doc_id')
    ->leftJoin('document_workflows', 'documents.id', '=', 'document_workflows.document_id')
    ->select(
        'documents.id as doc_id',
        'documents.title',
        'document_status.status as doc_status',
        Capsule::raw('GROUP_CONCAT(DISTINCT document_workflows.status) as workflow_statuses')
    )
    ->groupBy('documents.id', 'documents.title', 'document_status.status')
    ->having(Capsule::raw('COUNT(document_workflows.id)'), '>', 0)
    ->limit(10)
    ->get();

echo "   Sample documents with status and workflow info:\n";
foreach ($statusMismatches as $doc) {
    echo "   - Doc ID {$doc->doc_id}: Document Status='{$doc->doc_status}', Workflow Statuses='{$doc->workflow_statuses}'\n";
}

echo "\n6. WORKFLOW FETCH LOGIC ANALYSIS:\n";
echo "   Current receive logic fetches documents where:\n";
echo "   - User is recipient in documentWorkflow with status 'pending' or 'received'\n";
echo "   - Sender must have 'company-admin' role\n";
echo "   - Document status != 'complete'\n";

echo "\n7. POTENTIAL ISSUES:\n";
$pendingWorkflows = Capsule::table('document_workflows')
    ->where('status', 'pending')
    ->count();

$receivedWorkflows = Capsule::table('document_workflows')
    ->where('status', 'received')
    ->count();

echo "   - Pending workflows: {$pendingWorkflows}\n";
echo "   - Received workflows: {$receivedWorkflows}\n";

// Check for orphaned workflows
$orphanedWorkflows = Capsule::table('document_workflows')
    ->leftJoin('documents', 'document_workflows.document_id', '=', 'documents.id')
    ->whereNull('documents.id')
    ->count();

echo "   - Orphaned workflows (no document): {$orphanedWorkflows}\n";

echo "\n=== Analysis Complete ===\n";
