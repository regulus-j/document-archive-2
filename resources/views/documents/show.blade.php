@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
                <div x-data="{ isOpen: false }" class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Document Progress
                        </h3>
                        <button @click="isOpen = !isOpen" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800 focus:outline-none transition-colors">
                            <span x-text="isOpen ? 'Hide Details' : 'Show Details'" class="mr-1"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
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
                         class="max-h-[500px] overflow-y-auto pr-2">
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
                        <p class="text-base font-medium text-indigo-700">
                            {{ $document->trackingNumber->tracking_number ?? 'N/A' }}
                        </p>
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
                        <p class="text-sm font-medium text-slate-500 mb-2">Attachments</p>
                        @if($document->attachments->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($document->attachments as $attachment)
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('documents.download', $attachment->id) }}"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors font-medium truncate block">
                                        {{ $attachment->filename }}
                                    </a>
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
                            {{-- Step header for sequential workflows --}}
                            @if($isSequentialWorkflow)
                            <div class="flex items-center gap-2 -ml-[31px] mb-2 mt-3 first:mt-0">
                                @php
                                    $stepDone = $stepWorkflows->every(fn($w) => in_array($w->status, ['approved','rejected','acknowledged','commented','returned','forwarded']));
                                    $stepActive = $stepWorkflows->contains(fn($w) => in_array($w->status, ['received','pending']));
                                @endphp
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
                            </div>
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
                                        </div>
                                    </div>
                                </div>

                                {{-- Sub-workflows (forwarded-from-review) --}}
                                @if($wf->childWorkflows && $wf->childWorkflows->count())
                                <div class="ml-4 pl-4 border-l-2 border-purple-300 space-y-2 my-1">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Forwarded for Review</span>
                                        <span class="text-xs text-purple-400">({{ $wf->childWorkflows->count() }})</span>
                                    </div>
                                    @foreach($wf->childWorkflows as $subWf)
                                        @php
                                            $subCfg = $wfStatusConfig[$subWf->status] ?? ['icon' => 'minus', 'color' => 'slate', 'label' => ucfirst($subWf->status), 'ring' => 'ring-slate-200'];
                                        @endphp
                                        <div class="bg-purple-50/60 rounded-lg border {{ 'border-'.$subCfg['color'].'-200' }} border-dashed p-2.5">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
                                                        {{ $subWf->recipient ? strtoupper(substr($subWf->recipient->first_name ?? '?', 0, 1)) : '?' }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-medium text-slate-700">
                                                            {{ $subWf->recipient ? ($subWf->recipient->first_name . ' ' . $subWf->recipient->last_name) : 'Unknown' }}
                                                        </p>
                                                        @if($subWf->recipientOffice)
                                                            <span class="text-[10px] text-slate-400">{{ $subWf->recipientOffice->name }}</span>
                                                        @endif
                                                        @if($subWf->remarks)
                                                            <p class="text-[10px] text-slate-400 mt-1 italic">&ldquo;{{ $subWf->remarks }}&rdquo;</p>
                                                        @endif
                                                        @if($subWf->received_at)
                                                            <p class="text-[10px] text-slate-400 mt-0.5">Responded: {{ \Carbon\Carbon::parse($subWf->received_at)->format('M d, Y g:ia') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-{{ $subCfg['color'] }}-100 text-{{ $subCfg['color'] }}-700">
                                                    {{ $subCfg['label'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            @endforeach

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
@endsection
