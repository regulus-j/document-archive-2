@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white" x-data="{ activeTab: 'receive', showArchiveConfirm: false, archiveDocId: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Header --}}
        <div class="bg-white rounded-xl border border-indigo-200/80 overflow-hidden">
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">{{ __('Workflow Dashboard') }}</h1>
                        <p class="text-sm text-slate-500">Manage all your document workflows in one place</p>
                    </div>
                </div>
                <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    {{ __('All Documents') }}
                </a>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="bg-emerald-50/60 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50/60 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Tab Navigation --}}
        <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80">
            <div class="flex">
                {{-- Receive Tab --}}
                <button @click="activeTab = 'receive'"
                    :class="activeTab === 'receive' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <span>To Receive</span>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800">{{ $receiveDocuments->count() }}</span>
                    </div>
                </button>
                {{-- Pending Tab --}}
                <button @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'border-amber-500 text-amber-600 bg-amber-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pending</span>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-800">{{ $pendingReceivedDocs->count() }}</span>
                    </div>
                </button>
                {{-- Completed Tab --}}
                <button @click="activeTab = 'completed'"
                    :class="activeTab === 'completed' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Completed</span>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-800">{{ $completedReceivedDocs->count() + $completedSentDocs->count() }}</span>
                    </div>
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{--              TAB 1 — TO RECEIVE                    --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'receive'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            {{-- Info banner --}}
            <div class="bg-indigo-50 rounded-xl border border-indigo-200 p-4 mb-6">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-indigo-600">Click <strong>"Receive Document"</strong> to acknowledge receipt. After receiving, the document moves to the <strong>Pending</strong> tab for processing.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80">
                @if($receiveDocuments->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 px-4">
                        <div class="p-3 bg-indigo-100 rounded-lg mb-4">
                            <svg class="h-10 w-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900 mb-1">No documents to receive</h3>
                        <p class="text-slate-500 text-sm">There are no documents forwarded to you at this time.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-indigo-200/60">
                            <thead>
                                <tr>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">Document Title</th>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">From</th>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">Date Sent</th>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-indigo-200/60">
                                @foreach($receiveDocuments as $document)
                                    <tr class="hover:bg-indigo-50/40 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $document->title }}</div>
                                            @if($document->reference_number)
                                                <div class="text-xs text-slate-500">{{ $document->reference_number }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-900">
                                                {{ $document->transaction->fromOffice->name ?? ($document->user->offices->first()->name ?? 'Admin') }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $document->user->first_name ?? 'Unknown' }} {{ $document->user->last_name ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ $document->documentWorkflow->where('recipient_id', auth()->id())->first()?->created_at?->format('M d, Y h:i A') ?? $document->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('documents.receive.confirm', $document->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Receive
                                                    </button>
                                                </form>
                                                <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{--              TAB 2 — PENDING                       --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
            {{-- Sub-tabs for Received / Sent --}}
            <div x-data="{ pendingTab: 'received' }" class="space-y-4">
                <div class="flex gap-2 mb-4">
                    <button @click="pendingTab = 'received'"
                        :class="pendingTab === 'received' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        Received ({{ $pendingReceivedDocs->count() }})
                    </button>
                    <button @click="pendingTab = 'sent'"
                        :class="pendingTab === 'sent' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Sent ({{ $pendingSentDocs->count() }})
                    </button>
                </div>

                {{-- Received Pending Documents --}}
                <div x-show="pendingTab === 'received'" class="bg-white rounded-xl overflow-visible border border-indigo-200/80">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-12">#</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Sender</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Status</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Tracking</th>
                                    <th class="bg-white px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-24">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($pendingReceivedDocs as $idx => $document)
                                    @php
                                        $status = strtolower($document->status?->status ?? '');
                                        $statusColor = match($status) {
                                            'approved' => 'emerald', 'pending' => 'amber', 'forwarded' => 'blue',
                                            'recalled' => 'purple', 'uploaded' => 'indigo', 'rejected' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-500">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900 truncate max-w-xs">{{ $document->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(isset($pendingRecipients[$document->id]))
                                                @foreach($pendingRecipients[$document->id] as $r)
                                                    <div class="flex items-center mb-1">
                                                        <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                            {{ substr($r['sender'], 0, 1) }}
                                                        </div>
                                                        <span class="ml-2 text-sm text-slate-700">{{ $r['sender'] }}</span>
                                                    </div>
                                                    @break
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                {{ $document->status?->status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(isset($pendingRecipients[$document->id]))
                                                @foreach($pendingRecipients[$document->id] as $r)
                                                    <div class="text-xs text-slate-500">
                                                        <span class="font-medium">Received:</span>
                                                        {{ $r['received_at'] ? \Carbon\Carbon::parse($r['received_at'])->format('M d, Y H:i') : 'N/A' }}
                                                    </div>
                                                    @if($r['purpose'])
                                                        <div class="text-xs text-indigo-600">
                                                            <span class="font-medium">Purpose:</span>
                                                            {{ ucwords(str_replace('_', ' ', $r['purpose'])) }}
                                                        </div>
                                                    @endif
                                                    @break
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                @php
                                                    $userWorkflow = $document->documentWorkflow()
                                                        ->where('recipient_id', auth()->id())
                                                        ->where('status', 'received')
                                                        ->first();
                                                @endphp
                                                @if($userWorkflow)
                                                    <a href="{{ route('documents.review', $userWorkflow->id) }}" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                        Process
                                                    </a>
                                                @endif
                                                <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition-colors">
                                                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-slate-500 font-medium">No pending received documents</p>
                                            <p class="text-sm text-slate-400 mt-1">All documents have been processed.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Sent Pending Documents --}}
                <div x-show="pendingTab === 'sent'" x-cloak class="bg-white rounded-xl overflow-visible border border-indigo-200/80">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-12">#</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Recipient</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Status</th>
                                    <th class="bg-white px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-24">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($pendingSentDocs as $idx => $document)
                                    @php
                                        $status = strtolower($document->status?->status ?? '');
                                        $statusColor = match($status) {
                                            'approved' => 'emerald', 'pending' => 'amber', 'forwarded' => 'blue',
                                            'recalled' => 'purple', 'uploaded' => 'indigo', 'rejected' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-500">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900 truncate max-w-xs">{{ $document->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(isset($pendingRecipients[$document->id]))
                                                @foreach($pendingRecipients[$document->id] as $r)
                                                    <div class="flex items-center mb-1">
                                                        <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                            {{ substr($r['name'], 0, 1) }}
                                                        </div>
                                                        <span class="ml-2 text-sm text-slate-700">{{ $r['name'] }}</span>
                                                    </div>
                                                    @break
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                {{ $document->status?->status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition-colors">
                                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                            <p class="text-slate-500 font-medium">No pending sent documents</p>
                                            <p class="text-sm text-slate-400 mt-1">No documents awaiting recipient action.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════ --}}
        {{--              TAB 3 — COMPLETED                     --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'completed'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
            <div x-data="{ completedTab: 'received' }" class="space-y-4">
                <div class="flex gap-2 mb-4">
                    <button @click="completedTab = 'received'"
                        :class="completedTab === 'received' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Received ({{ $completedReceivedDocs->count() }})
                    </button>
                    <button @click="completedTab = 'sent'"
                        :class="completedTab === 'sent' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Sent ({{ $completedSentDocs->count() }})
                    </button>
                </div>

                {{-- Received Completed --}}
                <div x-show="completedTab === 'received'" class="bg-white rounded-xl overflow-visible border border-indigo-200/80">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-12">#</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Uploaded By</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Date Completed</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Status</th>
                                    <th class="bg-white px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($completedReceivedDocs as $idx => $document)
                                    @php
                                        $statusClass = match(optional($document->status)->status) {
                                            'completed', 'complete' => 'bg-green-100 text-green-800',
                                            'acknowledged' => 'bg-indigo-100 text-indigo-800',
                                            'commented' => 'bg-cyan-100 text-cyan-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-slate-100 text-slate-800'
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $document->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-900">{{ optional($document->user)->first_name }} {{ optional($document->user)->last_name }}</div>
                                            <div class="text-xs text-slate-500">{{ optional(optional($document->user)->offices->first())->name ?? 'No Office' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-900">{{ $document->updated_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-slate-500">{{ $document->updated_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst(optional($document->status)->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View
                                                </a>
                                                <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    Download
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-slate-500 font-medium">No completed received documents</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Sent Completed --}}
                <div x-show="completedTab === 'sent'" x-cloak class="bg-white rounded-xl overflow-visible border border-indigo-200/80">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-12">#</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Recipient</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Date Completed</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Status</th>
                                    <th class="bg-white px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($completedSentDocs as $idx => $document)
                                    @php
                                        $statusClass = match(optional($document->status)->status) {
                                            'completed', 'complete' => 'bg-green-100 text-green-800',
                                            'acknowledged' => 'bg-indigo-100 text-indigo-800',
                                            'commented' => 'bg-cyan-100 text-cyan-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-slate-100 text-slate-800'
                                        };
                                        $lastWorkflow = $document->documentWorkflow->last();
                                        $recipientName = $lastWorkflow ? (optional($lastWorkflow->recipient)->first_name . ' ' . optional($lastWorkflow->recipient)->last_name) : 'N/A';
                                        $recipientOffice = $lastWorkflow ? optional($lastWorkflow->recipientOffice)->name : 'No Office';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $document->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-900">{{ $recipientName }}</div>
                                            <div class="text-xs text-slate-500">{{ $recipientOffice }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-900">{{ $document->updated_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-slate-500">{{ $document->updated_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst(optional($document->status)->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View
                                                </a>
                                                <a href="{{ route('documents.download', $document->id) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    Download
                                                </a>
                                                @if(optional($document->status)->status !== 'archived')
                                                    <form action="{{ route('documents.archive.store', $document->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                            Archive
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-slate-500 font-medium">No completed sent documents</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
