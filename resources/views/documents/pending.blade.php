@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-6 border border-indigo-200/80 overflow-hidden">
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Pending Actions</h1>
                        <p class="text-sm text-slate-500">Process your received documents here.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('documents.workflow-dashboard') }}" class="inline-flex items-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-lg text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        {{ __('Go to Receive') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Flow Instructions -->
        <div class="bg-indigo-50 rounded-xl border border-indigo-200 p-4 mb-6">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-indigo-600 mt-0.5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-indigo-800 font-medium">Document Processing Flow:</p>
                    <p class="mt-1 text-sm text-indigo-600">Select "Process Document" to review and take action on received documents. You can check "Received Documents" for items that need your attention, or "Sent Documents" to track documents you've forwarded. Use "Go to Receive" to check for new documents that have been forwarded to you.</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80 mb-6">
            <div class="flex">
                <a href="{{ route('documents.pending', ['tab' => 'received']) }}"
                   class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm {{ $tab === 'received' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <span>Received Documents</span>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800">
                            {{ $documents->total() }}
                        </span>
                    </div>
                </a>
                <a href="{{ route('documents.pending', ['tab' => 'sent']) }}"
                   class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm {{ $tab === 'sent' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span>Sent Documents</span>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800">
                            {{ $tab === 'sent' ? $documents->total() : \App\Models\Document::whereHas('documentWorkflow', function($query) {
                                $query->where('sender_id', auth()->id())
                                      ->where('status', 'received');
                            })->whereHas('status', function($q) {
                                $q->whereNotIn('status', ['complete', 'archived', 'recalled']);
                            })->count() }}
                        </span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Document List -->
        <div class="bg-white rounded-xl overflow-visible border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-12">#</th>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title</th>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">{{ $tab === 'sent' ? 'Recipient' : 'Sender' }}</th>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Status & Workflow</th>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Tracking</th>
                            <th class="bg-white px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @php
                            $counter = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                        @endphp
                        @forelse ($documents as $document)
                            @php
                                $status = $document->status?->status ? strtolower($document->status->status) : '';
                                $isRejected = in_array($status, ['rejected']);
                                $statusColor = 'gray';

                                if ($status == 'approved') {
                                    $statusColor = 'emerald';
                                } elseif ($status == 'pending') {
                                    $statusColor = 'amber';
                                } elseif ($status == 'forwarded') {
                                    $statusColor = 'blue';
                                } elseif ($status == 'recalled') {
                                    $statusColor = 'purple';
                                } elseif ($status == 'uploaded') {
                                    $statusColor = 'indigo';
                                } elseif ($status == 'rejected') {
                                    $statusColor = 'red';
                                }

                                $latestWorkflow = $isRejected ? $document->documentWorkflow()
                                    ->where('status', 'rejected')
                                    ->latest()
                                    ->first() : null;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-500">{{ $counter++ }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900 truncate">{{ $document->title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-2">
                                        @foreach($documentRecipients[$document->id] as $recipient)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                    {{ substr($tab === 'sent' ? $recipient['name'] : $recipient['sender'], 0, 1) }}
                                                </div>
                                                <div class="ml-2 text-sm text-slate-700 font-medium truncate">
                                                    {{ $tab === 'sent' ? $recipient['name'] : $recipient['sender'] }}
                                                </div>
                                            </div>
                                            @break
                                        @endforeach
                                        @if($document->transaction?->fromOffice)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $isRejected ? 'red' : 'blue' }}-100 text-{{ $isRejected ? 'red' : 'blue' }}-800 self-start">
                                                {{ $document->transaction?->fromOffice?->name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-2">
                                        <!-- Status Badge -->
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                {{ $document->status?->status ?? 'N/A' }}
                                            </span>
                                        </div>

                                        <!-- Recipients -->
                                        <div class="flex items-center text-xs text-slate-500">
                                            <span class="font-medium mr-1">Recipients:</span>
                                            @if (isset($documentRecipients[$document->id]) && count($documentRecipients[$document->id]) > 0)
                                                <span class="truncate max-w-xs">
                                                    @foreach ($documentRecipients[$document->id] as $recipient)
                                                        {{ $recipient['name'] }}
                                                        @if ($recipient['received'])
                                                            <span class="text-emerald-600">(Received)</span>
                                                        @else
                                                            <span class="text-amber-600">(Pending)</span>
                                                        @endif
                                                        @if (!$loop->last), @endif
                                                    @endforeach
                                                </span>
                                            @else
                                                <span class="italic">No recipients</span>
                                            @endif
                                        </div>

                                        <!-- Rejection Information -->
                                        @if($isRejected && $latestWorkflow && $latestWorkflow->remarks)
                                            <div class="mt-1">
                                                <span class="text-xs text-red-600"><b>Remarks:</b> {{ $latestWorkflow->remarks }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1.5">
                                        @if(isset($documentRecipients[$document->id]))
                                            @foreach($documentRecipients[$document->id] as $workflow)
                                                <div class="text-xs space-y-1">
                                                    <div class="text-slate-500">
                                                        <span class="font-medium">From:</span>
                                                        {{ $workflow['sender'] }}
                                                    </div>
                                                    <div class="text-slate-500">
                                                        <span class="font-medium">Received:</span>
                                                        {{ \Carbon\Carbon::parse($workflow['received_at'])->format('M d, Y H:i') }}
                                                    </div>
                                                    @if($workflow['purpose'])
                                                        <div class="text-indigo-600">
                                                            <span class="font-medium">Purpose:</span>
                                                            {{ ucwords(str_replace('_', ' ', $workflow['purpose'])) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-xs text-slate-500 space-y-1">
                                                <div>
                                                    <span class="font-medium">Created:</span>
                                                    {{ $document->created_at->format('M d, Y H:i') }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Updated:</span>
                                                    {{ $document->updated_at->format('M d, Y H:i') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        @if($tab === 'received')
                                            @php
                                                // Find the correct workflow for the current user
                                                $userWorkflow = $document->documentWorkflow()
                                                    ->where('recipient_id', auth()->id())
                                                    ->where('status', 'received')
                                                    ->first();
                                            @endphp
                                            @if($userWorkflow)
                                                <a href="{{ route('documents.review', $userWorkflow->id) }}" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                    Process Document
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-sm font-medium rounded-md text-slate-500 bg-slate-50">
                                                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Not Available
                                                </span>
                                            @endif
                                        @endif
                                        <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4">
                                    <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-slate-200 rounded-lg bg-slate-50/50 mx-4 my-6">
                                        <svg class="h-12 w-12 text-slate-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-slate-900 font-medium text-lg mb-2">No {{ $tab }} documents found</p>
                                        <p class="text-slate-500 text-base">
                                            @if($tab === 'received')
                                                All documents have been processed.
                                            @else
                                                No documents awaiting recipient action.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="p-6 border-t border-slate-200">
                    {{ $documents->appends(['tab' => $tab])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
