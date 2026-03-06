@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function openSignatureModal(imgUrl, name, position, action, date) {
        document.getElementById('sig-modal-img').src = imgUrl;
        document.getElementById('sig-modal-name').textContent = name;
        document.getElementById('sig-modal-position').textContent = position || '';
        document.getElementById('sig-modal-date').textContent = date;

        var actionEl = document.getElementById('sig-modal-action');
        actionEl.textContent = action;
        var colors = {Approved:'green',Rejected:'red',Acknowledged:'blue',Commented:'indigo',Returned:'yellow'};
        var c = colors[action] || 'gray';
        actionEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-' + c + '-100 text-' + c + '-700';

        var modal = document.getElementById('sig-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSignatureModal(e) {
        if (e && e.target !== e.currentTarget) return;
        document.getElementById('sig-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSignatureModal();
    });
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white">
    <div class="max-w-7xl mx-auto space-y-8 p-4 md:p-8">
        <!-- Header -->
        <div class="bg-white rounded-xl border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80 hover:shadow-sm">
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">{{ __('Document Details') }}</h1>
                        <p class="text-sm text-slate-500">View complete document information and history</p>
                    </div>
                </div>
                <a href="javascript:history.back()"
                    class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg shadow-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Progress Tracking Card -->
        <div class="bg-white rounded-xl border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80 hover:shadow-sm overflow-hidden">
            <div class="p-6">
                @php
                    // Sort audit logs by created_at timestamp in descending order
                    $sortedLogs = $auditLogs->sortByDesc('created_at');
                @endphp
                <div x-data="{ isOpen: true }" class="relative">
                    <div class="flex flex-wrap items-center justify-between mb-4 gap-2">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Document Audit Log
                        </h3>
                        <div class="flex items-center gap-2">
                            {{-- Export CSV --}}
                            <a href="{{ route('documents.audit.export', $document->id) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors"
                                title="Export audit log as CSV">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export CSV
                            </a>
                            {{-- Print --}}
                            <a href="{{ route('documents.audit.print', $document->id) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-colors"
                                title="Open printable audit log">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Print
                            </a>
                            {{-- Collapse/expand toggle --}}
                            <button @click="isOpen = !isOpen" class="flex items-center text-sm text-slate-500 hover:text-slate-700 focus:outline-none transition-colors border border-slate-200 bg-slate-50 hover:bg-slate-100 rounded-lg px-3 py-1.5">
                                <span x-text="isOpen ? 'Collapse' : 'Expand'" class="mr-1 text-xs font-medium"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="bg-white rounded-lg border border-slate-200 p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-medium text-slate-500">Current Status</h4>
                            @php
                                $latestLog = $sortedLogs->first();
                                $currentStatus = $latestLog ? $latestLog->status : ($document->status?->status ?? 'Pending');
                                $currentDotColor = match(strtolower($currentStatus)) {
                                    'pending' => 'yellow',
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    'received' => 'blue',
                                    'forwarded' => 'purple',
                                    'returned' => 'amber',
                                    'completed' => 'indigo',
                                    'needs_revision' => 'amber',
                                    'cancelled' => 'gray',
                                    'draft' => 'gray',
                                    default => 'gray'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-{{ $currentDotColor }}-500 text-white">
                                {{ ucfirst($currentStatus) }}
                            </span>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="max-h-[640px] overflow-y-auto pr-2">
                <div class="relative">
                    <!-- Progress Line -->
                    <div class="absolute h-full w-0.5 bg-slate-200 left-6 top-0"></div>

                    <!-- Timeline Items -->
                    <div class="space-y-8 relative">
                        @php
                            // Define status colors
                            $statusColors = [
                                'created' => ['bg' => 'bg-green-500', 'text' => 'text-green-800', 'light' => 'bg-green-100'],
                                'pending' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-800', 'light' => 'bg-yellow-100'],
                                'received' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-800', 'light' => 'bg-indigo-100'],
                                'approved' => ['bg' => 'bg-green-500', 'text' => 'text-green-800', 'light' => 'bg-green-100'],
                                'rejected' => ['bg' => 'bg-red-500', 'text' => 'text-red-800', 'light' => 'bg-red-100'],
                                'returned' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-800', 'light' => 'bg-amber-100'],
                                'forwarded' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-800', 'light' => 'bg-purple-100'],
                                'completed' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-800', 'light' => 'bg-indigo-100'],
                                'uploaded' => ['bg' => 'bg-green-500', 'text' => 'text-green-800', 'light' => 'bg-green-100'],
                                'needs_revision' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-800', 'light' => 'bg-amber-100'],
                                'cancelled' => ['bg' => 'bg-slate-500', 'text' => 'text-slate-800', 'light' => 'bg-slate-100'],
                                'draft' => ['bg' => 'bg-slate-500', 'text' => 'text-slate-800', 'light' => 'bg-slate-100']
                            ];

                            // Helper function to get status color with fallback
                            function getStatusColor($status, $type) {
                                global $statusColors;
                                if (!isset($statusColors) || !is_array($statusColors)) {
                                    // Fallback colors if $statusColors is not available
                                    return $type === 'bg' ? 'bg-slate-500' : ($type === 'light' ? 'bg-slate-100' : 'text-slate-800');
                                }

                                $defaultColors = [
                                    'bg' => 'bg-slate-500',
                                    'light' => 'bg-slate-100',
                                    'text' => 'text-slate-800'
                                ];

                                if ($status === null) {
                                    return $defaultColors[$type] ?? $defaultColors['bg'];
                                }

                                $status = strtolower($status);
                                if (!isset($statusColors[$status])) {
                                    return $defaultColors[$type] ?? $defaultColors['bg'];
                                }

                                return $statusColors[$status][$type] ?? $defaultColors[$type] ?? $defaultColors['bg'];
                            }
                        @endphp

                        @php
                            $currentStep = 1;
                            $totalSteps = $sortedLogs->count();
                        @endphp

                        @foreach($sortedLogs as $log)
                            <div class="flex items-start relative">
                                <!-- Timeline Point -->
                                <div class="flex-shrink-0 w-12 flex flex-col items-center">
                                    <div class="relative">
                                        @php
                                            $dotColor = match(strtolower($log->status ?? '')) {
                                                'pending' => 'yellow',
                                                'approved' => 'green',
                                                'rejected' => 'red',
                                                'received' => 'blue',
                                                'forwarded' => 'purple',
                                                'returned' => 'amber',
                                                'completed' => 'indigo',
                                                'needs_revision' => 'amber',
                                                'cancelled' => 'gray',
                                                'draft' => 'gray',
                                                default => 'gray'
                                            };
                                        @endphp
                                        <div class="bg-{{ $dotColor }}-500 h-4 w-4 rounded-full border-4 border-white shadow"></div>
                                    </div>
                                </div>

                                <!-- Timeline Content -->
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-slate-800">
                                            <span class="font-medium">
                                                {{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}
                                            </span>
                                            @if($log->action === 'created')
                                                created the document
                                            @elseif($log->action === 'updated')
                                                updated the document
                                            @elseif($log->action === 'forwarded')
                                                @php
                                                    $workflowEntry = $workflows->where('id', $log->workflow_id)->first();
                                                    $recipientOffice = $workflowEntry->recipientOffice->name ?? null;
                                                @endphp
                                                forwarded the document to {{ $recipientOffice ?? 'another office' }}
                                            @elseif($log->action === 'received')
                                                received the document
                                            @elseif($log->action === 'reviewed')
                                                reviewed the document
                                            @elseif($log->action === 'approved')
                                                approved the document
                                            @elseif($log->action === 'rejected')
                                                rejected the document
                                            @elseif($log->action === 'returned')
                                                returned the document
                                            @else
                                                @php
                                                    $action = strtolower($log->action ?? 'updated');
                                                    $action = str_replace('workflow', 'processed', $action);
                                                @endphp
                                                {{ $action }} the document
                                            @endif

                                            @if($log->status)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $dotColor }}-500 text-white ml-2">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            @endif
                                        </p>
                                        @if($log->details)
                                            <p class="text-sm text-slate-500 mt-0.5">{{ $log->details }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right text-sm whitespace-nowrap text-slate-500">
                                        <time datetime="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y H:i') }}</time>
                                    </div>
                                </div>
                            </div>
                            @php $currentStep++; @endphp
                        @endforeach
                    </div>
                </div>

                    {{-- Pagination & entry count --}}
                    @if($auditLogs->hasPages() || $auditLogs->total() > 0)
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-3 border-t border-slate-100">
                        <p class="text-xs text-slate-400">
                            Showing {{ $auditLogs->firstItem() }}–{{ $auditLogs->lastItem() }} of {{ $auditLogs->total() }} entr{{ $auditLogs->total() === 1 ? 'y' : 'ies' }}
                        </p>
                        @if($auditLogs->hasPages())
                        <div class="text-xs">
                            {{ $auditLogs->links() }}
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- End of Timeline -->
                </div>
            </div>
        </div>

        <!-- Error Message -->
        @if(session('error'))
        <div class="bg-white border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-md" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><strong>Error!</strong> {{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Document Details Card -->
        <div class="bg-white rounded-xl border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80 hover:shadow-sm overflow-hidden">
            <!-- Card Header -->
            <div class="p-6 border-b border-indigo-200/60">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-3">
                        <h2 class="text-xl font-semibold text-slate-800">{{ $document->title }}</h2>
                    </div>
                    @php
                        $statusColor = match(strtolower($document->status?->status ?? '')) {
                            'approved' => 'emerald',
                            'pending' => 'amber',
                            'forwarded' => 'blue',
                            'recalled' => 'purple',
                            'uploaded' => 'indigo',
                            'rejected' => 'red',
                            default => 'gray'
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 mt-2 md:mt-0">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $document->status?->status ?? "N/A" }}
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <!-- Document Information Grid with Attachment Card -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Tracking Number Card -->
                    <div class="bg-indigo-50/60 p-4 rounded-lg border border-indigo-200/60 transition-all duration-300 hover:border-indigo-300/80">
                        <div class="flex items-center mb-1">
                            <svg class="h-4 w-4 text-indigo-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            <p class="text-sm font-medium text-indigo-900">Tracking Number</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-base font-medium text-indigo-700">
                                {{ $document->trackingNumber->tracking_number ?? 'N/A' }}
                            </p>
                            @if($document->trackingNumber)
                            <button onclick="openBarcodeModal()" class="ml-2 p-1.5 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-600 hover:text-indigo-800 transition-colors" title="View Barcode">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    <!-- Classification Card -->
                    <div class="bg-indigo-50/60 p-4 rounded-lg border border-indigo-200/60 transition-all duration-300 hover:border-indigo-300/80">
                        <div class="flex items-center mb-1">
                            <svg class="h-4 w-4 text-indigo-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <p class="text-sm font-medium text-indigo-900">Classification</p>
                        </div>
                        <p class="text-base font-medium text-indigo-700">
                            {{ $document->categories->first()->category ?? 'N/A' }}
                        </p>
                    </div>
                    <!-- From Office Card -->
                    <div class="bg-emerald-50/60 p-4 rounded-lg border border-emerald-200/60 transition-all duration-300 hover:border-emerald-300/80">
                        <div class="flex items-center mb-1">
                            <svg class="h-4 w-4 text-emerald-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="text-sm font-medium text-emerald-900">From Office</p>
                        </div>
                        <p class="text-base font-medium text-emerald-700">
                            {{ $document->originatingOffice->name ?? ($document->user->offices->first()->name ?? 'N/A') }}
                        </p>
                    </div>
                    <!-- To Office Card -->
                    <div class="bg-purple-50/60 p-4 rounded-lg border border-purple-200/60 transition-all duration-300 hover:border-purple-300/80">
                        <div class="flex items-center mb-1">
                            <svg class="h-4 w-4 text-purple-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="text-sm font-medium text-purple-900">To Office</p>
                        </div>
                        <p class="text-base font-medium text-purple-700">
                            @if(isset($workflows) && $workflows->isNotEmpty())
                            @php
                            $officeNames = [];
                            foreach($workflows as $workflow) {
                            if($workflow->recipient_office && $workflow->recipientOffice) {
                            $officeNames[] = $workflow->recipientOffice->name;
                            }
                            }
                            @endphp

                            @if(count($officeNames) > 0)
                            {{ implode(', ', array_unique($officeNames)) }}
                            @else
                            N/A
                            @endif
                            @else
                            N/A
                            @endif
                        </p>
                    </div>
                    <!-- Status Card -->
                    <div class="bg-amber-50/60 p-4 rounded-lg border border-amber-200/60 transition-all duration-300 hover:border-amber-300/80">
                        <div class="flex items-center mb-1">
                            <svg class="h-4 w-4 text-amber-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-medium text-amber-900">Status</p>
                        </div>
                        <p class="text-base font-medium text-amber-700">
                            {{ $document->status?->status ?? "N/A" }}
                        </p>
                    </div>
                    <!-- Attachments Card -->
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <p class="text-sm font-medium text-slate-500 mb-2">Document File</p>
                        @if($document->path)
                        <button onclick="openDocViewer('{{ route('documents.preview', $document->id) }}', '{{ $document->title }}', '{{ route('documents.download', $document->id) }}')"
                            class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 transition-colors font-medium mb-3 cursor-pointer">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Main Document
                        </button>
                        @endif

                        <p class="text-sm font-medium text-slate-500 mb-2">Attachments</p>
                        @if($document->attachments->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($document->attachments as $attachment)
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <button onclick="openDocViewer('{{ route('attachments.preview', $attachment->id) }}', '{{ addslashes($attachment->filename) }}', '{{ route('documents.download', $attachment->id) }}')"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors font-medium truncate block text-left cursor-pointer">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            {{ $attachment->filename }}
                                        </span>
                                    </button>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        @if($attachment->uploader)
                                            <span class="text-slate-600">{{ $attachment->uploader->first_name }} {{ $attachment->uploader->last_name }}</span>
                                            <span class="mx-1">&middot;</span>
                                        @endif
                                        {{ $attachment->created_at->format('M d, Y g:ia') }}
                                        @if($attachment->storage_size)
                                            <span class="mx-1">&middot;</span>
                                            {{ number_format($attachment->storage_size / 1024, 1) }} KB
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-base font-medium text-slate-900">N/A</p>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-slate-50/60 p-4 rounded-lg border border-slate-200/60 transition-all duration-300 hover:border-slate-300/80 mb-8">
                    <div class="flex items-center mb-2">
                        <svg class="h-4 w-4 text-slate-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <p class="text-sm font-medium text-slate-700">Description</p>
                    </div>
                    <p class="text-base text-slate-600">{{ $document->description }}</p>
                </div>

                <!-- Urgency Analysis Panel -->
                @if($document->urgency_level)
                <div class="bg-{{ $document->urgency_color }}-50/60 p-4 rounded-lg border border-{{ $document->urgency_color }}-200/60 transition-all duration-300 hover:border-{{ $document->urgency_color }}-300/80 mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">{{ $document->urgency_icon }}</span>
                            <p class="text-sm font-semibold text-{{ $document->urgency_color }}-800">Urgency Analysis</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-{{ $document->urgency_color }}-100 text-{{ $document->urgency_color }}-700 ring-1 ring-{{ $document->urgency_color }}-200">
                            {{ $document->urgency_level }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        @if($document->urgency_reasoning)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-{{ $document->urgency_color }}-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-slate-600">{{ $document->urgency_reasoning }}</p>
                        </div>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            @if($document->urgency_confidence)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $document->urgency_confidence }}% confidence
                            </span>
                            @endif
                            @if($document->urgency_analyzed_at)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Analyzed {{ $document->urgency_analyzed_at->diffForHumans() }}
                            </span>
                            @endif
                            @if($document->escalation_count > 0)
                            <span class="flex items-center gap-1 text-red-500 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                {{ $document->escalation_count }} escalation(s)
                            </span>
                            @endif
                        </div>
                        @if($document->urgency_keywords && is_array(json_decode($document->urgency_keywords, true)))
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @foreach(array_slice(json_decode($document->urgency_keywords, true), 0, 8) as $kw)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-{{ $document->urgency_color }}-100/80 text-{{ $document->urgency_color }}-600">{{ $kw }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- E-Signatures -->
                <div class="bg-indigo-50/60 p-4 rounded-lg border border-indigo-200/60 transition-all duration-300 hover:border-indigo-300/80 mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <p class="text-sm font-medium text-indigo-900">E-Signatures</p>
                        </div>
                        @if($document->eSignatures->count())
                            <span class="text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">{{ $document->eSignatures->count() }} signature(s)</span>
                        @endif
                    </div>
                    @if($document->eSignatures->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($document->eSignatures as $sig)
                        <div class="bg-white rounded-lg border border-indigo-100 p-3 transition-all duration-300 hover:border-indigo-300 hover:shadow-sm cursor-pointer" onclick="openSignatureModal('{{ asset('storage/' . $sig->signature_path) }}', '{{ addslashes($sig->full_name) }}', '{{ addslashes($sig->position ?? '') }}', '{{ ucfirst($sig->action) }}', '{{ $sig->signed_at->format('M d, Y g:ia') }}')">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-20 h-14 rounded border border-slate-200 bg-white overflow-hidden">
                                    <img src="{{ asset('storage/' . $sig->signature_path) }}" alt="Signature" class="w-full h-full object-contain">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-800">{{ $sig->full_name }}</p>
                                    @if($sig->position)
                                        <p class="text-xs text-slate-500">{{ $sig->position }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1">
                                        @php
                                            $actionColors = ['approved'=>'green','rejected'=>'red','acknowledged'=>'blue','commented'=>'indigo','returned'=>'yellow'];
                                            $ac = $actionColors[$sig->action] ?? 'gray';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $ac }}-100 text-{{ $ac }}-700">
                                            {{ ucfirst($sig->action) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1">{{ $sig->signed_at->format('M d, Y g:ia') }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-base font-medium text-indigo-700">No signatures yet</p>
                    @endif
                </div>

                <!-- Version History -->
                <div x-data="{ versionOpen: true }" class="bg-white p-4 rounded-lg border border-slate-200 mb-8">
                    <div class="flex items-center justify-between mb-3 cursor-pointer" @click="versionOpen = !versionOpen">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-base font-semibold text-slate-800">Version History</h3>
                            @if($document->versions->count())
                                <span class="text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">{{ $document->versions->count() }} previous version(s)</span>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform" :class="versionOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>

                    <div x-show="versionOpen" x-transition>
                        {{-- Current Version --}}
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-emerald-200 bg-emerald-50/50 mb-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                Now
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-slate-800">Current Version</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Uploaded by {{ $document->user->first_name ?? '' }} {{ $document->user->last_name ?? '' }}
                                    &middot; {{ $document->updated_at->format('M d, Y g:ia') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button onclick="openDocViewer('{{ route('documents.previewCurrent', $document->id) }}', '{{ addslashes($document->title) }}', '{{ route('documents.download', $document->id) }}')"
                                   class="p-1.5 rounded-lg hover:bg-emerald-100 text-emerald-600 transition" title="Preview Current Version">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <a href="{{ route('documents.download', $document->id) }}"
                                   class="p-1.5 rounded-lg hover:bg-emerald-100 text-slate-400 hover:text-emerald-600 transition" title="Download Current">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Previous Versions --}}
                        @if($document->versions->count())
                            <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                                @foreach($document->versions as $version)
                                    <div class="flex items-start gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition group">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-slate-300 to-slate-400 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            v{{ $version->version_number }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-700">Version {{ $version->version_number }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                @if($version->uploader)
                                                    <span class="text-indigo-600">{{ $version->uploader->first_name }} {{ $version->uploader->last_name }}</span>
                                                    <span class="mx-1">&middot;</span>
                                                @endif
                                                {{ $version->created_at->format('M d, Y g:ia') }}
                                                @if($version->file_size)
                                                    <span class="mx-1">&middot;</span>
                                                    {{ $version->formatted_size }}
                                                @endif
                                            </p>
                                            @if($version->change_notes)
                                                <p class="text-xs text-slate-400 mt-1 italic">&ldquo;{{ $version->change_notes }}&rdquo;</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button onclick="openDocViewer('{{ route('documents.versionPreview', [$document->id, $version->id]) }}', 'Version {{ $version->version_number }} — {{ $document->title }}', '{{ Storage::disk('public')->url($version->file_path) }}')"
                                               class="p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-600 transition" title="Preview v{{ $version->version_number }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                            <a href="{{ Storage::disk('public')->url($version->file_path) }}" download
                                               class="p-1.5 rounded-lg hover:bg-indigo-100 text-slate-400 hover:text-indigo-600 transition" title="Download v{{ $version->version_number }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500 text-center py-3">No previous versions. This is the original document.</p>
                        @endif

                        {{-- Upload New Version Form --}}
                        @if($canUploadVersion ?? false)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <h4 class="text-sm font-medium text-slate-700 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Upload New Version
                            </h4>
                            <form id="show-version-upload-form" action="{{ route('documents.uploadVersion', $document->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="flex items-center justify-center px-4 py-3 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition mb-2">
                                    <svg class="w-5 h-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span class="text-sm text-slate-500" id="version-file-label">Choose a file...</span>
                                    <input type="file" name="version_file" id="show-version-file-input" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.odt,.ods,.odp,.rtf,.jpg,.jpeg,.png"
                                           onchange="document.getElementById('version-file-label').textContent = this.files[0]?.name || 'Choose a file...'">
                                </label>
                                <textarea name="version_notes" rows="2" class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm mb-2" placeholder="What changed in this version? (optional)"></textarea>
                                
                                {{-- Print/Copy Tracking Prompt --}}
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-2" x-data="{ recordPrint: false }">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="record_print" value="1" x-model="recordPrint"
                                               class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-xs font-medium text-amber-800">
                                            <svg class="w-3.5 h-3.5 inline-block mr-0.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Record print/copy before uploading
                                        </span>
                                    </label>
                                    @if(($totalPrintCopies ?? 0) > 0)
                                    <p class="text-xs text-amber-600 mt-1 ml-6">{{ $totalPrintCopies }} recorded {{ $totalPrintCopies === 1 ? 'copy' : 'copies' }} so far.</p>
                                    @endif
                                    <div x-show="recordPrint" x-collapse class="mt-2 ml-6 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs text-slate-500">Copies:</label>
                                            <input type="number" name="print_copies" value="1" min="1" max="999"
                                                   class="w-16 text-xs rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200">
                                        </div>
                                        <input type="text" name="print_reason" placeholder="Reason (optional)"
                                               class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200">
                                    </div>
                                </div>
                                
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload New Version
                                </button>
                                <p class="text-xs text-slate-400 mt-1.5">Max 10MB &middot; The current document will become a previous version</p>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Print/Copy Tracking -->
                <div x-data="{ printOpen: false }" class="bg-white p-4 rounded-lg border border-slate-200 mb-8">
                    <div class="flex items-center justify-between mb-3 cursor-pointer" @click="printOpen = !printOpen">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <h3 class="text-base font-semibold text-slate-800">Print Tracking</h3>
                            <span class="text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">{{ $totalPrintCopies ?? 0 }} total copies</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="event.stopPropagation(); openPrintModal()" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Record Print
                            </button>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="printOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <div x-show="printOpen" x-transition>
                        @if(isset($printHistory) && $printHistory->count() > 0)
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($printHistory as $print)
                                <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700">
                                                {{ $print->printer->first_name ?? '' }} {{ $print->printer->last_name ?? '' }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $print->created_at->format('M d, Y g:ia') }}
                                                @if($print->print_reason)
                                                    &middot; <span class="italic">{{ $print->print_reason }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                            {{ $print->copies }} {{ Str::plural('copy', $print->copies) }}
                                        </span>
                                        @if($print->version)
                                            <span class="block text-xs text-slate-400 mt-0.5">v{{ $print->version->version_number }}</span>
                                        @else
                                            <span class="block text-xs text-slate-400 mt-0.5">Current</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500 text-center py-3">No print records yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Barcode Overlay (for PDF documents) -->
                @if($canUploadVersion && strtolower(pathinfo($document->path, PATHINFO_EXTENSION)) === 'pdf')
                <div class="bg-white p-4 rounded-lg border border-slate-200 mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <h3 class="text-base font-semibold text-slate-800">Barcode Overlay</h3>
                        @if($document->barcode_applied)
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">Applied</span>
                        @else
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">Not applied</span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 mb-3">Overlay the tracking number barcode directly onto the PDF document.</p>

                    <form action="{{ route('documents.barcodeOverlay', $document->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">X (mm)</label>
                                <input type="number" name="barcode_x" value="{{ $document->barcode_settings['x'] ?? 10 }}" min="0" max="500"
                                    class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Y (mm)</label>
                                <input type="number" name="barcode_y" value="{{ $document->barcode_settings['y'] ?? 10 }}" min="0" max="800"
                                    class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Width (mm)</label>
                                <input type="number" name="barcode_width" value="{{ $document->barcode_settings['width'] ?? 60 }}" min="10" max="200"
                                    class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Height (mm)</label>
                                <input type="number" name="barcode_height" value="{{ $document->barcode_settings['height'] ?? 15 }}" min="5" max="100"
                                    class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Page</label>
                                <select name="barcode_page" class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                    <option value="1" {{ ($document->barcode_settings['page'] ?? 1) == 1 ? 'selected' : '' }}>First page</option>
                                    <option value="0" {{ ($document->barcode_settings['page'] ?? 1) == 0 ? 'selected' : '' }}>All pages</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Show text</label>
                                <select name="barcode_show_text" class="w-full text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                    <option value="1" {{ ($document->barcode_settings['show_text'] ?? true) ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !($document->barcode_settings['show_text'] ?? true) ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm"
                            onclick="return confirm('{{ $document->barcode_applied ? 'A barcode has already been applied. This will re-apply it with new settings. Continue?' : 'This will permanently modify the PDF file. Continue?' }}')">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            {{ $document->barcode_applied ? 'Re-apply Barcode' : 'Apply Barcode Overlay' }}
                        </button>
                    </form>
                </div>
                @endif

                <!-- Workflow Pipeline — Full Document Workflow (all steps, completed or not) -->
                @if(isset($workflows) && $workflows->isNotEmpty())
                <div class="bg-white p-4 rounded-lg border border-slate-200 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-indigo-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            <h3 class="text-base font-semibold text-slate-800">Document Workflow Pipeline</h3>
                        </div>
                        @php
                            $totalStepsCount = $workflows->whereNull('parent_workflow_id')->count();
                            $completedStepsCount = $workflows->whereNull('parent_workflow_id')->whereIn('status', ['approved','rejected','acknowledged','commented','returned','forwarded'])->count();
                            $pipelineProgress = $totalStepsCount > 0 ? round(($completedStepsCount / $totalStepsCount) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5">
                                <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $pipelineProgress >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $pipelineProgress }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-500">{{ $completedStepsCount }}/{{ $totalStepsCount }}</span>
                            </div>
                            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $workflows->first()->workflow_type ?? 'parallel' }}</span>
                            {{-- Expand / Collapse All --}}
                            <div class="flex items-center gap-1 border-l border-slate-200 pl-3">
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('expand-all-workflows'))" class="px-2 py-0.5 text-[10px] font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded transition-colors" title="Expand all steps and sub-workflows">
                                    <svg class="w-3 h-3 inline -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    Expand
                                </button>
                                <button type="button" onclick="window.dispatchEvent(new CustomEvent('collapse-all-workflows'))" class="px-2 py-0.5 text-[10px] font-medium text-slate-500 bg-slate-50 hover:bg-slate-100 rounded transition-colors" title="Collapse all steps and sub-workflows">
                                    <svg class="w-3 h-3 inline -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/></svg>
                                    Collapse
                                </button>
                            </div>
                        </div>
                    </div>

                    @php
                        $topLevelWorkflows = $workflows->whereNull('parent_workflow_id');
                        $workflowsByStep = $topLevelWorkflows->groupBy('step_order');
                        $isSequentialWorkflow = $workflows->where('workflow_type', 'sequential')->isNotEmpty();
                        $maxStep = $workflowsByStep->keys()->max();

                        $wfStatusConfig = [
                            'waiting'      => ['icon' => 'clock',       'color' => 'slate',   'label' => 'Waiting',      'ring' => 'ring-slate-200'],
                            'pending'      => ['icon' => 'arrow-right', 'color' => 'yellow',  'label' => 'Pending',      'ring' => 'ring-yellow-300'],
                            'received'     => ['icon' => 'inbox',       'color' => 'blue',    'label' => 'Received',     'ring' => 'ring-blue-300'],
                            'approved'     => ['icon' => 'check',       'color' => 'green',   'label' => 'Approved',     'ring' => 'ring-green-300'],
                            'rejected'     => ['icon' => 'x',           'color' => 'red',     'label' => 'Rejected',     'ring' => 'ring-red-300'],
                            'returned'     => ['icon' => 'reply',       'color' => 'amber',   'label' => 'Returned',     'ring' => 'ring-amber-300'],
                            'acknowledged' => ['icon' => 'check',       'color' => 'indigo',  'label' => 'Acknowledged', 'ring' => 'ring-indigo-300'],
                            'commented'    => ['icon' => 'chat',        'color' => 'cyan',    'label' => 'Commented',    'ring' => 'ring-cyan-300'],
                            'forwarded'    => ['icon' => 'forward',     'color' => 'purple',  'label' => 'Forwarded',    'ring' => 'ring-purple-300'],
                        ];
                        $purposeLabels = [
                            'appropriate_action' => 'Action Required',
                            'dissemination' => 'Dissemination',
                            'for_comment' => 'For Comment',
                        ];
                    @endphp

                    {{-- Origin node --}}
                    <div class="flex items-center gap-3 mb-2 ml-1">
                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700">Sent by {{ $document->user->first_name ?? '' }} {{ $document->user->last_name ?? '' }}</span>
                            <span class="text-xs text-slate-400 ml-2">{{ $document->created_at->format('M d, Y g:ia') }}</span>
                        </div>
                    </div>

                    {{-- Pipeline steps --}}
                    <div class="relative ml-4 pl-6 border-l-2 border-slate-200 space-y-1 py-2">
                        @foreach($workflowsByStep->sortKeys() as $step => $stepWorkflows)
                            @php
                                $stepDone = $stepWorkflows->every(fn($w) => in_array($w->status, ['approved','rejected','acknowledged','commented','returned','forwarded']));
                                $stepActive = $stepWorkflows->contains(fn($w) => in_array($w->status, ['received','pending']));
                                $stepId = 'pipeline-step-' . $step;
                            @endphp

                            {{-- Step header for sequential workflows (collapsible) --}}
                            @if($isSequentialWorkflow)
                            <div x-data="{ stepOpen: {{ $stepDone ? 'false' : 'true' }} }"
                                 @expand-all-workflows.window="stepOpen = true"
                                 @collapse-all-workflows.window="stepOpen = false"
                                 id="{{ $stepId }}">
                                <div class="flex items-center gap-2 -ml-[31px] mb-2 mt-3 first:mt-0 cursor-pointer select-none" @click="stepOpen = !stepOpen">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $stepDone ? 'bg-emerald-500 text-white' : ($stepActive ? 'bg-indigo-500 text-white ring-4 ring-indigo-100' : 'bg-slate-200 text-slate-500') }}">
                                        @if($stepDone)
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $step }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-semibold uppercase tracking-wider {{ $stepDone ? 'text-emerald-700' : ($stepActive ? 'text-indigo-700' : 'text-slate-400') }}">
                                        Step {{ $step }}
                                        @if($stepDone) — Completed @elseif($stepActive) — In Progress @else — Waiting @endif
                                    </span>
                                    {{-- Toggle chevron --}}
                                    <svg class="w-3 h-3 transition-transform duration-200 {{ $stepDone ? 'text-emerald-400' : ($stepActive ? 'text-indigo-400' : 'text-slate-300') }}"
                                         :class="stepOpen && 'rotate-90'"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    {{-- Collapsed summary --}}
                                    <template x-if="!stepOpen">
                                        <span class="text-[10px] text-slate-400 ml-1">{{ $stepWorkflows->count() }} recipient{{ $stepWorkflows->count() > 1 ? 's' : '' }}</span>
                                    </template>
                                </div>

                                {{-- Collapsible step content --}}
                                <div x-show="stepOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            @endif

                            @foreach($stepWorkflows as $wf)
                                @php
                                    $cfg = $wfStatusConfig[$wf->status] ?? ['icon' => 'minus', 'color' => 'slate', 'label' => ucfirst($wf->status), 'ring' => 'ring-slate-200'];
                                    $isDone = in_array($wf->status, ['approved','rejected','acknowledged','commented','returned','forwarded']);
                                    $isActive = in_array($wf->status, ['received','pending']);
                                @endphp
                                <div class="relative flex items-start gap-3 py-2 group">
                                    {{-- Connector dot --}}
                                    <div class="absolute -left-[31px] top-3 w-4 h-4 rounded-full border-2 border-white shadow-sm
                                        {{ $isDone ? 'bg-'.$cfg['color'].'-500' : ($isActive ? 'bg-'.$cfg['color'].'-400 ring-4 '.$cfg['ring'] : 'bg-slate-200') }}">
                                    </div>

                                    {{-- Workflow card --}}
                                    <div class="flex-1 bg-{{ $isDone ? $cfg['color'].'-50' : ($isActive ? 'white' : 'slate-50') }} rounded-lg border {{ $isDone ? 'border-'.$cfg['color'].'-200' : ($isActive ? 'border-'.$cfg['color'].'-200 shadow-sm' : 'border-slate-200 border-dashed') }} p-3 transition-all">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-{{ $cfg['color'] }}-400 to-{{ $cfg['color'] }}-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                    {{ $wf->recipient ? strtoupper(substr($wf->recipient->first_name ?? '?', 0, 1)) : '?' }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-800">
                                                        {{ $wf->recipient ? ($wf->recipient->first_name . ' ' . $wf->recipient->last_name) : 'Unknown' }}
                                                    </p>
                                                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                                        @if($wf->recipientOffice)
                                                            <span class="text-xs text-slate-500">{{ $wf->recipientOffice->name }}</span>
                                                            <span class="text-slate-300">&middot;</span>
                                                        @endif
                                                        @if($wf->purpose)
                                                            <span class="text-xs text-slate-400">{{ $purposeLabels[$wf->purpose] ?? ucfirst($wf->purpose) }}</span>
                                                        @endif
                                                    </div>
                                                    @if($wf->remarks)
                                                        <p class="text-xs text-slate-500 mt-1.5 italic">&ldquo;{{ $wf->remarks }}&rdquo;</p>
                                                    @endif
                                                    @if($wf->received_at)
                                                        <p class="text-xs text-slate-400 mt-1">Responded: {{ \Carbon\Carbon::parse($wf->received_at)->format('M d, Y g:ia') }}</p>
                                                    @elseif($wf->created_at)
                                                        <p class="text-xs text-slate-400 mt-1">Sent: {{ $wf->created_at->format('M d, Y g:ia') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $cfg['color'] }}-100 text-{{ $cfg['color'] }}-700">
                                                {{ $cfg['label'] }}
                                            </span>
                                            @if(($canReroute ?? false) && $isActive && $wf->status !== 'received')
                                            <button type="button"
                                                onclick="openRerouteModal({{ $wf->id }}, '{{ addslashes($wf->recipient ? ($wf->recipient->first_name . ' ' . $wf->recipient->last_name) : 'Unknown') }}', {{ $document->id }})"
                                                class="flex-shrink-0 ml-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors cursor-pointer"
                                                title="Reroute this workflow step">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                Reroute
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Sub-workflows (forwarded-from-review) — recursive --}}
                                @include('documents.partials.sub-workflows', [
                                    'childWorkflows' => $wf->childWorkflows,
                                    'wfStatusConfig' => $wfStatusConfig,
                                    'depth' => 1,
                                ])
                            @endforeach

                            @if($isSequentialWorkflow)
                                </div>{{-- /x-show stepOpen --}}
                            </div>{{-- /x-data step --}}
                            @endif

                            {{-- Connector arrow between steps --}}
                            @if($isSequentialWorkflow && $step < $maxStep)
                                <div class="flex items-center -ml-[25px] py-1">
                                    <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- End node --}}
                    @php
                        $allDone = $topLevelWorkflows->every(fn($w) => in_array($w->status, ['approved','rejected','acknowledged','commented','returned','forwarded']));
                    @endphp
                    <div class="flex items-center gap-3 mt-2 ml-1">
                        <div class="flex-shrink-0 w-8 h-8 {{ $allDone ? 'bg-gradient-to-br from-emerald-500 to-emerald-600' : 'bg-slate-200' }} rounded-full flex items-center justify-center shadow-sm">
                            @if($allDone)
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div>
                            <span class="text-sm font-medium {{ $allDone ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $allDone ? 'Workflow Complete' : 'Workflow In Progress' }}
                            </span>
                            @if($allDone && $document->updated_at)
                                <span class="text-xs text-slate-400 ml-2">{{ $document->updated_at->format('M d, Y g:ia') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="flex flex-wrap gap-3">
                            @foreach(['approved' => 'Approved', 'rejected' => 'Rejected', 'received' => 'Received', 'pending' => 'Pending', 'waiting' => 'Waiting'] as $sKey => $sLabel)
                                @php $sc = $wfStatusConfig[$sKey]; @endphp
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full bg-{{ $sc['color'] }}-500"></div>
                                    <span class="text-[10px] text-slate-500">{{ $sLabel }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-8">
                    <div class="flex items-center gap-2 text-slate-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium">No workflow has been created for this document yet.</p>
                    </div>
                </div>
                @endif


                <!-- Reroute History -->
                @if(isset($rerouteLogs) && $rerouteLogs->count())
                <div class="bg-amber-50/60 p-4 rounded-lg border border-amber-200/60 mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <p class="text-sm font-semibold text-amber-800">Reroute History</p>
                        <span class="text-xs text-amber-500">({{ $rerouteLogs->count() }})</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($rerouteLogs as $log)
                        <div class="bg-white rounded-lg border border-amber-100 p-3">
                            <div class="flex items-start justify-between">
                                <div class="text-sm">
                                    <span class="text-slate-600">From</span>
                                    <span class="font-medium text-slate-800">{{ $log->old_recipient_name }}</span>
                                    <span class="text-slate-600">to</span>
                                    <span class="font-medium text-slate-800">{{ $log->new_recipient_name }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">By {{ $log->rerouted_by_name }} &mdash; {{ $log->reason }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
            </div>
        </div>

        <!-- Status Banners -->
        @if($document->status === 'needs_revision')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-amber-700">This document was rejected and needs revision.</p>
                    @if(isset($workflow) && $workflow->remarks)
                    <p class="mt-2 text-sm text-amber-700"><strong>Rejection remarks:</strong> {{ $workflow->remarks }}</p>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('documents.edit', $document->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Revise Document</a>
                        <form action="{{ route('documents.cancel', $document->id) }}" method="POST" class="inline-block ml-2">@csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50">Cancel Workflow</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($document->status === 'returned')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-amber-700">This document was returned to you for updates.</p>
                    @php $returnedWorkflow = $document->documentWorkflow()->where('status', 'returned')->first(); @endphp
                    @if($returnedWorkflow && $returnedWorkflow->remarks)
                    <p class="mt-2 text-sm text-amber-700"><strong>Return remarks:</strong> {{ $returnedWorkflow->remarks }}</p>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('documents.edit', $document->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Update Document</a>
                        <form action="{{ route('documents.cancel', $document->id) }}" method="POST" class="inline-block ml-2">@csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50">Cancel Workflow</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Signature Modal -->
<div id="sig-modal" class="fixed inset-0 z-50 hidden" onclick="closeSignatureModal(event)">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 id="sig-modal-name" class="text-lg font-semibold text-slate-800"></h3>
                    <p id="sig-modal-position" class="text-sm text-slate-500"></p>
                </div>
                <button onclick="closeSignatureModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 bg-slate-50 flex items-center justify-center">
                <img id="sig-modal-img" src="" alt="Signature" class="max-w-full max-h-64 object-contain">
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <span id="sig-modal-action" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"></span>
                <span id="sig-modal-date" class="text-xs text-slate-400"></span>
            </div>
        </div>
    </div>
</div>

<!-- Reroute Modal -->
@if($canReroute ?? false)
<div id="reroute-modal" class="fixed inset-0 z-50 hidden" onclick="closeRerouteModal(event)">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <h3 class="text-lg font-semibold text-slate-800">Reroute Workflow Step</h3>
                    </div>
                    <button onclick="closeRerouteModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="text-sm text-slate-500 mt-1">Reassign from <strong id="reroute-current-user" class="text-slate-700"></strong></p>
            </div>
            <form id="reroute-form" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">New Recipient</label>
                    <input type="hidden" id="reroute-recipient" name="new_recipient_id" required />
                    <div class="relative">
                        <input type="text" id="reroute-search" autocomplete="off"
                            placeholder="Search by name or email..."
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 pr-8" />
                        <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div id="reroute-results" class="mt-1 max-h-48 overflow-y-auto rounded-lg border border-slate-200 bg-white hidden">
                        <!-- Populated by JS -->
                    </div>
                    <p id="reroute-selected-label" class="text-xs text-emerald-600 mt-1 hidden">
                        <span class="font-medium">Selected:</span> <span id="reroute-selected-name"></span>
                    </p>
                    <p id="reroute-loading" class="text-xs text-slate-400 mt-1 hidden">Loading recipients...</p>
                    <p id="reroute-error" class="text-xs text-red-500 mt-1 hidden">Failed to load recipients.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reason for Rerouting</label>
                    <textarea name="reason" required rows="3" maxlength="500" placeholder="Explain why this step is being rerouted..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 resize-none"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeRerouteModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-sm transition">Reroute</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let rerouteAllUsers = [];

function openRerouteModal(workflowId, currentUser, documentId) {
    const modal = document.getElementById('reroute-modal');
    const form = document.getElementById('reroute-form');
    const searchInput = document.getElementById('reroute-search');
    const hiddenInput = document.getElementById('reroute-recipient');
    const currentUserEl = document.getElementById('reroute-current-user');
    const loadingEl = document.getElementById('reroute-loading');
    const errorEl = document.getElementById('reroute-error');
    const selectedLabel = document.getElementById('reroute-selected-label');
    const resultsDiv = document.getElementById('reroute-results');

    // Reset state
    currentUserEl.textContent = currentUser;
    form.action = `/documents/workflows/${workflowId}/reroute`;
    searchInput.value = '';
    hiddenInput.value = '';
    selectedLabel.classList.add('hidden');
    resultsDiv.classList.add('hidden');
    errorEl.classList.add('hidden');
    loadingEl.classList.remove('hidden');
    rerouteAllUsers = [];

    modal.classList.remove('hidden');

    // Fetch recipients with proper headers
    fetch(`/documents/workflows/${documentId}/reroute-recipients`, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
    })
    .then(users => {
        loadingEl.classList.add('hidden');
        rerouteAllUsers = users;
        renderRerouteResults(users);
        if (users.length === 0) {
            resultsDiv.innerHTML = '<p class="px-3 py-2 text-sm text-slate-400">No recipients available.</p>';
            resultsDiv.classList.remove('hidden');
        }
    })
    .catch((err) => {
        console.error('Reroute recipients fetch error:', err);
        loadingEl.classList.add('hidden');
        errorEl.classList.remove('hidden');
    });
}

function renderRerouteResults(users) {
    const resultsDiv = document.getElementById('reroute-results');
    if (users.length === 0) {
        resultsDiv.innerHTML = '<p class="px-3 py-2 text-sm text-slate-400">No matching recipients.</p>';
        resultsDiv.classList.remove('hidden');
        return;
    }

    resultsDiv.innerHTML = users.map(u => `
        <button type="button"
            onclick="selectRerouteRecipient(${u.id}, '${u.name.replace(/'/g, "\\'")} (${u.email})')"
            class="w-full text-left px-3 py-2 text-sm hover:bg-amber-50 transition-colors flex items-center justify-between border-b border-slate-100 last:border-0">
            <div class="min-w-0">
                <span class="font-medium text-slate-700">${u.name}</span>
                ${u.office ? `<span class="text-xs text-amber-600 ml-1.5 bg-amber-50 px-1.5 py-0.5 rounded">${u.office}</span>` : ''}
                <p class="text-xs text-slate-400 truncate">${u.email}</p>
            </div>
            <svg class="w-4 h-4 text-slate-300 flex-shrink-0 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    `).join('');
    resultsDiv.classList.remove('hidden');
}

function selectRerouteRecipient(id, label) {
    const hiddenInput = document.getElementById('reroute-recipient');
    const searchInput = document.getElementById('reroute-search');
    const selectedLabel = document.getElementById('reroute-selected-label');
    const selectedName = document.getElementById('reroute-selected-name');
    const resultsDiv = document.getElementById('reroute-results');

    hiddenInput.value = id;
    searchInput.value = '';
    selectedName.textContent = label;
    selectedLabel.classList.remove('hidden');
    resultsDiv.classList.add('hidden');
}

// Live search filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('reroute-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (!query) {
                renderRerouteResults(rerouteAllUsers);
                return;
            }
            const filtered = rerouteAllUsers.filter(u =>
                u.name.toLowerCase().includes(query) || u.email.toLowerCase().includes(query)
            );
            renderRerouteResults(filtered);
        });

        searchInput.addEventListener('focus', function() {
            if (rerouteAllUsers.length > 0) {
                const query = this.value.toLowerCase().trim();
                const filtered = query
                    ? rerouteAllUsers.filter(u => u.name.toLowerCase().includes(query) || u.email.toLowerCase().includes(query))
                    : rerouteAllUsers;
                renderRerouteResults(filtered);
            }
        });
    }
});

function closeRerouteModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('reroute-modal').classList.add('hidden');
}
</script>
@endif

{{-- ═══ Document Viewer Modal ═══ --}}
<div id="doc-viewer-modal" class="hidden fixed inset-0 z-[60] overflow-hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDocViewer()"></div>

    {{-- Modal Content --}}
    <div class="relative flex flex-col h-full max-w-6xl mx-auto p-4 sm:p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between bg-white rounded-t-xl px-5 py-3 border-b border-slate-200 shadow-sm">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 id="doc-viewer-title" class="text-sm font-semibold text-slate-800 truncate">Document</h3>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Open in new tab --}}
                <a id="doc-viewer-newtab" href="#" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
                    title="Open in new tab">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    New Tab
                </a>
                {{-- Download --}}
                <a id="doc-viewer-download" href="#"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors"
                    title="Download file">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                </a>
                {{-- Close --}}
                <button onclick="closeDocViewer()"
                    class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Viewer Body --}}
        <div class="flex-1 bg-white rounded-b-xl overflow-hidden shadow-xl relative min-h-0">
            {{-- Loading spinner --}}
            <div id="doc-viewer-loading" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                <div class="flex flex-col items-center gap-3">
                    <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm text-slate-500">Loading document...</p>
                </div>
            </div>

            {{-- iframe for PDF/documents --}}
            <iframe id="doc-viewer-frame" class="w-full h-full border-0 hidden"></iframe>

            {{-- Image viewer --}}
            <div id="doc-viewer-image" class="hidden w-full h-full flex items-center justify-center overflow-auto p-4 bg-slate-100">
                <img id="doc-viewer-img" class="max-w-full max-h-full object-contain rounded shadow-lg" alt="Document preview" />
            </div>

            {{-- DOCX viewer (mammoth.js) --}}
            <div id="doc-viewer-docx" class="hidden w-full h-full overflow-auto p-6 bg-white"></div>

            {{-- XLSX/CSV viewer (SheetJS) --}}
            <div id="doc-viewer-xlsx" class="hidden w-full h-full overflow-auto p-4 bg-white"></div>

            {{-- Unsupported format fallback --}}
            <div id="doc-viewer-unsupported" class="hidden w-full h-full flex items-center justify-center">
                <div class="text-center p-8">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-slate-600 font-medium mb-2">Preview not available for this file type</p>
                    <p class="text-sm text-slate-400 mb-4">You can download the file to view it on your device.</p>
                    <a id="doc-viewer-fallback-download" href="#"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Modal (replaces QR Code Modal) -->
<div id="barcodeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeBarcodeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
            <button onclick="closeBarcodeModal()" class="absolute top-3 right-3 p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-semibold text-slate-800 mb-1 flex items-center">
                <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Barcode
            </h3>
            <p class="text-sm text-slate-500 mb-4 font-mono">{{ $document->trackingNumber->tracking_number ?? '' }}</p>
            <div class="flex justify-center mb-5">
                <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-sm">
                    <img id="barcodeImage" src="" alt="Barcode" class="max-w-full h-auto" style="min-width: 200px; min-height: 50px;">
                    <p class="text-center text-xs text-slate-500 font-mono mt-2">{{ $document->trackingNumber->tracking_number ?? '' }}</p>
                </div>
            </div>
            @if($document->barcode_applied)
                <div class="mb-4 p-2 bg-emerald-50 border border-emerald-200 rounded-lg text-center">
                    <span class="text-xs text-emerald-700 font-medium">Barcode has been overlaid on the document PDF</span>
                </div>
            @endif
            <div class="flex justify-center gap-3">
                <a id="barcodeDownloadLink" href="" download="barcode-{{ $document->trackingNumber->tracking_number ?? 'code' }}.png"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-medium rounded-lg hover:from-emerald-600 hover:to-green-700 shadow-sm transition-all">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download PNG
                </a>
                <button onclick="closeBarcodeModal()" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print Tracking Modal -->
<div id="printTrackingModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closePrintModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full relative overflow-hidden" style="max-height: 85vh;">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-lg">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Record Print</h3>
                        <p class="text-xs text-indigo-100">Track copies printed for this document</p>
                    </div>
                </div>
                <button onclick="closePrintModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/20 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Print Summary -->
                <div class="mb-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200/60 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-900">Total Copies Printed</p>
                        <p class="text-xs text-indigo-600">Across {{ $printHistory->count() ?? 0 }} print event(s)</p>
                    </div>
                    <span class="text-2xl font-bold text-indigo-700">{{ $totalPrintCopies ?? 0 }}</span>
                </div>

                <!-- Record New Print Form -->
                <form action="{{ route('documents.recordPrint', $document->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Number of Copies</label>
                            <input type="number" name="copies" value="1" min="1" max="999" required
                                class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Version</label>
                            <select name="version_id" class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                <option value="">Current version</option>
                                @foreach($document->versions as $ver)
                                    <option value="{{ $ver->id }}">v{{ $ver->version_number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Reason (optional)</label>
                        <input type="text" name="print_reason" maxlength="500" placeholder="e.g., For office distribution, For filing..."
                            class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Record Print
                    </button>
                </form>

                <!-- Print History -->
                @if(isset($printHistory) && $printHistory->count() > 0)
                <div class="border-t border-slate-100 pt-4">
                    <h4 class="text-sm font-semibold text-slate-700 mb-2">Print History</h4>
                    <div class="max-h-48 overflow-y-auto space-y-2">
                        @foreach($printHistory as $print)
                        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg text-sm">
                            <div>
                                <p class="font-medium text-slate-700">
                                    {{ $print->printer->first_name ?? '' }} {{ $print->printer->last_name ?? '' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $print->created_at->format('M d, Y g:ia') }}
                                    @if($print->print_reason)
                                        &middot; {{ $print->print_reason }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ $print->copies }} {{ Str::plural('copy', $print->copies) }}
                                </span>
                                @if($print->version)
                                    <span class="block text-xs text-slate-400 mt-0.5">v{{ $print->version->version_number }}</span>
                                @else
                                    <span class="block text-xs text-slate-400 mt-0.5">Current</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    const docExts = ['doc', 'docx'];
    const sheetExts = ['xls', 'xlsx', 'csv'];
    const previewableExts = ['pdf', ...imageExts, ...docExts, ...sheetExts];

    function getExtension(filename) {
        return (filename || '').split('.').pop().toLowerCase();
    }

    function renderDocxInModal(url, container) {
        container.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-indigo-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-slate-500">Loading document...</span></div>';
        fetch(url)
            .then(function(res) { return res.arrayBuffer(); })
            .then(function(buf) { return mammoth.convertToHtml({ arrayBuffer: buf }); })
            .then(function(result) {
                container.innerHTML = '<div class="prose prose-sm max-w-none">' + result.value + '</div>';
            })
            .catch(function(err) {
                container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Failed to render document.</p><p class="text-xs text-slate-400 mt-1">' + err.message + '</p></div>';
            });
    }

    function renderXlsxInModal(url, container) {
        container.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-slate-500">Loading spreadsheet...</span></div>';
        fetch(url)
            .then(function(res) { return res.arrayBuffer(); })
            .then(function(buf) {
                var wb = XLSX.read(buf, { type: 'array' });
                var html = '';
                if (wb.SheetNames.length > 1) {
                    html += '<div class="flex gap-1 mb-3 flex-wrap">';
                    wb.SheetNames.forEach(function(name, i) {
                        html += '<button onclick="modalSwitchSheet(this, ' + i + ')" class="px-3 py-1 text-xs rounded-md border ' + (i === 0 ? 'bg-indigo-100 border-indigo-300 text-indigo-700 font-medium' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100') + '">' + name + '</button>';
                    });
                    html += '</div>';
                }
                wb.SheetNames.forEach(function(name, i) {
                    var sheet = wb.Sheets[name];
                    var tableHtml = XLSX.utils.sheet_to_html(sheet, { editable: false });
                    html += '<div class="modal-sheet-content" data-sheet="' + i + '" style="' + (i > 0 ? 'display:none;' : '') + '">' + tableHtml + '</div>';
                });
                container.innerHTML = html;
                container.querySelectorAll('table').forEach(function(t) {
                    t.className = 'w-full text-xs border-collapse';
                    t.querySelectorAll('td, th').forEach(function(cell) {
                        cell.className = 'border border-slate-200 px-2 py-1 text-slate-700';
                    });
                    t.querySelectorAll('th').forEach(function(th) {
                        th.className += ' bg-slate-100 font-medium text-slate-800';
                    });
                });
            })
            .catch(function(err) {
                container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Failed to render spreadsheet.</p><p class="text-xs text-slate-400 mt-1">' + err.message + '</p></div>';
            });
    }

    window.modalSwitchSheet = function(btn, index) {
        btn.parentElement.querySelectorAll('button').forEach(function(b) {
            b.className = 'px-3 py-1 text-xs rounded-md border bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100';
        });
        btn.className = 'px-3 py-1 text-xs rounded-md border bg-indigo-100 border-indigo-300 text-indigo-700 font-medium';
        var viewer = btn.closest('#doc-viewer-xlsx') || document.getElementById('doc-viewer-xlsx');
        viewer.querySelectorAll('.modal-sheet-content').forEach(function(s) {
            s.style.display = parseInt(s.dataset.sheet) === index ? '' : 'none';
        });
    };

    function openDocViewer(previewUrl, title, downloadUrl) {
        const modal = document.getElementById('doc-viewer-modal');
        const titleEl = document.getElementById('doc-viewer-title');
        const frame = document.getElementById('doc-viewer-frame');
        const imageDiv = document.getElementById('doc-viewer-image');
        const imgEl = document.getElementById('doc-viewer-img');
        const docxDiv = document.getElementById('doc-viewer-docx');
        const xlsxDiv = document.getElementById('doc-viewer-xlsx');
        const unsupported = document.getElementById('doc-viewer-unsupported');
        const loading = document.getElementById('doc-viewer-loading');
        const downloadBtn = document.getElementById('doc-viewer-download');
        const newtabBtn = document.getElementById('doc-viewer-newtab');
        const fallbackBtn = document.getElementById('doc-viewer-fallback-download');

        // Set title and download links
        titleEl.textContent = title;
        downloadBtn.href = downloadUrl;
        newtabBtn.href = previewUrl;
        fallbackBtn.href = downloadUrl;

        // Reset visibility
        frame.classList.add('hidden');
        imageDiv.classList.add('hidden');
        docxDiv.classList.add('hidden');
        xlsxDiv.classList.add('hidden');
        unsupported.classList.add('hidden');
        loading.classList.remove('hidden');

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        const ext = getExtension(title);

        if (imageExts.includes(ext)) {
            // Image preview
            imgEl.onload = () => loading.classList.add('hidden');
            imgEl.onerror = () => {
                loading.classList.add('hidden');
                imageDiv.classList.add('hidden');
                unsupported.classList.remove('hidden');
            };
            imgEl.src = previewUrl;
            imageDiv.classList.remove('hidden');
        } else if (ext === 'pdf') {
            // PDF preview via iframe (no sandbox for better compatibility)
            frame.onload = () => loading.classList.add('hidden');
            frame.src = previewUrl;
            frame.classList.remove('hidden');
        } else if (docExts.includes(ext)) {
            // DOCX preview via mammoth.js
            loading.classList.add('hidden');
            docxDiv.classList.remove('hidden');
            renderDocxInModal(previewUrl, docxDiv);
        } else if (sheetExts.includes(ext)) {
            // Excel/CSV preview via SheetJS
            loading.classList.add('hidden');
            xlsxDiv.classList.remove('hidden');
            renderXlsxInModal(previewUrl, xlsxDiv);
        } else {
            // Unsupported — show download fallback
            loading.classList.add('hidden');
            unsupported.classList.remove('hidden');
        }

        // Close on Escape key
        document.addEventListener('keydown', docViewerEscHandler);
    }

    function closeDocViewer() {
        const modal = document.getElementById('doc-viewer-modal');
        const frame = document.getElementById('doc-viewer-frame');
        const imgEl = document.getElementById('doc-viewer-img');
        const docxDiv = document.getElementById('doc-viewer-docx');
        const xlsxDiv = document.getElementById('doc-viewer-xlsx');

        modal.classList.add('hidden');
        document.body.style.overflow = '';

        // Clean up to stop loading
        frame.src = '';
        imgEl.src = '';
        docxDiv.innerHTML = '';
        xlsxDiv.innerHTML = '';

        document.removeEventListener('keydown', docViewerEscHandler);
    }

    function docViewerEscHandler(e) {
        if (e.key === 'Escape') closeDocViewer();
    }
</script>

{{-- ═══ Workflow Zoom / Focus Modal ═══ --}}
<div id="workflow-zoom-modal" class="hidden fixed inset-0 z-[55] overflow-hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeWorkflowZoom()"></div>
    {{-- Panel --}}
    <div class="relative flex flex-col max-w-5xl mx-auto my-[3vh] h-[94vh] bg-white rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-shrink-0 bg-gradient-to-r from-purple-50 to-indigo-50">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
                <h3 class="text-lg font-semibold text-slate-800">Sub-Workflow Detail View</h3>
                <span id="workflow-zoom-badge" class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full"></span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="zoomExpandAll()" class="px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    Expand All
                </button>
                <button type="button" onclick="zoomCollapseAll()" class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/></svg>
                    Collapse All
                </button>
                <button onclick="closeWorkflowZoom()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        {{-- Scrollable content --}}
        <div id="workflow-zoom-content" class="flex-1 overflow-y-auto p-6">
            <p class="text-sm text-slate-400">Select a sub-workflow to zoom in.</p>
        </div>
    </div>
</div>

<script>
    // ── Workflow Zoom/Focus ──
    let _zoomSourceEl = null;
    let _zoomPlaceholder = null;
    let _zoomOriginalClasses = '';

    function openWorkflowZoom(sourceId) {
        const source = document.getElementById(sourceId);
        if (!source) return;

        const modal = document.getElementById('workflow-zoom-modal');
        const container = document.getElementById('workflow-zoom-content');
        const badge = document.getElementById('workflow-zoom-badge');

        // Create a placeholder to hold the element's position
        _zoomPlaceholder = document.createElement('div');
        _zoomPlaceholder.id = 'zoom-ph-' + sourceId;
        _zoomPlaceholder.style.display = 'none';
        source.parentNode.insertBefore(_zoomPlaceholder, source);

        // Save references
        _zoomSourceEl = source;
        _zoomOriginalClasses = source.className;

        // Move the actual DOM element into the modal (preserves Alpine.js state)
        container.innerHTML = '';
        container.appendChild(source);

        // Remove nested indentation for full-width display
        source.classList.remove('ml-4', 'pl-4', 'border-l-2', 'border-purple-300', 'border-indigo-300', 'my-1');
        source.classList.add('w-full');

        // Count items for badge
        const cards = source.querySelectorAll('[class*="rounded-lg border"]');
        badge.textContent = cards.length + ' workflow' + (cards.length !== 1 ? 's' : '');

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Expand all nested sub-workflows in the zoomed view
        window.dispatchEvent(new CustomEvent('expand-all-workflows'));

        document.addEventListener('keydown', _workflowZoomEsc);
    }

    function closeWorkflowZoom() {
        const modal = document.getElementById('workflow-zoom-modal');

        if (_zoomSourceEl && _zoomPlaceholder && _zoomPlaceholder.parentNode) {
            // Restore original classes
            _zoomSourceEl.className = _zoomOriginalClasses;

            // Move back to original position
            _zoomPlaceholder.parentNode.insertBefore(_zoomSourceEl, _zoomPlaceholder);
            _zoomPlaceholder.remove();
        }

        modal.classList.add('hidden');
        document.body.style.overflow = '';

        _zoomSourceEl = null;
        _zoomPlaceholder = null;
        _zoomOriginalClasses = '';

        document.removeEventListener('keydown', _workflowZoomEsc);
    }

    function zoomExpandAll() {
        window.dispatchEvent(new CustomEvent('expand-all-workflows'));
    }

    function zoomCollapseAll() {
        window.dispatchEvent(new CustomEvent('collapse-all-workflows'));
    }

    function _workflowZoomEsc(e) {
        if (e.key === 'Escape') closeWorkflowZoom();
    }

    // === Barcode Modal ===
    function openBarcodeModal() {
        const modal = document.getElementById('barcodeModal');
        const img = document.getElementById('barcodeImage');
        const downloadLink = document.getElementById('barcodeDownloadLink');
        const barcodeUrl = "{{ route('documents.barcode', $document->id) }}";

        img.src = barcodeUrl;
        downloadLink.href = barcodeUrl;
        modal.classList.remove('hidden');
        document.addEventListener('keydown', _barcodeModalEsc);
    }

    function closeBarcodeModal() {
        document.getElementById('barcodeModal').classList.add('hidden');
        document.removeEventListener('keydown', _barcodeModalEsc);
    }

    function _barcodeModalEsc(e) {
        if (e.key === 'Escape') closeBarcodeModal();
    }

    // === Print Tracking Modal ===
    function openPrintModal() {
        const modal = document.getElementById('printTrackingModal');
        modal.classList.remove('hidden');
        document.addEventListener('keydown', _printModalEsc);
        loadPrintHistory();
    }

    function closePrintModal() {
        document.getElementById('printTrackingModal').classList.add('hidden');
        document.removeEventListener('keydown', _printModalEsc);
    }

    function _printModalEsc(e) {
        if (e.key === 'Escape') closePrintModal();
    }

    function loadPrintHistory() {
        const historyContainer = document.getElementById('printHistoryList');
        if (!historyContainer) return;
        
        fetch("{{ route('documents.printHistory', $document->id) }}", {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.prints && data.prints.length > 0) {
                historyContainer.innerHTML = data.prints.map(p => `
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800">${p.printer_name || 'Unknown'}</span>
                            <span class="text-sm text-gray-500 ml-2">${p.printed_at || ''}</span>
                            ${p.version_number ? `<span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded ml-1">v${p.version_number}</span>` : ''}
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-gray-700">${p.copies} ${p.copies === 1 ? 'copy' : 'copies'}</span>
                            ${p.print_reason ? `<div class="text-xs text-gray-500">${p.print_reason}</div>` : ''}
                        </div>
                    </div>
                `).join('');
            } else {
                historyContainer.innerHTML = '<p class="text-gray-400 text-sm text-center py-4">No print records yet.</p>';
            }
        })
        .catch(() => {
            historyContainer.innerHTML = '<p class="text-red-400 text-sm text-center py-4">Failed to load print history.</p>';
        });
    }

    function submitPrintRecord(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update the total copies display
                const totalEl = document.getElementById('totalPrintCopies');
                if (totalEl) totalEl.textContent = data.total_copies;
                
                // Reset form
                form.reset();
                form.querySelector('[name="copies"]').value = 1;
                
                // Reload history
                loadPrintHistory();
                
                // Show brief success
                const successMsg = document.createElement('div');
                successMsg.className = 'bg-green-50 text-green-700 text-sm p-2 rounded mt-2';
                successMsg.textContent = 'Print record saved successfully!';
                form.appendChild(successMsg);
                setTimeout(() => successMsg.remove(), 3000);
            }
        })
        .catch(() => {
            alert('Failed to save print record. Please try again.');
        });
    }

    // === Barcode Overlay ===
    function submitBarcodeOverlay(e) {
        e.preventDefault();
        if (!confirm('This will permanently overlay a barcode on the document PDF. Continue?')) return;
        
        const form = e.target;
        const formData = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Applying...';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.textContent = 'Apply Barcode Overlay';
            if (data.success) {
                alert('Barcode overlay applied successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to apply barcode overlay.');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Apply Barcode Overlay';
            alert('Failed to apply barcode overlay. Please try again.');
        });
    }
</script>

{{-- ═══════ Barcode Preview Modal for Version Upload ═══════ --}}
@include('documents.partials.barcode-preview-modal', [
    'modalId'        => 'showBarcodeModal',
    'formSelector'   => '#show-version-upload-form',
    'trackingNumber' => $document->tracking_number ?? null,
])
<script>
document.addEventListener('DOMContentLoaded', function() {
    var versionInput = document.getElementById('show-version-file-input');
    if (versionInput) {
        bindBarcodePreviewToFileInput('#show-version-file-input', 'showBarcodeModal', @json($document->tracking_number ?? null));
    }
});
</script>

{{-- ═══════ Print Prompt Modal ═══════ --}}
@include('documents.partials.print-prompt-modal')

@endsection
