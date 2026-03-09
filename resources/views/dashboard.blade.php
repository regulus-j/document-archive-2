<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 -mt-6 -mx-6 px-6 py-8 mb-6 rounded-b-2xl shadow-lg">
            <h2 class="text-3xl font-bold text-white leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-blue-100 mt-2">
                {{ __("Welcome back, " . auth()->user()->first_name . "!") }}
            </p>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-blue-50 to-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    [
                        'title' => 'Total Documents', 
                        'value' => $totalDocuments, 
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'color' => 'from-blue-500 to-blue-600'
                    ],
                    [
                        'title' => 'Total Users', 
                        'value' => $countCompanyUsers, 
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        'color' => 'from-indigo-500 to-indigo-600'
                    ],
                    [
                        'title' => 'Total Offices', 
                        'value' => $countOffices, 
                        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'color' => 'from-purple-500 to-purple-600'
                    ],
                    [
                        'title' => 'Incoming Documents', 
                        'value' => $incomingDocuments + $pendingDocuments, 
                        'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                        'color' => 'from-emerald-500 to-emerald-600'
                    ],
                ] as $stat)
                    <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-blue-100 hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-1">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-gradient-to-br {{ $stat['color'] }} rounded-xl p-4 shadow-lg">
                                    <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
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
                                        <dd class="text-3xl font-bold text-gray-900">
                                            {{ $stat['value'] }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- User & Office Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Performance -->
                <div class="bg-white shadow-xl rounded-xl border border-blue-100 overflow-hidden">
                    <div class="p-6 border-b border-blue-100 flex items-center">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-3 shadow-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">User Performance</h3>
                    </div>
                    <div class="overflow-y-auto" style="min-height:150px; max-height:320px;">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Docs Uploaded</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Docs Processed</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($userPerformance as $u)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm mr-2">
                                                    {{ substr($u->first_name, 0, 1) }}
                                                </div>
                                                <span class="text-sm text-gray-800">{{ $u->first_name }} {{ $u->last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $u->total_docs }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $u->processed_docs }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Office Performance -->
                <div class="bg-white shadow-xl rounded-xl border border-blue-100 overflow-hidden">
                    <div class="p-6 border-b border-blue-100 flex items-center">
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-3 shadow-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Office Performance</h3>
                    </div>
                    <div class="overflow-y-auto" style="min-height:150px; max-height:320px;">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Office</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Total Docs</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Pending</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($officePerformance as $office)
                                    <tr class="hover:bg-emerald-50 transition-colors">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-800">{{ $office->name }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $office->doc_count }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $office->pending_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $office->pending_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No offices found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Storage by Office KPI -->
            <div class="bg-white shadow-xl rounded-xl border border-blue-100 overflow-hidden">
                <div class="p-6 border-b border-blue-100 flex items-center">
                    <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-xl p-3 shadow-lg mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Storage by Office</h3>
                </div>
                <div class="overflow-y-auto" style="min-height:100px; max-height:280px;">
                    @if($storageByOffice->isEmpty())
                        <div class="p-6 text-center text-sm text-gray-500">No office storage data available</div>
                    @else
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($storageByOffice as $office)
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $office->name }}</p>
                                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $office->storage_formatted }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Disk usage</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-xl rounded-xl p-8 border border-blue-100">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-3 shadow-lg mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
                </div>
                
                <form action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Select Action</label>
                        <select id="action" name="action"
                            class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg shadow-sm">
                            <option value="">Select an action</option>
                            @foreach ([ 
                                ['value' => 'find', 'label' => 'Find Document', 'icon' => 'M10.5 3a7.5 7.5 0 015.916 12.5l4.243 4.242-1.414 1.414-4.242-4.243A7.5 7.5 0 1110.5 3z'], 
                                ['value' => 'receive', 'label' => 'Receive Document', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'], 
                                ['value' => 'acccept', 'label' => 'Accept Document', 'icon' => 'M5 13l4 4L19 7'], 
                                ['value' => 'reject', 'label' => 'Reject Document', 'icon' => 'M6 18L18 6M6 6l12 12'], 
                            ] as $action)
                                <option value="{{ $action['value'] }}">
                                    {{ $action['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Tracking Number
                        </label>
                        <div class="flex rounded-lg shadow-sm">
                            <input type="text" name="tracking_number" id="tracking_number"
                                class="flex-1 min-w-0 block w-full px-4 py-3 rounded-l-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300"
                                placeholder="Enter tracking number">
                                
                            <button type="button" onclick="startScanner()"
                                class="inline-flex items-center px-4 py-3 border border-gray-300 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                Scan QR
                            </button>
                            
                            <button type="submit"
                                class="inline-flex items-center px-4 py-3 border border-transparent text-sm font-medium rounded-r-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Submit
                            </button>
                        </div>
                        
                        <div id="reader" class="mt-6 hidden rounded-lg overflow-hidden shadow-lg border border-blue-100"></div>
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
                                
                                // Add a success notification
                                const notification = document.createElement('div');
                                notification.className = 'fixed bottom-4 right-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center';
                                notification.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    QR Code scanned successfully!
                                `;
                                document.body.appendChild(notification);
                                
                                // Remove notification after 3 seconds
                                setTimeout(() => {
                                    notification.remove();
                                }, 3000);
                            });
                        }
                    </script>
                    @endpush
                </form>
            </div>
            
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