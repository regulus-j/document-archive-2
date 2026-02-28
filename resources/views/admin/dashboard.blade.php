<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Super Admin Dashboard') }}
            </h2>

        </div>
    </x-slot>

    <div class="py-6 bg-gradient-to-b from-indigo-50 to-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Companies Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-indigo-600 text-sm font-medium">Total Companies</div>
                                <div class="text-3xl font-bold text-slate-900">{{ $totalCompanies }}</div>
                                <div class="text-sm text-emerald-500 mt-1">

                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Users Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-indigo-600 text-sm font-medium">Total Users</div>
                                <div class="text-3xl font-bold text-slate-900">{{ $totalUsers }}</div>
                                <div class="text-sm text-emerald-500 mt-1">

                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Documents Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-cyan-500 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-cyan-600 text-sm font-medium">Total Documents</div>
                                <div class="text-3xl font-bold text-slate-900">{{ $totalDocuments }}</div>
                                <div class="text-sm text-emerald-500 mt-1">

                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Subscriptions Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-sky-500 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sky-600 text-sm font-medium">Active Subscriptions</div>
                                <div class="text-3xl font-bold text-slate-900">{{ $activeSubscriptions }}</div>
                                <div class="text-sm text-emerald-500 mt-1">

                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-sky-500 to-sky-600 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Activities -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-indigo-100">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-slate-800">Recent Activities</h3>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gradient-to-r from-indigo-50 to-indigo-50">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                                        Company</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                                        Action</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach($recentActivities as $activity)
                                    <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                    {{ substr($activity['company_name'] ?? $activity->company_name ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-slate-900">
                                                        {{ $activity['company_name'] ?? $activity->company_name ?? 'No Company' }}
                                                    </div>
                                                    <div class="text-sm text-indigo-600">
                                                        ID: {{ $activity['company_id'] ?? $activity->company_id ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-slate-900">{{ $activity['action'] ?? $activity->action ?? 'N/A' }}</div>
                                            <div class="text-sm text-slate-500">{{ $activity['user_name'] ?? $activity->user_name ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ \Carbon\Carbon::parse($activity['created_at'] ?? $activity->created_at)->diffForHumans() }}
                                        </td>
                                        <td>
                                            <div class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ date('M j, Y, g:i a', strtotime($activity['created_at'] ?? $activity->created_at)) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if(($activity['type'] ?? $activity->type ?? '') === 'document')
                                                <a href="{{ route('documents.show', $activity['id'] ?? $activity['document_id'] ?? 0) }}" class="text-indigo-600 hover:text-indigo-900">View Document</a>
                                            @else
                                                <a href="{{ route('users.show', $activity['id'] ?? $activity['user_id'] ?? 0) }}" class="text-indigo-600 hover:text-indigo-900">View Profile</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>