@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Header Box -->
            <div class="bg-white rounded-xl mb-6 border border-slate-200 overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">{{ __('Workflow Management') }}</h2>
                            <p class="text-sm text-slate-600">Track and manage document workflows</p>
                        </div>
                    </div>
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Back to Documents
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-white border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-md"
                    role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Message for Workflow Access -->
            @if (session('info'))
                <div class="bg-white border-l-4 border-indigo-500 text-indigo-700 p-4 mb-6 rounded-r-lg shadow-md"
                    role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-indigo-800">{{ session('info') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="bg-white border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-md"
                    role="alert">
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
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pending Documents Notice -->
            @if(isset($pendingReceive) && $pendingReceive->count() > 0)
                <div class="bg-white rounded-xl mb-6 border-l-4 border-orange-500 border-t border-b border-r border-orange-200/80">
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <p class="text-sm text-slate-600">
                                <span class="font-medium text-orange-600">{{ $pendingReceive->count() }}</span> document(s) awaiting receipt
                            </p>
                        </div>
                        <a href="{{ route('documents.workflow-dashboard') }}" class="inline-flex items-center px-3 py-1.5 border border-orange-600 text-sm font-medium rounded-md text-orange-600 hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            Go to Receive
                        </a>
                    </div>
                </div>
            @endif

            <!-- Workflow Instructions -->
            <div class="bg-white rounded-xl mb-6 border-l-4 border-indigo-500 border-t border-b border-r border-slate-200">
                <div class="p-4 flex items-start space-x-3">
                    <p class="text-sm text-slate-600">
                        <span class="font-medium text-indigo-600">Document Tracking:</span>
                        This page shows the tracking status of all documents in the workflow. Use the "View Details" button to see complete information about any document. For actions like receiving or reviewing documents, please visit the respective pages from the navigation menu.
                    </p>
                </div>
            </div>

            <!-- Search Section -->
            <div class="max-w-7xl mx-auto mb-6">
                <div class="flex items-center bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="tracking_number"
                            class="block w-full pl-10 pr-3 py-3 border-0 rounded-l-xl focus:ring-2 focus:ring-indigo-500 text-sm"
                            placeholder="Search workflows by keywords..."
                            oninput="filterWorkflows(this.value)">
                    </div>
                    <div class="flex items-center pr-2">
                        {{-- <button type="button"
                            onclick="startScanner()"
                            class="p-2 hover:bg-slate-100 rounded-lg transition-colors"
                            title="Scan QR Code">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 6v4m2-4h-2m-4 0H4m12 6h-2m2-4H4m6-6h2M4 12h2m10-6h2m-6 0h-2" />
                            </svg>
                        </button> --}}
                        <button type="button"
                            onclick="clearFilter()"
                            class="p-2 hover:bg-slate-100 rounded-lg transition-colors ml-1"
                            title="Clear Search">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- QR Scanner Modal -->
                {{-- <div id="qr-modal" class="hidden fixed inset-0 bg-slate-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900">Scan QR Code</h3>
                            <button type="button" onclick="stopScanner()" class="text-slate-400 hover:text-slate-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div id="reader" class="border rounded-lg overflow-hidden"></div>
                        <div class="mt-4 text-center">
                            <button type="button" onclick="stopScanner()"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-md hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div> --}}
            </div>            <!-- Main Content -->
            <div class="bg-white rounded-xl overflow-hidden border border-slate-200 transition-all duration-300 hover:border-indigo-300/80">
                <div class="bg-gradient-to-r from-indigo-50 to-white p-6 border-b border-indigo-200/60">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="text-lg font-semibold text-slate-800">{{ __('Active Workflows') }}</h3>
                        </div>
                        <span class="text-sm text-indigo-600 bg-indigo-50 py-1 px-3 rounded-full border border-indigo-200/60">{{ $workflows->count() ?? 0 }} workflows</span>
                    </div>
                </div>

                    @if(isset($workflows) && $workflows->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-gradient-to-r from-slate-50 to-white">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">
                                            {{ __('ID') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">
                                            {{ __('Document') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">
                                            {{ __('Current Step') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">
                                            {{ __('Recipient') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($workflows as $workflow)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                                {{ $workflow->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                                <a href="{{ route('documents.show', $workflow->document_id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                                    {{ $workflow->document->title ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                                {{ $workflow->step_order }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @php
                                                    $recipientInfo = '';
                                                    if ($workflow->recipients && $workflow->recipients->count() > 0) {
                                                        // Multiple individual recipients
                                                        foreach ($workflow->recipients as $recipient) {
                                                            $recipientInfo .= "<div class='mb-2 last:mb-0'>";
                                                            $recipientInfo .= "<div class='font-medium text-slate-900'>" .
                                                                e($recipient->first_name . ' ' . $recipient->last_name) .
                                                                "</div>";

                                                            // Add their office if available
                                                            if ($recipient->office) {
                                                                $recipientInfo .= "<div class='text-xs text-slate-500'>" . e($recipient->office->name) . "</div>";
                                                            }
                                                            $recipientInfo .= "</div>";
                                                        }
                                                    } elseif ($workflow->recipientOffices && $workflow->recipientOffices->count() > 0) {
                                                        // Multiple office recipients
                                                        foreach ($workflow->recipientOffices as $office) {
                                                            $recipientInfo .= "<div class='mb-2 last:mb-0'>";
                                                            $recipientInfo .= "<div class='font-medium text-slate-900'>" .
                                                                e($office->name) .
                                                                "</div>";
                                                            $recipientInfo .= "<div class='text-xs text-slate-500'>Entire Team</div>";
                                                            $recipientInfo .= "</div>";
                                                        }
                                                    } elseif ($workflow->recipient) {
                                                        // Single individual recipient (legacy support)
                                                        $recipientInfo .= "<div class='font-medium text-slate-900'>" .
                                                            e($workflow->recipient->first_name . ' ' . $workflow->recipient->last_name) .
                                                            "</div>";

                                                        // Add their office if available
                                                        $recipientOffice = $workflow->recipientOffice ? $workflow->recipientOffice->name : null;
                                                        if ($recipientOffice) {
                                                            $recipientInfo .= "<div class='text-xs text-slate-500'>" . e($recipientOffice) . "</div>";
                                                        }
                                                    } elseif ($workflow->recipientOffice) {
                                                        // Single office recipient (legacy support)
                                                        $recipientInfo .= "<div class='font-medium text-slate-900'>" .
                                                            e($workflow->recipientOffice->name) .
                                                            "</div>" .
                                                            "<div class='text-xs text-slate-500'>Entire Team</div>";
                                                    } else {
                                                        $recipientInfo = "<div class='text-slate-500'>No recipient assigned</div>";
                                                    }
                                                @endphp
                                                {!! $recipientInfo !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                                @if($workflow->status === 'pending')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($workflow->status === 'received')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Received
                                                    </span>
                                                @elseif($workflow->status === 'approved')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($workflow->status === 'rejected')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @elseif($workflow->status === 'returned')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                                        Returned
                                                    </span>
                                                @elseif($workflow->status === 'referred')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                        Referred
                                                    </span>
                                                @elseif($workflow->status === 'forwarded')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Forwarded
                                                    </span>
                                                @elseif($workflow->status === 'completed')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                        Completed
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">
                                                        {{ ucfirst($workflow->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex flex-wrap space-x-2">
                                                    <a href="{{ route('documents.show', $workflow->document_id) }}"
                                                        class="text-indigo-500 hover:underline">View Details</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $workflows->links() }}
                        </div>
                    @else
                        <div class="p-6">
                            <div class="flex flex-col items-center justify-center p-6 bg-indigo-50/50 rounded-lg border border-indigo-100">
                                <svg class="w-16 h-16 text-indigo-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p class="text-indigo-900 text-lg font-medium mb-1">No Active Workflows</p>
                                <p class="text-indigo-600 text-sm">No workflows have been created yet</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let qrScanner = null;

        function startScanner() {
            const modal = document.getElementById('qr-modal');
            const reader = document.getElementById('reader');

            // Show the modal
            modal.classList.remove('hidden');

            // Initialize QR Scanner
            qrScanner = new Html5QrcodeScanner(
                "reader",
                {
                    fps: 10,
                    qrbox: 250,
                    rememberLastUsedCamera: true,
                    showTorchButtonIfSupported: true
                }
            );

            // Render the scanner with success callback
            qrScanner.render((decodedText) => {
                document.getElementById('tracking_number').value = decodedText;
                filterWorkflows(decodedText);
                stopScanner();
            });

            // Add keyboard listener for Escape key
            document.addEventListener('keydown', handleEscapeKey);
        }

        function stopScanner() {
            if (qrScanner) {
                qrScanner.clear();
                qrScanner = null;
            }

            const modal = document.getElementById('qr-modal');
            modal.classList.add('hidden');

            // Remove keyboard listener
            document.removeEventListener('keydown', handleEscapeKey);
        }

        function handleEscapeKey(event) {
            if (event.key === 'Escape') {
                stopScanner();
            }
        }

        function filterWorkflows(trackingNumber) {
            if (!trackingNumber) {
                showAllRows();
                return;
            }

            const searchTerm = trackingNumber.trim().toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            const noResultsRow = document.getElementById('no-results-row');
            let hasVisibleRows = false;

            rows.forEach(row => {
                if (row.id === 'no-results-row') return;

                // Get all text content from cells except the actions column
                const cells = row.querySelectorAll('td:not(:last-child)');
                let rowText = '';
                cells.forEach(cell => {
                    rowText += ' ' + cell.textContent.trim().toLowerCase();
                });

                // Check for exact match of tracking number
                const hasExactMatch = rowText.includes(searchTerm);

                // Also check for partial matches if no exact match is found
                const hasPartialMatch = !hasExactMatch && searchTerm.length >= 3 &&
                    rowText.split(' ').some(word => word.includes(searchTerm));

                const shouldShow = hasExactMatch || hasPartialMatch;
                row.classList.toggle('hidden', !shouldShow);
                if (shouldShow) hasVisibleRows = true;
            });

            // Show/hide the no results message
            if (trackingNumber && !hasVisibleRows) {
                if (!noResultsRow) {
                    const tbody = document.querySelector('table tbody');
                    const newRow = document.createElement('tr');
                    newRow.id = 'no-results-row';
                    newRow.innerHTML = `
                        <td colspan="6" class="px-6 py-4">
                            <div class="flex flex-col items-center justify-center py-6 text-center">
                                <svg class="h-12 w-12 text-slate-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <p class="text-slate-900 font-medium text-lg mb-2">No matching workflows found</p>
                                <p class="text-slate-500 text-base">Try adjusting your search term</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                } else {
                    noResultsRow.classList.remove('hidden');
                }
            } else if (noResultsRow) {
                noResultsRow.classList.add('hidden');
            }

            // Update the workflow count
            const countSpan = document.querySelector('.text-indigo-600.bg-indigo-50');
            if (countSpan) {
                const visibleCount = [...rows].filter(row => !row.classList.contains('hidden') && row.id !== 'no-results-row').length;
                countSpan.textContent = `${visibleCount} workflows`;
            }
        }

        function showAllRows() {
            const rows = document.querySelectorAll('table tbody tr');
            const noResultsRow = document.getElementById('no-results-row');

            rows.forEach(row => {
                if (row.id === 'no-results-row') {
                    row.classList.add('hidden');
                } else {
                    row.classList.remove('hidden');
                }
            });

            // Update the workflow count
            const countSpan = document.querySelector('.text-indigo-600.bg-indigo-50');
            if (countSpan) {
                const visibleCount = [...rows].filter(row => !row.classList.contains('hidden') && row.id !== 'no-results-row').length;
                countSpan.textContent = `${visibleCount} workflows`;
            }
        }

        function clearFilter() {
            document.getElementById('tracking_number').value = '';
            showAllRows();
        }
    </script>
    @endpush
@endsection
