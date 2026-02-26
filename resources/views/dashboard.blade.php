<x-app-layout>


    <!-- Welcome Message -->
    <div class="bg-gradient-to-b from-blue-50 to-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg p-6 border border-gray-200 mb-8">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500 mr-3 mr-5 " fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            {{ __("Welcome back, " . auth()->user()->first_name . "!") }}
                        </h1>
                        <p class="text-gray-500 mt-1">Here's what's happening in your organization.</p>
                    </div>
                </div>
            </div>

            <!-- Subscription alert banner for company admins -->
            @if(isset($needsSubscription) && $needsSubscription)
            <div class="mb-8">
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

        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Stats Overview -->
            <div class="bg-white rounded-lg p-6 border border-gray-200 mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Organization Overview
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ([
                        [
                            'title' => 'Total Documents',
                            'value' => $totalDocuments,
                            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'color' => 'blue'
                        ],
                        [
                            'title' => 'Total Users',
                            'value' => $countCompanyUsers,
                            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                            'color' => 'indigo'
                        ],
                        [
                            'title' => 'Total Teams',
                            'value' => $countOffices,
                            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            'color' => 'purple'
                        ],
                        [
                            'title' => 'Incoming Documents',
                            'value' => $incomingDocuments + $pendingDocuments,
                            'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                            'color' => 'emerald'
                        ],
                    ] as $stat)
                    <div class="bg-{{ $stat['color'] }}-50 rounded-lg p-4 border border-{{ $stat['color'] }}-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-{{ $stat['color'] }}-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-{{ $stat['color'] }}-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $stat['icon'] }}" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-sm font-medium text-{{ $stat['color'] }}-800 mb-1">
                                    {{ $stat['title'] }}
                                </div>
                                <div class="text-2xl font-bold text-{{ $stat['color'] }}-900">
                                    {{ $stat['value'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg p-6 border border-gray-200 mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick Actions
                </h3>

                {{-- Quick Shortcut Buttons --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                    <a href="{{ route('documents.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 transition group">
                        <svg class="w-6 h-6 text-blue-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-sm font-medium text-blue-700">New Document</span>
                    </a>
                    <a href="{{ route('documents.workflows') }}" class="flex flex-col items-center p-4 rounded-lg border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 transition group">
                        <svg class="w-6 h-6 text-indigo-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="text-sm font-medium text-indigo-700">My Workflows</span>
                    </a>
                    <a href="{{ route('documents.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition group">
                        <svg class="w-6 h-6 text-emerald-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="text-sm font-medium text-emerald-700">Browse Documents</span>
                    </a>
                    <a href="{{ route('documents.receive.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 transition group">
                        <svg class="w-6 h-6 text-amber-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="text-sm font-medium text-amber-700">Receive Documents</span>
                    </a>
                </div>

                {{-- Tracking Number Lookup --}}
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Find Document by Tracking Number or QR Code
                    </h4>

                    <form id="quick-action-form" action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" id="qa-action" value="find">

                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" name="tracking_number" id="tracking_number"
                                class="flex-1 min-w-0 block w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter tracking number (e.g. ADM-20250101-000001)">

                            <div class="flex gap-2">
                                {{-- Upload QR Image --}}
                                <label title="Upload QR code image" class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Upload QR</span>
                                    <input type="file" id="qr-image-input" accept="image/*" class="hidden" onchange="decodeQrFromImage(this)">
                                </label>

                                {{-- Camera Scan --}}
                                <button type="button" onclick="toggleScanner()"
                                    class="inline-flex items-center px-3 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span class="hidden sm:inline" id="scanner-btn-text">Scan QR</span>
                                </button>

                                {{-- Submit --}}
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 border border-blue-600 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Find
                                </button>
                            </div>
                        </div>

                        {{-- QR decode status message --}}
                        <div id="qr-decode-status" class="hidden"></div>

                        {{-- Camera QR Scanner --}}
                        <div id="reader-wrapper" class="hidden">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                    Camera active — point at a QR code
                                </p>
                                <button type="button" onclick="stopScanner()" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Close
                                </button>
                            </div>
                            <div id="reader" class="rounded-lg overflow-hidden shadow-lg border border-blue-100"></div>
                        </div>
                    </form>
                </div>
            </div>

            @push('scripts')
            <script src="https://unpkg.com/html5-qrcode"></script>
            <script>
                let html5QrCode = null;
                let scannerActive = false;

                // Toggle camera scanner on/off
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
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                    html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                        document.getElementById('tracking_number').value = decodedText;
                        stopScanner();
                        showQrStatus('success', 'QR code scanned: ' + decodedText);
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
                    if (btnText) btnText.textContent = 'Scan QR';
                }

                // Decode QR from uploaded image file (client-side using html5-qrcode)
                function decodeQrFromImage(input) {
                    if (!input.files || !input.files[0]) return;

                    const file = input.files[0];
                    showQrStatus('info', 'Decoding QR code from image...');

                    const tempScanner = new Html5Qrcode("qr-temp-canvas");
                    tempScanner.scanFileV2(file, /* showImage= */ false)
                        .then(result => {
                            const text = result.decodedText;
                            document.getElementById('tracking_number').value = text;
                            showQrStatus('success', 'QR code decoded: ' + text);
                            tempScanner.clear();
                        })
                        .catch(err => {
                            showQrStatus('error', 'Could not decode QR code from image. Make sure the image contains a clear QR code.');
                            tempScanner.clear();
                        });

                    // Reset file input so the same file can be selected again
                    input.value = '';
                }

                function showQrStatus(type, message) {
                    const el = document.getElementById('qr-decode-status');
                    el.classList.remove('hidden');
                    const colors = {
                        success: 'bg-green-50 border-green-200 text-green-800',
                        error: 'bg-red-50 border-red-200 text-red-800',
                        info: 'bg-blue-50 border-blue-200 text-blue-800'
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
            {{-- Hidden canvas element for QR image decoding --}}
            <div id="qr-temp-canvas" style="display:none;"></div>
            @endpush

            <!-- Recent Activity
            <div class="bg-white shadow-xl rounded-xl p-8 border border-blue-100">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 shadow-lg mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Recent Activity</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                        <div class="flex-shrink-0 mr-4">
                            <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                D
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Document #12345 was received</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                        <div class="flex-shrink-0 mr-4">
                            <div class="h-10 w-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                U
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">User John Doe updated their profile</p>
                            <p class="text-xs text-gray-500">Yesterday at 3:45 PM</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                        <div class="flex-shrink-0 mr-4">
                            <div class="h-10 w-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                O
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">New office added: Downtown Branch</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                        View all activity
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div> -->
        </div>
    </div>
</x-app-layout>
