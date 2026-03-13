<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="text-3xl font-bold text-slate-900 leading-tight">
            {{ __('Team Dashboard') }}
        </h2>
    </x-slot> --}}

    <!-- Subscription alert banner for company users -->
    @if(isset($needsSubscription) && $needsSubscription)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 mb-2">
        <div class="bg-gradient-to-r from-amber-100 to-amber-50 border-l-4 border-amber-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-amber-800">
                        Your company doesn't have an active subscription. Some features may be limited.
                        @if(auth()->user()->hasRole('company-admin'))
                        <a href="{{ route('plans.select') }}" class="font-medium underline text-amber-800 hover:text-amber-900">
                            Click here to select a subscription plan
                        </a>
                        @else
                        Please contact your company administrator to activate a subscription.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="py-8 lg:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Welcome Message -->
            <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900">
                            {{ __("Welcome back, " . auth()->user()->first_name . "!") }}
                        </h1>
                        <p class="text-slate-500 text-sm mt-0.5">{{ __("From " . optional(auth()->user()->companies()->first())->company_name . " company!") }}</p>
                    </div>
                </div>
            </div>

            <!-- Office Lead Statistics (Only shown for office leads) -->
            @if($isOfficeLead && $ledOffice)
            <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ $ledOffice->name }} Team Statistics
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Office Document Count -->
                    <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-indigo-400 shadow-card hover:shadow-card-hover transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-50 rounded-lg p-3">
                                <svg class="h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Documents</div>
                                <div class="text-2xl font-bold text-slate-900">{{ $officeDocumentCount }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Documents -->
                    <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-emerald-400 shadow-card hover:shadow-card-hover transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Documents Today</div>
                                <div class="text-2xl font-bold text-slate-900">{{ $officeDocumentsTodayCount }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Workflows -->
                    <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-amber-400 shadow-card hover:shadow-card-hover transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-amber-50 rounded-lg p-3">
                                <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Pending Workflows</div>
                                <div class="text-2xl font-bold text-slate-900">{{ $officePendingWorkflowsCount }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Members -->
                    <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-slate-400 shadow-card hover:shadow-card-hover transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-slate-100 rounded-lg p-3">
                                <svg class="h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Team Members</div>
                                <div class="text-2xl font-bold text-slate-900">{{ $officeMembers->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Office Documents -->
                @if($officeDocuments->count() > 0)
                <div class="mt-6">
                    <h4 class="text-base font-medium text-slate-700 mb-3">Recent Team Documents</h4>
                    <div class="overflow-x-auto rounded-lg border border-slate-200/80">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Uploader</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Categories</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach($officeDocuments as $document)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                            {{ Str::limit($document->title, 30) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $document->user ? $document->user->first_name . ' ' . $document->user->last_name : 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $document->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($document->categories as $category)
                                                <span class="px-2 inline-flex text-xs leading-5 font-medium rounded-full bg-slate-100 text-slate-700">
                                                    {{ $category->name ?? $category->category ?? 'Unnamed Category' }}
                                                </span>
                                            @empty
                                                <span class="text-slate-400 text-xs">No categories</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.office-user-dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                            </svg>
                            View Full Team Dashboard
                        </a>
                    </div>
                </div>
                @else
                <div class="text-center text-slate-500 py-4">No recent team documents found</div>
                @endif
            </div>
            @endif

            <!-- Office User Statistics -->
            <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Your Activity Overview
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ([
                        [
                            'title' => 'Documents Received',
                            'value' => $totalDocuments,
                            'icon' => 'M3 10h11M9 21V3m0 18v-8m-6 8h6m6-18h6m-6 0v18m0-18v8m6-8v8',
                            'accent' => 'border-l-indigo-400',
                            'iconBg' => 'bg-indigo-50',
                            'iconColor' => 'text-indigo-500'
                        ],
                        [
                            'title' => 'Pending Documents',
                            'value' => $pendingDocuments,
                            'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                            'accent' => 'border-l-amber-400',
                            'iconBg' => 'bg-amber-50',
                            'iconColor' => 'text-amber-500'
                        ],
                        [
                            'title' => 'Processed Documents',
                            'value' => $countRecentDocs,
                            'icon' => 'M5 13l4 4L19 7',
                            'accent' => 'border-l-emerald-400',
                            'iconBg' => 'bg-emerald-50',
                            'iconColor' => 'text-emerald-500'
                        ]
                    ] as $stat)
                        <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] {{ $stat['accent'] }} shadow-card hover:shadow-card-hover transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 {{ $stat['iconBg'] }} rounded-lg p-3">
                                    <svg class="h-5 w-5 {{ $stat['iconColor'] }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $stat['icon'] }}" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">
                                        {{ $stat['title'] }}
                                    </div>
                                    <div class="text-2xl font-bold text-slate-900">
                                        {{ $stat['value'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions for Office Users -->
            <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick Actions
                </h3>

                {{-- Quick Shortcut Buttons --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
                    <a href="{{ route('documents.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-slate-300 transition group shadow-card">
                        <svg class="w-5 h-5 text-slate-500 mb-2 group-hover:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Browse Documents</span>
                    </a>
                    <a href="{{ route('documents.workflow-dashboard') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-slate-300 transition group shadow-card">
                        <svg class="w-5 h-5 text-slate-500 mb-2 group-hover:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Receive Documents</span>
                    </a>
                    <a href="{{ route('documents.workflows') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-slate-300 transition group shadow-card">
                        <svg class="w-5 h-5 text-slate-500 mb-2 group-hover:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">My Workflows</span>
                    </a>
                </div>

                {{-- Tracking Number Lookup --}}
                <div class="border-t border-slate-200/80 pt-6">
                    <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Find Document by Tracking Number or Barcode
                    </h4>

                    <form id="ou-quick-action-form" action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="find">

                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" name="tracking_number" id="ou-tracking-number"
                                class="flex-1 min-w-0 block w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm text-slate-900 placeholder:text-slate-400"
                                placeholder="Enter tracking number (e.g. ADM-20250101-000001)">

                            <div class="flex gap-2">
                                {{-- Upload Barcode Image --}}
                                <label title="Upload barcode image" class="inline-flex items-center px-3 py-2.5 border border-slate-300 text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Upload Barcode</span>
                                    <input type="file" id="ou-qr-image-input" accept="image/*" class="hidden" onchange="ouDecodeQrFromImage(this)">
                                </label>

                                {{-- Camera Scan --}}
                                <button type="button" onclick="ouToggleScanner()"
                                    class="inline-flex items-center px-3 py-2.5 border border-slate-300 text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span class="hidden sm:inline" id="ou-scanner-btn-text">Scan Barcode</span>
                                </button>

                                {{-- Submit --}}
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 border border-indigo-600 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Find
                                </button>
                            </div>
                        </div>

                        {{-- QR decode status message --}}
                        <div id="ou-qr-decode-status" class="hidden"></div>

                        {{-- Camera QR Scanner --}}
                        <div id="ou-reader-wrapper" class="hidden">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-slate-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                    Camera active — point at a barcode
                                </p>
                                <button type="button" onclick="ouStopScanner()" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Close
                                </button>
                            </div>
                            <div id="ou-reader" class="rounded-lg overflow-hidden shadow-lg border border-indigo-100"></div>
                        </div>
                    </form>
                </div>
            </div>

            @push('scripts')
            <script src="https://unpkg.com/html5-qrcode"></script>
            <script>
                let ouHtml5QrCode = null;
                let ouScannerActive = false;

                function ouToggleScanner() {
                    if (ouScannerActive) { ouStopScanner(); } else { ouStartScanner(); }
                }

                function ouStartScanner() {
                    const wrapper = document.getElementById('ou-reader-wrapper');
                    wrapper.classList.remove('hidden');
                    ouScannerActive = true;
                    const btnText = document.getElementById('ou-scanner-btn-text');
                    if (btnText) btnText.textContent = 'Stop';

                    ouHtml5QrCode = new Html5Qrcode("ou-reader");
                    ouHtml5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 350, height: 150 } }, function(decodedText) {
                        document.getElementById('ou-tracking-number').value = decodedText;
                        ouStopScanner();
                        ouShowQrStatus('success', 'Barcode scanned: ' + decodedText);
                    }).catch(function(err) {
                        ouShowQrStatus('error', 'Could not start camera: ' + err);
                        ouStopScanner();
                    });
                }

                function ouStopScanner() {
                    const wrapper = document.getElementById('ou-reader-wrapper');
                    if (ouHtml5QrCode && ouScannerActive) {
                        ouHtml5QrCode.stop().then(function() { ouHtml5QrCode.clear(); wrapper.classList.add('hidden'); }).catch(function() { wrapper.classList.add('hidden'); });
                    } else { wrapper.classList.add('hidden'); }
                    ouScannerActive = false;
                    const btnText = document.getElementById('ou-scanner-btn-text');
                    if (btnText) btnText.textContent = 'Scan Barcode';
                }

                function ouDecodeQrFromImage(input) {
                    if (!input.files || !input.files[0]) return;
                    const file = input.files[0];
                    ouShowQrStatus('info', 'Decoding barcode from image...');
                    const tempScanner = new Html5Qrcode("ou-qr-temp-canvas");
                    tempScanner.scanFileV2(file, false)
                        .then(function(result) {
                            document.getElementById('ou-tracking-number').value = result.decodedText;
                            ouShowQrStatus('success', 'Barcode decoded: ' + result.decodedText);
                            tempScanner.clear();
                        }).catch(function() {
                            ouShowQrStatus('error', 'Could not decode barcode. Make sure the barcode is clearly visible.');
                            tempScanner.clear();
                        });
                    input.value = '';
                }

                function ouShowQrStatus(type, message) {
                    const el = document.getElementById('ou-qr-decode-status');
                    el.classList.remove('hidden');
                    const colors = { success: 'bg-green-50 border-green-200 text-green-800', error: 'bg-red-50 border-red-200 text-red-800', info: 'bg-indigo-50 border-indigo-200 text-indigo-800' };
                    el.className = 'flex items-center p-3 rounded-lg border text-sm ' + colors[type];
                    el.innerHTML = '<span>' + message + '</span>';
                    if (type !== 'info') { setTimeout(function() { el.classList.add('hidden'); }, 5000); }
                }
            </script>
            {{-- Hidden temp canvas for QR image decoding --}}
            <div id="ou-qr-temp-canvas" style="display:none;"></div>
            @endpush

        </div>
    </div>
</x-app-layout>
