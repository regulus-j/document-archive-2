<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 leading-tight">
                    {{ __('Performance Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">{{ $company->company_name }} &middot; {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_format' => 'pdf']) }}" 
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
                <form action="{{ route('reports.company-dashboard') }}" method="GET" class="flex flex-wrap items-center gap-4">
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
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subDays(7)->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">7D</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subMonth()->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">1M</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subMonths(3)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subMonths(3)->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">3M</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subMonths(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 border-r border-slate-200 {{ now()->subMonths(6)->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">6M</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subYear()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 text-xs font-medium hover:bg-slate-50 {{ now()->subYear()->format('Y-m-d') == $startDate ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">1Y</a>
                    </div>
                </form>
            </div>

            {{-- ============================================= --}}
            {{-- KPI SUMMARY CARDS with trend indicators --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Documents --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        @if($periodComparison['documents']['change'] != 0)
                        <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full {{ $periodComparison['documents']['change'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($periodComparison['documents']['change'] > 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            {{ abs($periodComparison['documents']['change']) }}%
                        </span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $periodComparison['documents']['current'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Documents Created</p>
                    <p class="text-xs text-slate-400 mt-0.5">prev: {{ $periodComparison['documents']['previous'] }}</p>
                </div>

                {{-- Workflows --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                        @if($periodComparison['workflows']['change'] != 0)
                        <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full {{ $periodComparison['workflows']['change'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($periodComparison['workflows']['change'] > 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            {{ abs($periodComparison['workflows']['change']) }}%
                        </span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $periodComparison['workflows']['current'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Workflows Sent</p>
                    <p class="text-xs text-slate-400 mt-0.5">prev: {{ $periodComparison['workflows']['previous'] }}</p>
                </div>

                {{-- Completion Rate --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-100">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span class="text-xs font-medium px-2 py-1 rounded-full {{ $workflowCompletion['completion_rate'] >= 80 ? 'bg-green-100 text-green-700' : ($workflowCompletion['completion_rate'] >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $workflowCompletion['completion_rate'] >= 80 ? 'Healthy' : ($workflowCompletion['completion_rate'] >= 50 ? 'Fair' : 'Needs Attention') }}
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $workflowCompletion['completion_rate'] }}%</p>
                    <p class="text-sm text-slate-500 mt-1">Completion Rate</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $workflowCompletion['completed'] }}/{{ $workflowCompletion['total'] }} workflows</p>
                </div>

                {{-- Avg Turnaround --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-100">
                            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        @if($periodComparison['avg_processing']['change'] != 0)
                        <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full {{ $periodComparison['avg_processing']['change'] < 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($periodComparison['avg_processing']['change'] < 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @endif
                            </svg>
                            {{ abs($periodComparison['avg_processing']['change']) }}%
                        </span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $workflowCompletion['avg_turnaround_formatted'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Avg Turnaround</p>
                    <p class="text-xs text-slate-400 mt-0.5">prev: {{ $periodComparison['avg_processing']['previous_formatted'] }}</p>
                </div>

                {{-- Storage --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-slate-900">{{ $storageMetrics['formatted_total_size'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">Total Storage</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $storageMetrics['document_count'] }} docs &middot; {{ $storageMetrics['attachment_count'] }} files</p>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- WORKFLOW FUNNEL + DOCUMENT AGING + ACTIVITY --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Workflow Funnel --}}
                <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Workflow Breakdown</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Current period status distribution</p>
                    </div>
                    <div class="p-6 space-y-4">
                        @php
                            $funnelItems = [
                                ['label' => 'Total Workflows', 'value' => $workflowCompletion['total'], 'color' => 'blue', 'width' => 100],
                                ['label' => 'Completed', 'value' => $workflowCompletion['completed'], 'color' => 'green', 'width' => $workflowCompletion['total'] > 0 ? ($workflowCompletion['completed']/$workflowCompletion['total'])*100 : 0],
                                ['label' => 'Approved', 'value' => $workflowCompletion['approved'], 'color' => 'emerald', 'width' => $workflowCompletion['total'] > 0 ? ($workflowCompletion['approved']/$workflowCompletion['total'])*100 : 0],
                                ['label' => 'Rejected', 'value' => $workflowCompletion['rejected'], 'color' => 'red', 'width' => $workflowCompletion['total'] > 0 ? ($workflowCompletion['rejected']/$workflowCompletion['total'])*100 : 0],
                                ['label' => 'Pending', 'value' => $workflowCompletion['pending'], 'color' => 'amber', 'width' => $workflowCompletion['total'] > 0 ? ($workflowCompletion['pending']/$workflowCompletion['total'])*100 : 0],
                            ];
                        @endphp
                        @foreach($funnelItems as $item)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-slate-700">{{ $item['label'] }}</span>
                                <span class="font-semibold text-slate-900">{{ $item['value'] }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5">
                                <div class="bg-{{ $item['color'] }}-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ max($item['width'], ($item['value'] > 0 ? 3 : 0)) }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-sm text-slate-600">Approval Rate</span>
                            <span class="text-lg font-bold {{ $workflowCompletion['approval_rate'] >= 70 ? 'text-green-600' : ($workflowCompletion['approval_rate'] >= 40 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ $workflowCompletion['approval_rate'] }}%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Document Aging --}}
                <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">Pending Document Age</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $documentAging['total_pending'] }} total pending</p>
                            </div>
                            @if($documentAging['critical_count'] > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                {{ $documentAging['critical_count'] }} overdue
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="agingChart" height="220"></canvas>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Recent Activity</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Latest workflow actions</p>
                    </div>
                    <div class="divide-y divide-slate-50 max-h-[380px] overflow-y-auto">
                        @forelse($recentActivity as $activity)
                        <div class="px-5 py-3 hover:bg-slate-50 transition">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full 
                                    {{ $activity['status'] === 'approved' ? 'bg-green-100' : ($activity['status'] === 'rejected' ? 'bg-red-100' : ($activity['status'] === 'received' ? 'bg-indigo-100' : 'bg-slate-100')) }}">
                                    @if($activity['status'] === 'approved')
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($activity['status'] === 'rejected')
                                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @elseif($activity['status'] === 'received')
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $activity['document_title'] }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $activity['sender_name'] }}
                                        <svg class="inline w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        {{ $activity['recipient_name'] }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $activity['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($activity['status'] === 'rejected' ? 'bg-red-100 text-red-800' : ($activity['status'] === 'received' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-800')) }}">
                                        {{ ucfirst($activity['status']) }}
                                    </span>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $activity['time_ago'] }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <svg class="mx-auto w-10 h-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="text-sm text-slate-500">No recent activity</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- DOCUMENT VOLUME TRENDS --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Document Volume Trends</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Documents created and workflows over time</p>
                    </div>
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
                    <div class="p-6 flex items-center justify-center" style="min-height: 300px">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-base font-semibold text-slate-900">Status Distribution</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Current document statuses</p>
                    </div>
                    <div class="p-6 flex items-center justify-center" style="min-height: 300px">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- WORKFLOW BOTTLENECKS --}}
            {{-- ============================================= --}}
            @if($workflowBottlenecks['total_pending'] > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Workflow Bottlenecks</h3>
                        <p class="text-xs text-slate-500">{{ $workflowBottlenecks['total_pending'] }} pending workflows need attention</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
                    {{-- Pending by User --}}
                    <div class="p-5">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">By User</h4>
                        <div class="space-y-3">
                            @foreach($workflowBottlenecks['pending_by_user']->take(5) as $item)
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $item['user']->first_name ?? 'Unknown' }} {{ $item['user']->last_name ?? '' }}</p>
                                    <p class="text-xs text-slate-500">Avg wait: {{ $item['avg_wait_formatted'] }}</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    {{ $item['pending_count'] }}
                                </span>
                            </div>
                            @endforeach
                            @if($workflowBottlenecks['pending_by_user']->isEmpty())
                            <p class="text-sm text-slate-400 italic">No pending items</p>
                            @endif
                        </div>
                    </div>
                    {{-- Pending by Office --}}
                    <div class="p-5">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">By Office</h4>
                        <div class="space-y-3">
                            @foreach($workflowBottlenecks['pending_by_office']->take(5) as $item)
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $item['office']->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500">Avg wait: {{ $item['avg_wait_formatted'] }}</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    {{ $item['pending_count'] }}
                                </span>
                            </div>
                            @endforeach
                            @if($workflowBottlenecks['pending_by_office']->isEmpty())
                            <p class="text-sm text-slate-400 italic">No pending items</p>
                            @endif
                        </div>
                    </div>
                    {{-- Oldest Pending --}}
                    <div class="p-5">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">Longest Waiting</h4>
                        <div class="space-y-3">
                            @foreach($workflowBottlenecks['oldest_pending'] as $item)
                            <div class="flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $item['document']->title ?? 'Document' }}</p>
                                    <p class="text-xs text-slate-500">Since {{ $item['created_at'] }}</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item['waiting_hours'] > 168 ? 'bg-red-100 text-red-800' : ($item['waiting_hours'] > 48 ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800') }}">
                                    {{ $item['waiting_formatted'] }}
                                </span>
                            </div>
                            @endforeach
                            @if(count($workflowBottlenecks['oldest_pending']) === 0)
                            <p class="text-sm text-slate-400 italic">No pending items</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- USER PERFORMANCE TABLE --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">User Performance</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Individual metrics for the selected period</p>
                    </div>
                    <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'user_performance']) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs font-medium transition shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
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
                            @forelse($userPerformanceMetrics as $metric)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($metric['user']->first_name, 0, 1)) }}{{ strtoupper(substr($metric['user']->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900">{{ $metric['user']->first_name }} {{ $metric['user']->last_name }}</p>
                                            <p class="text-xs text-slate-400">{{ $metric['total_documents_handled'] }} total handled</p>
                                        </div>
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
                                    No performance data for the selected period
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- OFFICE PERFORMANCE TABLE --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Office Performance</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Departmental efficiency for the selected period</p>
                    </div>
                    <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'office_performance']) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs font-medium transition shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Office</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Members</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Originated</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Received</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Processed</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg Processing</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider" style="min-width: 140px;">Efficiency</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($officePerformanceMetrics as $metric)
                            <tr class="hover:bg-indigo-50/30 transition">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($metric['office']->name, 0, 2)) }}
                                        </div>
                                        <p class="text-sm font-medium text-slate-900">{{ $metric['office']->name }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['user_count'] }}</td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['documents_originated'] }}</td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['documents_received'] }}</td>
                                <td class="px-5 py-3 text-center text-sm text-slate-700">{{ $metric['workflows_processed'] }}</td>
                                <td class="px-5 py-3 text-center text-xs text-slate-600">{{ $metric['avg_processing_time'] }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-700 {{ $metric['efficiency_score'] >= 70 ? 'bg-green-500' : ($metric['efficiency_score'] >= 40 ? 'bg-amber-400' : 'bg-red-500') }}"
                                                 style="width: {{ $metric['efficiency_score'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 w-8 text-right">{{ $metric['efficiency_score'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    <svg class="mx-auto w-10 h-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    No office data for the selected period
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- STORAGE SECTION --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- User Storage --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">Storage by User</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Disk usage per team member</p>
                        </div>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'user_storage']) }}" 
                           class="inline-flex items-center px-2.5 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs font-medium transition">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                            Excel
                        </a>
                    </div>
                    <div class="p-5">
                        <canvas id="userStorageChart" height="200"></canvas>
                    </div>
                    <div class="border-t border-slate-50">
                        <div class="max-h-48 overflow-y-auto">
                            <table class="w-full">
                                <thead class="sticky top-0 bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-slate-500">User</th>
                                        <th class="px-5 py-2 text-right text-xs font-medium text-slate-500">Docs</th>
                                        <th class="px-5 py-2 text-right text-xs font-medium text-slate-500">Size</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($storageMetrics['user_storage'] as $userStorage)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-2 text-sm text-slate-700">{{ $userStorage['user']->first_name }} {{ $userStorage['user']->last_name }}</td>
                                        <td class="px-5 py-2 text-sm text-slate-500 text-right">{{ $userStorage['count'] }}</td>
                                        <td class="px-5 py-2 text-sm font-medium text-slate-900 text-right">{{ $userStorage['formatted_size'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Office Storage --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">Storage by Office</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Disk usage per department</p>
                        </div>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'office_storage']) }}" 
                           class="inline-flex items-center px-2.5 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs font-medium transition">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                            Excel
                        </a>
                    </div>
                    <div class="p-5">
                        <canvas id="officeStorageChart" height="200"></canvas>
                    </div>
                    <div class="border-t border-slate-50">
                        <div class="max-h-48 overflow-y-auto">
                            <table class="w-full">
                                <thead class="sticky top-0 bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-2 text-left text-xs font-medium text-slate-500">Office</th>
                                        <th class="px-5 py-2 text-right text-xs font-medium text-slate-500">Docs</th>
                                        <th class="px-5 py-2 text-right text-xs font-medium text-slate-500">Size</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($storageMetrics['office_storage'] as $officeStorage)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-2 text-sm text-slate-700">{{ $officeStorage['office']->name }}</td>
                                        <td class="px-5 py-2 text-sm text-slate-500 text-right">{{ $officeStorage['count'] }}</td>
                                        <td class="px-5 py-2 text-sm font-medium text-slate-900 text-right">{{ $officeStorage['formatted_size'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- SMART INSIGHTS / RECOMMENDATIONS --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Smart Insights</h3>
                        <p class="text-xs text-slate-500">Automated recommendations based on your data</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php $insightCount = 0; @endphp

                    {{-- Bottleneck Warning --}}
                    @if($documentAging['critical_count'] > 0)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-red-50 border border-red-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Overdue Documents</p>
                            <p class="text-sm text-red-700 mt-1">{{ $documentAging['critical_count'] }} workflows have been pending for more than 7 days. Consider following up with the responsible parties to prevent further delays.</p>
                        </div>
                    </div>
                    @endif

                    {{-- Low Completion Rate --}}
                    @if($workflowCompletion['completion_rate'] < 60 && $workflowCompletion['total'] > 5)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-amber-50 border border-amber-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Low Completion Rate</p>
                            <p class="text-sm text-amber-700 mt-1">Only {{ $workflowCompletion['completion_rate'] }}% of workflows are being completed. Review the bottleneck analysis above to identify where workflows are stalling.</p>
                        </div>
                    </div>
                    @endif

                    {{-- Slowest/Fastest User --}}
                    @if(count($userPerformanceMetrics) > 0)
                    @php
                        $slowestUserKey = array_search(min(array_column($userPerformanceMetrics, 'performance_score')), array_column($userPerformanceMetrics, 'performance_score'));
                        $fastestUserKey = array_search(max(array_column($userPerformanceMetrics, 'performance_score')), array_column($userPerformanceMetrics, 'performance_score'));
                        $slowestUser = $userPerformanceMetrics[$slowestUserKey] ?? null;
                        $fastestUser = $userPerformanceMetrics[$fastestUserKey] ?? null;
                    @endphp

                    @if($slowestUser && $slowestUser['performance_score'] < 50)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-amber-50 border border-amber-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Performance Opportunity</p>
                            <p class="text-sm text-amber-700 mt-1">{{ $slowestUser['user']->first_name }} {{ $slowestUser['user']->last_name }} has a performance score of {{ $slowestUser['performance_score'] }}. Consider providing additional training or reviewing their workload distribution.</p>
                        </div>
                    </div>
                    @endif

                    @if($fastestUser && $fastestUser['performance_score'] > 80)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-green-50 border border-green-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Top Performer</p>
                            <p class="text-sm text-green-700 mt-1">{{ $fastestUser['user']->first_name }} {{ $fastestUser['user']->last_name }} is excelling with a score of {{ $fastestUser['performance_score'] }}. Consider having them share best practices with the team.</p>
                        </div>
                    </div>
                    @endif
                    @endif

                    {{-- Volume Trend --}}
                    @if(count($documentTrends['document_counts']) > 1)
                    @php
                        $lastIndex = count($documentTrends['document_counts']) - 1;
                        $previousIndex = $lastIndex - 1;
                        $currentValue = $documentTrends['document_counts'][$lastIndex];
                        $previousValue = $documentTrends['document_counts'][$previousIndex];
                        $percentChange = $previousValue > 0 ? round((($currentValue - $previousValue) / $previousValue) * 100) : 0;
                    @endphp

                    @if(abs($percentChange) > 30)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg {{ $percentChange > 0 ? 'bg-indigo-50 border-indigo-100' : 'bg-amber-50 border-amber-100' }} border">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 {{ $percentChange > 0 ? 'text-indigo-500' : 'text-amber-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold {{ $percentChange > 0 ? 'text-indigo-800' : 'text-amber-800' }}">Volume {{ $percentChange > 0 ? 'Increase' : 'Decrease' }}</p>
                            <p class="text-sm {{ $percentChange > 0 ? 'text-indigo-700' : 'text-amber-700' }} mt-1">
                                Document volume {{ $percentChange > 0 ? 'increased' : 'decreased' }} by {{ abs($percentChange) }}% compared to the previous month.
                                {{ $percentChange > 0 ? 'Ensure adequate resources to handle the growth.' : 'Investigate if this is expected or indicates a process issue.' }}
                            </p>
                        </div>
                    </div>
                    @endif
                    @endif

                    {{-- Storage Warning --}}
                    @php
                        $totalStorage = $storageMetrics['total_size'];
                        $storageInsight = false;
                        foreach($storageMetrics['user_storage'] as $user) {
                            if($totalStorage > 0 && $user['size'] > ($totalStorage * 0.4)) {
                                $storageInsightUser = $user;
                                $storageInsight = true;
                                break;
                            }
                        }
                    @endphp
                    @if($storageInsight)
                    @php $insightCount++; @endphp
                    <div class="flex gap-3 p-4 rounded-lg bg-purple-50 border border-purple-100">
                        <span class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-purple-800">Storage Concentration</p>
                            <p class="text-sm text-purple-700 mt-1">{{ $storageInsightUser['user']->first_name }} {{ $storageInsightUser['user']->last_name }} uses {{ round(($storageInsightUser['size'] / $totalStorage) * 100) }}% of total storage ({{ $storageInsightUser['formatted_size'] }}). Review their document management practices.</p>
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
                            <p class="text-sm text-green-700 mt-1">All metrics are within healthy ranges. Keep up the great work!</p>
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
    // Shared defaults
    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;

    const palette = {
        blue: { bg: 'rgba(59,130,246,0.15)', border: 'rgba(59,130,246,1)' },
        red: { bg: 'rgba(239,68,68,0.15)', border: 'rgba(239,68,68,1)' },
        green: { bg: 'rgba(16,185,129,0.15)', border: 'rgba(16,185,129,1)' },
        amber: { bg: 'rgba(245,158,11,0.15)', border: 'rgba(245,158,11,1)' },
        indigo: { bg: 'rgba(99,102,241,0.15)', border: 'rgba(99,102,241,1)' },
        purple: { bg: 'rgba(139,92,246,0.15)', border: 'rgba(139,92,246,1)' },
    };
    const chartColors = ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6','#F97316','#6366F1','#06B6D4'];

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 B';
        const k = 1024, dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B','KB','MB','GB','TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // ===== Document Aging Chart =====
    try {
        var agingData = @json($documentAging['buckets']);
        if (agingData) {
            new Chart(document.getElementById('agingChart'), {
                type: 'doughnut',
                data: {
                    labels: agingData.map(b => b.label),
                    datasets: [{
                        data: agingData.map(b => b.count),
                        backgroundColor: agingData.map(b => b.color),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.raw} workflow${ctx.raw !== 1 ? 's' : ''}`
                            }
                        }
                    }
                }
            });
        }
    } catch(e) { console.error('Aging chart error:', e); }

    // ===== Document Trends Chart =====
    try {
        var trendsData = @json($documentTrends);
        if (trendsData && trendsData.months && trendsData.months.length > 0) {
            var trendsCtx = document.getElementById('documentTrendsChart').getContext('2d');

            // Create gradient fills
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
                    datasets: [
                        {
                            label: 'Documents Created',
                            data: trendsData.document_counts,
                            backgroundColor: blueGrad,
                            borderColor: palette.blue.border,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Workflows Created',
                            data: trendsData.workflow_counts,
                            backgroundColor: redGrad,
                            borderColor: palette.red.border,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.9)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            cornerRadius: 8,
                            padding: 12,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8' }
                        }
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
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } }
                    }
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
        var statusColors = { 'approved': '#10B981', 'pending': '#F59E0B', 'rejected': '#EF4444', 'received': '#3B82F6', 'forwarded': '#8B5CF6', 'released': '#14B8A6' };
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
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } }
                    }
                }
            });
        } else {
            statusCanvas.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-slate-400"><svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg><p class="text-sm">No status data</p></div>';
        }
    } catch(e) { console.error('Status chart error:', e); }

    // ===== User Storage Bar =====
    try {
        var userStorData = @json($storageMetrics['user_storage'] ?? []);
        if (userStorData && userStorData.length > 0) {
            new Chart(document.getElementById('userStorageChart'), {
                type: 'bar',
                data: {
                    labels: userStorData.map(i => (i.user.first_name || '') + ' ' + ((i.user.last_name || '').charAt(0)) + '.'),
                    datasets: [{
                        label: 'Storage Used',
                        data: userStorData.map(i => i.size),
                        backgroundColor: palette.blue.bg,
                        borderColor: palette.blue.border,
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { callback: v => formatBytes(v, 1), color: '#94a3b8' }
                        },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
                    }
                }
            });
        }
    } catch(e) { console.error('User storage chart error:', e); }

    // ===== Office Storage Bar =====
    try {
        var offStorData = @json($storageMetrics['office_storage'] ?? []);
        if (offStorData && offStorData.length > 0) {
            new Chart(document.getElementById('officeStorageChart'), {
                type: 'bar',
                data: {
                    labels: offStorData.map(i => i.office.name),
                    datasets: [{
                        label: 'Storage Used',
                        data: offStorData.map(i => i.size),
                        backgroundColor: palette.green.bg,
                        borderColor: palette.green.border,
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { callback: v => formatBytes(v, 1), color: '#94a3b8' }
                        },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
                    }
                }
            });
        }
    } catch(e) { console.error('Office storage chart error:', e); }
});
</script>
