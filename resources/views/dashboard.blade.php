<x-app-layout>
    <!-- Subscription alert banner for company admins -->
    @if(isset($needsSubscription) && $needsSubscription)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
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
                        <a href="{{ route('plans.select') }}" class="font-medium underline text-amber-800 hover:text-amber-900">
                            Click here to select a subscription plan
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="py-8 lg:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
                    <p class="text-sm text-slate-500">Here's what's happening in your organization.</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 shadow-sm">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Last 7 days</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach ([
                            [
                                'title' => 'Total Documents',
                                'value' => $totalDocuments,
                                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'accent' => 'border-indigo-400',
                                'iconBg' => 'bg-indigo-100/50',
                                'iconColor' => 'text-indigo-500'
                            ],
                            [
                                'title' => 'Incoming Documents',
                                'value' => $incomingDocuments,
                                'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                                'accent' => 'border-amber-400',
                                'iconBg' => 'bg-amber-100/50',
                                'iconColor' => 'text-amber-500'
                            ],
                            [
                                'title' => 'Pending Workflows',
                                'value' => $pendingDocuments,
                                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                'accent' => 'border-slate-400',
                                'iconBg' => 'bg-slate-100/50',
                                'iconColor' => 'text-slate-500'
                            ],
                            [
                                'title' => 'Active Users',
                                'value' => $countCompanyUsers,
                                'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                'accent' => 'border-emerald-400',
                                'iconBg' => 'bg-emerald-100/50',
                                'iconColor' => 'text-emerald-500'
                            ],
                        ] as $stat)
                        <div class="bg-white rounded-lg p-4 border border-slate-200 border-l-[3px] {{ $stat['accent'] }} shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 {{ $stat['iconBg'] }} rounded-lg p-3">
                                    <svg class="h-5 w-5 {{ $stat['iconColor'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">{{ $stat['title'] }}</div>
                                    <div class="text-2xl font-bold text-slate-900">{{ $stat['value'] }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Documents Trend</h3>
                                <p class="text-xs text-slate-500">Total created across the company.</p>
                            </div>
                            <div class="text-sm font-semibold text-slate-900">{{ array_sum($documentTrendCounts) }}</div>
                        </div>
                        @php
                            $trendCount = count($documentTrendCounts);
                            $trendMax = max($documentTrendCounts) ?: 1;
                            $trendPoints = collect($documentTrendCounts)->values()->map(function ($value, $index) use ($trendCount, $trendMax) {
                                $x = $trendCount > 1 ? ($index / ($trendCount - 1)) * 100 : 0;
                                $y = 100 - (($value / $trendMax) * 70 + 15);
                                return $x . ',' . $y;
                            })->implode(' ');
                        @endphp
                        <div class="h-40 w-full">
                            <svg viewBox="0 0 100 100" class="h-full w-full">
                                <defs>
                                    <linearGradient id="trendFill" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <polyline fill="url(#trendFill)" stroke="none" points="{{ $trendPoints }} 100,100 0,100" />
                                <polyline fill="none" stroke="#6366f1" stroke-width="2" points="{{ $trendPoints }}" />
                            </svg>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-400 mt-2">
                            @foreach($documentTrendLabels as $label)
                                <span>{{ $label }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <h3 class="text-sm font-semibold text-slate-700 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('documents.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span class="text-xs font-medium text-slate-700">New Document</span>
                            </a>
                            <a href="{{ route('documents.workflows') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span class="text-xs font-medium text-slate-700">My Workflows</span>
                            </a>
                            <a href="{{ route('documents.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span class="text-xs font-medium text-slate-700">Browse Docs</span>
                            </a>
                            <a href="{{ route('documents.workflow-dashboard') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span class="text-xs font-medium text-slate-700">Receive Docs</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-slate-700">Office Activity</h3>
                            <span class="text-xs text-slate-400">Top {{ $officeActivity->count() }}</span>
                        </div>
                        @php $officeMax = $officeActivity->max('total') ?: 1; @endphp
                        <div class="space-y-3">
                            @forelse($officeActivity as $office)
                                <div>
                                    <div class="flex items-center justify-between text-xs text-slate-600">
                                        <span class="font-medium text-slate-700">{{ $office->name }}</span>
                                        <span>{{ $office->total }}</span>
                                    </div>
                                    <div class="h-2 bg-slate-100/50 rounded-full mt-1">
                                        <div class="h-2 bg-indigo-100/500 rounded-full" style="width: {{ ($office->total / $officeMax) * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-slate-500">No office activity yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between p-6 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Recent Documents</h3>
                            <p class="text-xs text-slate-500">Latest activity across the company.</p>
                        </div>
                        <a href="{{ route('documents.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Document</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Owner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Updated</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($dashboardRecentDocuments as $document)
                                    @php
                                        $statusValue = $document->effective_status ?? optional($document->status)->status ?? 'unknown';
                                        $statusClass = match ($statusValue) {
                                            'approved' => 'bg-emerald-100/50 text-emerald-700 border-emerald-200',
                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'pending' => 'bg-amber-100/50 text-amber-700 border-amber-200',
                                            'returned' => 'bg-slate-100/50 text-slate-700 border-slate-200',
                                            default => 'bg-slate-100/50 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('documents.show', $document) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                                {{ Str::limit($document->title, 40) }}
                                            </a>
                                            <div class="text-xs text-slate-400">{{ $document->classification ?? 'Unclassified' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            {{ optional($document->user)->first_name }} {{ optional($document->user)->last_name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                                {{ ucfirst($statusValue) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ $document->updated_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-6 text-center text-sm text-slate-500">No recent documents yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Find Document</h3>
                    <p class="text-xs text-slate-500 mb-4">Search by tracking number or scan a barcode.</p>
                    <form id="quick-action-form" action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" id="qa-action" value="find">

                        <input type="text" name="tracking_number" id="tracking_number"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-slate-900 placeholder:text-slate-400"
                            placeholder="Enter tracking number (e.g. ADM-20250101-000001)">

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label title="Upload barcode image" class="inline-flex items-center justify-center px-3 py-2.5 border border-slate-300 text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Upload
                                <input type="file" id="qr-image-input" accept="image/*" class="hidden" onchange="decodeQrFromImage(this)">
                            </label>
                            <button type="button" onclick="toggleScanner()" class="inline-flex items-center justify-center px-3 py-2.5 border border-slate-300 text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span id="scanner-btn-text">Scan</span>
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-2.5 border border-indigo-600 text-xs font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Find
                            </button>
                        </div>

                        <div id="qr-decode-status" class="hidden"></div>

                        <div id="reader-wrapper" class="hidden">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs text-slate-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                    Camera active — point at a barcode
                                </p>
                                <button type="button" onclick="stopScanner()" class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Close
                                </button>
                            </div>
                            <div id="reader" class="rounded-lg overflow-hidden shadow-lg border border-indigo-100"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrCode = null;
        let scannerActive = false;

        function toggleScanner() {
            if (scannerActive) {
                stopScanner();
            } else {
                startScanner();
            }
        }

        function startScanner() {
            const wrapper = document.getElementById('reader-wrapper');
            wrapper.classList.remove('hidden');
            scannerActive = true;

            const btnText = document.getElementById('scanner-btn-text');
            if (btnText) btnText.textContent = 'Stop';

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 350, height: 150 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                document.getElementById('tracking_number').value = decodedText;
                stopScanner();
                showQrStatus('success', 'Barcode scanned: ' + decodedText);
            }).catch(err => {
                showQrStatus('error', 'Could not start camera: ' + err);
                stopScanner();
            });
        }

        function stopScanner() {
            const wrapper = document.getElementById('reader-wrapper');
            if (html5QrCode && scannerActive) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    wrapper.classList.add('hidden');
                }).catch(() => {
                    wrapper.classList.add('hidden');
                });
            } else {
                wrapper.classList.add('hidden');
            }
            scannerActive = false;
            const btnText = document.getElementById('scanner-btn-text');
            if (btnText) btnText.textContent = 'Scan';
        }

        function decodeQrFromImage(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            showQrStatus('info', 'Decoding barcode from image...');

            const tempScanner = new Html5Qrcode("qr-temp-canvas");
            tempScanner.scanFileV2(file, false)
                .then(result => {
                    const text = result.decodedText;
                    document.getElementById('tracking_number').value = text;
                    showQrStatus('success', 'Barcode decoded: ' + text);
                    tempScanner.clear();
                })
                .catch(() => {
                    showQrStatus('error', 'Could not decode barcode from image. Make sure the barcode is clearly visible.');
                    tempScanner.clear();
                });

            input.value = '';
        }

        function showQrStatus(type, message) {
            const el = document.getElementById('qr-decode-status');
            el.classList.remove('hidden');
            const colors = {
                success: 'bg-green-50 border-green-200 text-green-800',
                error: 'bg-red-50 border-red-200 text-red-800',
                info: 'bg-indigo-100/50 border-indigo-200 text-indigo-800'
            };
            const icons = {
                success: '<svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                error: '<svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                info: '<svg class="w-4 h-4 mr-2 flex-shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>'
            };
            el.className = 'flex items-center p-3 rounded-lg border text-sm ' + colors[type];
            el.innerHTML = icons[type] + '<span>' + message + '</span>';

            if (type !== 'info') {
                setTimeout(() => { el.classList.add('hidden'); }, 5000);
            }
        }
    </script>
    <div id="qr-temp-canvas" style="display:none;"></div>
    @endpush
</x-app-layout>
