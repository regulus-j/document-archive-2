<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 leading-tight">
                    {{ __('Office Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">{{ $office->name }} &middot; {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.office-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_format' => 'pdf']) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-slate-50 via-white to-indigo-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ============================================= --}}
            {{-- DATE FILTER BAR --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <form action="{{ route('reports.office-dashboard') }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-slate-400">to</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition shadow-sm">
                        Apply
                    </button>
                    <div class="flex ml-auto border border-slate-200 rounded-lg overflow-hidden">
                        <a href="{{ route('reports.office-dashboard', ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subDays(7)->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">7D</a>
                        <a href="{{ route('reports.office-dashboard', ['start_date' => now()->subMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subMonth()->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">1M</a>
                        <a href="{{ route('reports.office-dashboard', ['start_date' => now()->subMonths(3)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subMonths(3)->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">3M</a>
                        <a href="{{ route('reports.office-dashboard', ['start_date' => now()->subMonths(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subMonths(6)->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">6M</a>
                        <a href="{{ route('reports.office-dashboard', ['start_date' => now()->subYear()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 {{ now()->subYear()->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">1Y</a>
                    </div>
                </form>
            </div>

            {{-- ============================================= --}}
            {{-- KPI SUMMARY CARDS --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $documentsUploaded }}</p>
                    <p class="text-sm text-slate-500 mt-1">Total Documents</p>
                    <p class="text-xs text-slate-400 mt-0.5">In selected period</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-100">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $documentsUploadedToday }}</p>
                    <p class="text-sm text-slate-500 mt-1">Today's Documents</p>
                    <p class="text-xs text-slate-400 mt-0.5">Uploaded today</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-100">
                            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        @if($pendingWorkflows > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            Needs action
                        </span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $pendingWorkflows }}</p>
                    <p class="text-sm text-slate-500 mt-1">Pending Workflows</p>
                    <p class="text-xs text-slate-400 mt-0.5">Awaiting action</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $officeMembers->count() }}</p>
                    <p class="text-sm text-slate-500 mt-1">Team Members</p>
                    <p class="text-xs text-slate-400 mt-0.5">In your office</p>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- WORKFLOW STATISTICS --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Workflow Statistics</h3>
                        <p class="text-xs text-slate-500">Actions and performance for the selected period</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-4">
                            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Sent</p>
                            <p class="text-2xl font-bold text-indigo-900">{{ $workflowStats['workflows_sent'] }}</p>
                        </div>
                        <div class="rounded-xl bg-green-50 border border-green-100 p-4">
                            <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-1">Received</p>
                            <p class="text-2xl font-bold text-green-900">{{ $workflowStats['workflows_received'] }}</p>
                        </div>
                        <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-4">
                            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Approved</p>
                            <p class="text-2xl font-bold text-indigo-900">{{ $workflowStats['workflows_approved'] }}</p>
                        </div>
                        <div class="rounded-xl bg-red-50 border border-red-100 p-4">
                            <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-1">Rejected</p>
                            <p class="text-2xl font-bold text-red-900">{{ $workflowStats['workflows_rejected'] }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="rounded-xl bg-amber-50 border border-amber-100 p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-1">Avg Processing Time</p>
                                <p class="text-2xl font-bold text-amber-900">{{ $workflowStats['avg_processing_time'] }}</p>
                            </div>
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-amber-100">
                                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-1">Approval Rate</p>
                                <p class="text-2xl font-bold text-emerald-900">{{ $workflowStats['approval_rate'] }}%</p>
                            </div>
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $workflowStats['approval_rate'] >= 70 ? 'bg-green-100' : ($workflowStats['approval_rate'] >= 40 ? 'bg-amber-100' : 'bg-red-100') }}">
                                @if($workflowStats['approval_rate'] >= 70)
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- DOCUMENT VOLUME TRENDS --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Document Volume Trends</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Documents and workflows over time</p>
                </div>
                <div class="p-6">
                    <canvas id="documentTrendsChart" height="100"></canvas>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- CATEGORY & STATUS CHARTS --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Document Categories</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Distribution by classification</p>
                    </div>
                    <div class="p-6 flex items-center justify-center" style="min-height: 280px">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Status Distribution</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Current document statuses</p>
                    </div>
                    <div class="p-6 flex items-center justify-center" style="min-height: 280px">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- MEMBER PERFORMANCE TABLE --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-semibold text-slate-900">Member Performance</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Individual metrics for the selected period</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Uploads</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Forwarded</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Processed</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg Response</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg Processing</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Approval</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider" style="min-width: 140px;">Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($memberPerformanceMetrics as $metric)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($metric['member']->first_name, 0, 1)) }}{{ strtoupper(substr($metric['member']->last_name, 0, 1)) }}
                                        </div>
                                        <p class="text-sm font-medium text-slate-900">{{ $metric['member']->first_name }} {{ $metric['member']->last_name }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['uploads_count'] }}</td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['forwarded_count'] }}</td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['processed_count'] }}</td>
                                <td class="px-5 py-3 text-center text-xs text-slate-600">{{ $metric['avg_response_time'] }}</td>
                                <td class="px-5 py-3 text-center text-xs text-slate-600">{{ $metric['avg_processing_time'] }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                        {{ $metric['approval_rate'] >= 80 ? 'bg-green-100 text-green-800' : ($metric['approval_rate'] >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $metric['approval_rate'] }}%
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-700 {{ $metric['performance_score'] >= 70 ? 'bg-green-500' : ($metric['performance_score'] >= 40 ? 'bg-amber-400' : 'bg-red-500') }}"
                                                 style="width: {{ $metric['performance_score'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 w-8 text-right">{{ $metric['performance_score'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                    <svg class="mx-auto w-10 h-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    No performance data available
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- SMART INSIGHTS --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Performance Insights</h3>
                        <p class="text-xs text-slate-500">Automated recommendations based on your office data</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php $insightCount = 0; @endphp

                    @if(count($memberPerformanceMetrics) > 0)
                        @php
                            $slowestMemberKey = array_search(min(array_column($memberPerformanceMetrics, 'performance_score')), array_column($memberPerformanceMetrics, 'performance_score'));
                            $fastestMemberKey = array_search(max(array_column($memberPerformanceMetrics, 'performance_score')), array_column($memberPerformanceMetrics, 'performance_score'));
                            $slowestMember = $memberPerformanceMetrics[$slowestMemberKey];
                            $fastestMember = $memberPerformanceMetrics[$fastestMemberKey];
                        @endphp

                        @if($slowestMember['performance_score'] < 50)
                        @php $insightCount++; @endphp
                        <div class="flex gap-3 p-4 rounded-lg bg-amber-50 border border-amber-100">
                            <span class="flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Performance Opportunity</p>
                                <p class="text-sm text-amber-700 mt-1">{{ $slowestMember['member']->first_name }} {{ $slowestMember['member']->last_name }} has the lowest performance score ({{ $slowestMember['performance_score'] }}). Consider providing additional support or reviewing workload distribution.</p>
                            </div>
                        </div>
                        @endif

                        @if($fastestMember['performance_score'] > 80)
                        @php $insightCount++; @endphp
                        <div class="flex gap-3 p-4 rounded-lg bg-green-50 border border-green-100">
                            <span class="flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-green-800">Top Performer</p>
                                <p class="text-sm text-green-700 mt-1">{{ $fastestMember['member']->first_name }} {{ $fastestMember['member']->last_name }} excels with a score of {{ $fastestMember['performance_score'] }}. Consider having them share best practices with the team.</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    @if($workflowStats['avg_processing_minutes'] > 120)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-amber-50 border border-amber-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">High Processing Time</p>
                            <p class="text-sm text-amber-700 mt-1">Average processing time is {{ $workflowStats['avg_processing_time'] }}. Consider reviewing workflow procedures to improve efficiency.</p>
                        </div>
                    </div>
                    @elseif($workflowStats['avg_processing_minutes'] < 30)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-green-50 border border-green-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Excellent Processing Speed</p>
                            <p class="text-sm text-green-700 mt-1">Average processing time is {{ $workflowStats['avg_processing_time'] }} — outstanding! Keep up the great work.</p>
                        </div>
                    </div>
                    @endif

                    @if($workflowStats['approval_rate'] < 50)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-red-50 border border-red-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Low Approval Rate</p>
                            <p class="text-sm text-red-700 mt-1">Approval rate is {{ $workflowStats['approval_rate'] }}%. This may indicate quality issues with submitted documents or inconsistent review standards.</p>
                        </div>
                    </div>
                    @elseif($workflowStats['approval_rate'] > 95)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-indigo-50 border border-indigo-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-indigo-800">Very High Approval Rate</p>
                            <p class="text-sm text-indigo-700 mt-1">Approval rate is {{ $workflowStats['approval_rate'] }}%. While this may indicate quality, ensure reviews remain thorough and standards are maintained.</p>
                        </div>
                    </div>
                    @endif

                    @if($insightCount === 0)
                    <div class="col-span-2 flex gap-3 p-4 rounded-lg bg-green-50 border border-green-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Looking Good!</p>
                            <p class="text-sm text-green-700 mt-1">All metrics are within healthy ranges. Your office is performing well — keep it up!</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

{{-- ============================================= --}}
{{-- CHART.JS SCRIPTS --}}
{{-- ============================================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;

    const chartColors = ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6','#F97316','#6366F1','#06B6D4'];
    const statusColors = { 'approved': '#10B981', 'pending': '#F59E0B', 'rejected': '#EF4444', 'received': '#3B82F6', 'forwarded': '#8B5CF6', 'released': '#14B8A6' };

    // ===== Document Trends =====
    try {
        var trendsCtx = document.getElementById('documentTrendsChart').getContext('2d');
        var trendsData = @json($documentTrends);
        if (trendsData && trendsData.months && trendsData.months.length > 0) {
            var blueGrad = trendsCtx.createLinearGradient(0, 0, 0, 300);
            blueGrad.addColorStop(0, 'rgba(59,130,246,0.25)');
            blueGrad.addColorStop(1, 'rgba(59,130,246,0.02)');
            var redGrad = trendsCtx.createLinearGradient(0, 0, 0, 300);
            redGrad.addColorStop(0, 'rgba(239,68,68,0.2)');
            redGrad.addColorStop(1, 'rgba(239,68,68,0.02)');

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: trendsData.months,
                    datasets: [{
                        label: 'Documents Created',
                        data: trendsData.document_counts,
                        backgroundColor: blueGrad,
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6
                    }, {
                        label: 'Workflows Created',
                        data: trendsData.workflow_counts,
                        backgroundColor: redGrad,
                        borderColor: 'rgba(239,68,68,1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.9)',
                            cornerRadius: 8,
                            padding: 12
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        }
    } catch(e) { console.error('Trends chart error:', e); }

    // ===== Categories Doughnut =====
    try {
        var catData = @json($categoryDistribution ?? []);
        var catCanvas = document.getElementById('categoriesChart');
        if (catData && catData.length > 0) {
            new Chart(catCanvas, {
                type: 'doughnut',
                data: {
                    labels: catData.map(i => i.category),
                    datasets: [{
                        data: catData.map(i => i.count),
                        backgroundColor: chartColors.slice(0, catData.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '55%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } } }
                }
            });
        } else {
            catCanvas.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-slate-400"><svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg><p class="text-sm">No category data</p></div>';
        }
    } catch(e) { console.error('Categories chart error:', e); }

    // ===== Status Doughnut =====
    try {
        var statusData = @json($statusDistribution ?? []);
        var statusCanvas = document.getElementById('statusChart');
        if (statusData && statusData.length > 0) {
            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(i => i.status.charAt(0).toUpperCase() + i.status.slice(1)),
                    datasets: [{
                        data: statusData.map(i => i.count),
                        backgroundColor: statusData.map(i => statusColors[i.status] || chartColors[statusData.indexOf(i) % chartColors.length]),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '55%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } } }
                }
            });
        } else {
            statusCanvas.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-slate-400"><svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg><p class="text-sm">No status data</p></div>';
        }
    } catch(e) { console.error('Status chart error:', e); }
});
</script>
