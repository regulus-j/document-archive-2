<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-900 leading-tight">
            {{ __('Office Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Welcome Message -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500 mr-3" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ __("Welcome back, " . auth()->user()->first_name . "!") }}
                        <br>
                        {{ __("From " . optional(auth()->user()->companies()->first())->company_name . " company!") }}
                    </div>
                </div>
            </div>

            <!-- Office User Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([ 
                    ['title' => 'Documents Received', 'value' => $totalDocuments, 'icon' => 'M3 10h11M9 21V3m0 18v-8m-6 8h6m6-18h6m-6 0v18m0-18v8m6-8v8'], 
                    ['title' => 'Pending Documents', 'value' => $pendingDocuments, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'], 
                    ['title' => 'Processed Documents', 'value' => $countRecentDocs, 'icon' => 'M5 13l4 4L19 7'], 
                ] as $stat)
                    <div class="bg-white shadow-sm rounded-lg">
                        <div class="p-6 border-b border-gray-200 flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $stat['icon'] }}" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        {{ $stat['title'] }}
                                    </dt>
                                    <dd class="text-3xl font-semibold text-gray-900">
                                        {{ $stat['value'] }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Quick Actions for Office Users -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h3>
                <form action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Select Action</label>
                        <select id="action" name="action"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Select an action</option>
                            @foreach ([ 
                                ['value' => 'find', 'label' => 'Find', 'icon' => 'M10.5 3a7.5 7.5 0 015.916 12.5l4.243 4.242-1.414 1.414-4.242-4.243A7.5 7.5 0 1110.5 3z'], 
                                ['value' => 'receive', 'label' => 'Receive', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'], 
                                ['value' => 'acccept', 'label' => 'Acccept', 'icon' => 'M5 13l4 4L19 7'], 
                                ['value' => 'reject', 'label' => 'Reject', 'icon' => 'M6 18L18 6M6 6l12 12'], 
                            ] as $action)
                                <option value="{{ $action['value'] }}">
                                    {{ $action['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Tracking
                            Number</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="tracking_number" id="tracking_number"
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300"
                                placeholder="Enter tracking number">
                            <button type="button" onclick="startScanner()"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                Scan QR
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-r-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit
                            </button>
                        </div>
                        <div id="reader" class="mt-4 hidden"></div>
                    </div>

                    @push('scripts')
                    <script src="https://unpkg.com/html5-qrcode"></script>
                    <script>
                        function startScanner() {
                            const reader = document.getElementById('reader');
                            reader.classList.remove('hidden');
                            
                            const html5QrCode = new Html5Qrcode("reader");
                            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                            
                            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                                document.getElementById('tracking_number').value = decodedText;
                                html5QrCode.stop();
                                reader.classList.add('hidden');
                            });
                        }
                    </script>
                    @endpush
                </form>
            </div>

            <!-- User/Office Performance -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900">User/Office Performance</h3>
                </div>
                <div class="min-h-48 max-h-96 overflow-y-auto">
                    @if($userPerformance->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($userPerformance as $workflow)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $workflow->recipient?->first_name }} {{ $workflow->recipient?->last_name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate">
                                            {{ $workflow->document?->title ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $wfColor = match($workflow->status) {
                                                    'approved' => 'emerald',
                                                    'rejected' => 'rose',
                                                    'pending' => 'amber',
                                                    default => 'blue',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $wfColor }}-100 text-{{ $wfColor }}-800 font-medium">
                                                {{ ucfirst($workflow->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $workflow->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex items-center justify-center h-48 text-gray-400">
                            <p class="text-sm">No performance data available</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
