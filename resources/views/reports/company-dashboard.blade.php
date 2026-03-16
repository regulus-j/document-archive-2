<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 leading-tight">
                        {{ __('Analytics') }}
                    </h2>
                    <p class="mt-0.5 text-sm text-slate-500 font-medium">{{ $company->company_name }} · {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_format' => 'pdf']) }}" 
                   class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- ============================================= --}}
            {{-- DATE FILTER BAR --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl border border-slate-200/60 p-3.5 backdrop-blur-sm">
                <form action="{{ route('reports.company-dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="bg-transparent border-none text-sm focus:ring-0 p-0 text-slate-700 font-medium">
                        <span class="text-slate-300">→</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="bg-transparent border-none text-sm focus:ring-0 p-0 text-slate-700 font-medium">
                    </div>
                    <button type="submit" class="px-3.5 py-1.5 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-sm font-medium transition-all duration-200">
                        Apply
                    </button>
                    <div class="flex ml-auto bg-slate-100/50 rounded-lg p-0.5">
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 {{ now()->subDays(7)->format('Y-m-d') == $startDate ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">7D</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 {{ now()->subMonth()->format('Y-m-d') == $startDate ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">1M</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subMonths(3)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 {{ now()->subMonths(3)->format('Y-m-d') == $startDate ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">3M</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subMonths(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 {{ now()->subMonths(6)->format('Y-m-d') == $startDate ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">6M</a>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => now()->subYear()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 {{ now()->subYear()->format('Y-m-d') == $startDate ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">1Y</a>
                    </div>
                </form>
            </div>

            {{-- ============================================= --}}
            {{-- KPI SUMMARY CARDS with trend indicators --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Documents --}}
                <div class="group relative bg-white rounded-xl border border-slate-200/60 p-5 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            @if($periodComparison['documents']['change'] != 0)
                            <div class="flex items-center gap-1 px-2 py-1 rounded-lg {{ $periodComparison['documents']['change'] > 0 ? 'bg-emerald-100/50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    @if($periodComparison['documents']['change'] > 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                                    @endif
                                </svg>
                                <span class="text-xs font-bold">{{ abs($periodComparison['documents']['change']) }}%</span>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ number_format($periodComparison['documents']['current']) }}</p>
                            <p class="text-sm font-medium text-slate-600">Documents Created</p>
                            <p class="text-xs text-slate-400">{{ number_format($periodComparison['documents']['previous']) }} previous period</p>
                        </div>
                    </div>
                </div>

                {{-- Workflows --}}
                <div class="group relative bg-white rounded-xl border border-slate-200/60 p-5 hover:border-violet-200 hover:shadow-lg hover:shadow-violet-500/10 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-50/50 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg shadow-violet-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            @if($periodComparison['workflows']['change'] != 0)
                            <div class="flex items-center gap-1 px-2 py-1 rounded-lg {{ $periodComparison['workflows']['change'] > 0 ? 'bg-emerald-100/50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    @if($periodComparison['workflows']['change'] > 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                                    @endif
                                </svg>
                                <span class="text-xs font-bold">{{ abs($periodComparison['workflows']['change']) }}%</span>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ number_format($periodComparison['workflows']['current']) }}</p>
                            <p class="text-sm font-medium text-slate-600">Workflows Sent</p>
                            <p class="text-xs text-slate-400">{{ number_format($periodComparison['workflows']['previous']) }} previous period</p>
                        </div>
                    </div>
                </div>

                {{-- Completion Rate --}}
                <div class="group relative bg-white rounded-xl border border-slate-200/60 p-5 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $workflowCompletion['completion_rate'] >= 80 ? 'bg-emerald-100/50 text-emerald-700' : ($workflowCompletion['completion_rate'] >= 50 ? 'bg-amber-100/50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                {{ $workflowCompletion['completion_rate'] >= 80 ? 'Excellent' : ($workflowCompletion['completion_rate'] >= 50 ? 'Good' : 'Low') }}
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ $workflowCompletion['completion_rate'] }}%</p>
                            <p class="text-sm font-medium text-slate-600">Completion Rate</p>
                            <p class="text-xs text-slate-400">{{ number_format($workflowCompletion['completed']) }}/{{ number_format($workflowCompletion['total']) }} complete</p>
                        </div>
                    </div>
                </div>

                {{-- Avg Turnaround --}}
                <div class="group relative bg-white rounded-xl border border-slate-200/60 p-5 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            @if($periodComparison['avg_processing']['change'] != 0)
                            <div class="flex items-center gap-1 px-2 py-1 rounded-lg {{ $periodComparison['avg_processing']['change'] < 0 ? 'bg-emerald-100/50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    @if($periodComparison['avg_processing']['change'] < 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    @endif
                                </svg>
                                <span class="text-xs font-bold">{{ abs($periodComparison['avg_processing']['change']) }}%</span>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ $workflowCompletion['avg_turnaround_formatted'] }}</p>
                            <p class="text-sm font-medium text-slate-600">Avg Turnaround</p>
                            <p class="text-xs text-slate-400">{{ $periodComparison['avg_processing']['previous_formatted'] }} previous</p>
                        </div>
                    </div>
                </div>

                {{-- Storage --}}
                <div class="group relative bg-white rounded-xl border border-slate-200/60 p-5 hover:border-purple-200 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50/50 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ $storageMetrics['formatted_total_size'] }}</p>
                            <p class="text-sm font-medium text-slate-600">Total Storage</p>
                            <p class="text-xs text-slate-400">{{ number_format($storageMetrics['document_count']) }} docs · {{ number_format($storageMetrics['attachment_count']) }} files</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- WORKFLOW FUNNEL + DOCUMENT AGING + ACTIVITY --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Workflow Funnel --}}
                <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="text-sm font-bold text-slate-900">Workflow Breakdown</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Status distribution</p>
                    </div>
                    <div class="p-5 space-y-3.5">
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
                        <div class="group">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-semibold text-slate-700 group-hover:text-slate-900 transition-colors">{{ $item['label'] }}</span>
                                <span class="font-bold text-slate-900 tabular-nums">{{ number_format($item['value']) }}</span>
                            </div>
                            <div class="w-full bg-slate-100/50 rounded-full h-2 overflow-hidden">
                                <div class="bg-{{ $item['color'] }}-500 h-2 rounded-full transition-all duration-700 ease-out group-hover:opacity-90" style="width: {{ max($item['width'], ($item['value'] > 0 ? 3 : 0)) }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50 -mx-5 px-5 py-3 -mb-5">
                            <span class="text-sm font-semibold text-slate-700">Approval Rate</span>
                            <span class="text-xl font-bold tabular-nums {{ $workflowCompletion['approval_rate'] >= 70 ? 'text-emerald-600' : ($workflowCompletion['approval_rate'] >= 40 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $workflowCompletion['approval_rate'] }}%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Document Aging --}}
                <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Document Age</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ number_format($documentAging['total_pending']) }} pending</p>
                            </div>
                            @if($documentAging['critical_count'] > 0)
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50">
                                <div class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></div>
                                <span class="text-xs font-bold text-rose-700">{{ $documentAging['critical_count'] }} overdue</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-5">
                        <canvas id="agingChart" height="220"></canvas>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="text-sm font-bold text-slate-900">Recent Activity</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Latest actions</p>
                    </div>
                    <div class="divide-y divide-slate-50 max-h-[380px] overflow-y-auto">
                        @forelse($recentActivity as $activity)
                        <div class="px-5 py-3 hover:bg-slate-50/50 transition-colors group">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-lg 
                                    {{ $activity['status'] === 'approved' ? 'bg-emerald-100 group-hover:bg-emerald-200' : ($activity['status'] === 'rejected' ? 'bg-rose-100 group-hover:bg-rose-200' : ($activity['status'] === 'received' ? 'bg-indigo-100 group-hover:bg-indigo-200' : 'bg-slate-100/50 group-hover:bg-slate-200')) }} transition-colors">
                                    @if($activity['status'] === 'approved')
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($activity['status'] === 'rejected')
                                        <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @elseif($activity['status'] === 'received')
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-indigo-600 transition-colors">{{ $activity['document_title'] }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                        <span class="font-medium">{{ $activity['sender_name'] }}</span>
                                        <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        <span class="font-medium">{{ $activity['recipient_name'] }}</span>
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold
                                        {{ $activity['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($activity['status'] === 'rejected' ? 'bg-rose-100 text-rose-700' : ($activity['status'] === 'received' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100/50 text-slate-700')) }}">
                                        {{ ucfirst($activity['status']) }}
                                    </span>
                                    <p class="text-xs text-slate-400 mt-1 font-medium">{{ $activity['time_ago'] }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-12 text-center">
                            <div class="w-12 h-12 rounded-xl bg-slate-100/50 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-sm font-medium text-slate-500">No recent activity</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- DOCUMENT VOLUME TRENDS --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-sm font-bold text-slate-900">Volume Trends</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Activity over time</p>
                </div>
                <div class="p-5">
                    <canvas id="documentTrendsChart" height="100"></canvas>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- CATEGORY & STATUS CHARTS --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="text-sm font-bold text-slate-900">Categories</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Distribution by type</p>
                    </div>
                    <div class="p-5 flex items-center justify-center" style="min-height: 300px">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="text-sm font-bold text-slate-900">Status Overview</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Current states</p>
                    </div>
                    <div class="p-5 flex items-center justify-center" style="min-height: 300px">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- WORKFLOW BOTTLENECKS --}}
            {{-- ============================================= --}}
            @if($workflowBottlenecks['total_pending'] > 0)
            <div class="bg-white rounded-xl border border-amber-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-white flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Bottlenecks</h3>
                        <p class="text-xs text-amber-700 font-medium">{{ number_format($workflowBottlenecks['total_pending']) }} pending workflows</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
                    {{-- Pending by User --}}
                    <div class="p-5">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">By User</h4>
                        <div class="space-y-2.5">
                            @foreach($workflowBottlenecks['pending_by_user']->take(5) as $item)
                            <div class="flex items-center justify-between group hover:bg-slate-50 -mx-2 px-2 py-1.5 rounded-lg transition-colors">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $item['user']->first_name ?? 'Unknown' }} {{ $item['user']->last_name ?? '' }}</p>
                                    <p class="text-xs text-slate-500 font-medium">{{ $item['avg_wait_formatted'] }} avg</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 group-hover:bg-amber-200 transition-colors">
                                    {{ $item['pending_count'] }}
                                </span>
                            </div>
                            @endforeach
                            @if($workflowBottlenecks['pending_by_user']->isEmpty())
                            <p class="text-sm text-slate-400 font-medium text-center py-4">No pending items</p>
                            @endif
                        </div>
                    </div>
                    {{-- Pending by Office --}}
                    <div class="p-5">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">By Office</h4>
                        <div class="space-y-2.5">
                            @foreach($workflowBottlenecks['pending_by_office']->take(5) as $item)
                            <div class="flex items-center justify-between group hover:bg-slate-50 -mx-2 px-2 py-1.5 rounded-lg transition-colors">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $item['office']->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500 font-medium">{{ $item['avg_wait_formatted'] }} avg</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 group-hover:bg-amber-200 transition-colors">
                                    {{ $item['pending_count'] }}
                                </span>
                            </div>
                            @endforeach
                            @if($workflowBottlenecks['pending_by_office']->isEmpty())
                            <p class="text-sm text-slate-400 font-medium text-center py-4">No pending items</p>
                            @endif
                        </div>
                    </div>
                    {{-- Oldest Pending --}}
                    <div class="p-5">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Longest Waiting</h4>
                        <div class="space-y-2.5">
                            @foreach($workflowBottlenecks['oldest_pending'] as $item)
                            <div class="flex items-center justify-between group hover:bg-slate-50 -mx-2 px-2 py-1.5 rounded-lg transition-colors">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $item['document']->title ?? 'Document' }}</p>
                                    <p class="text-xs text-slate-500 font-medium">{{ $item['created_at'] }}</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $item['waiting_hours'] > 168 ? 'bg-rose-100 text-rose-700' : ($item['waiting_hours'] > 48 ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700') }}">
                                    {{ $item['waiting_formatted'] }}
                                </span>
                            </div>
                            @endforeach
                            @if(count($workflowBottlenecks['oldest_pending']) === 0)
                            <p class="text-sm text-slate-400 font-medium text-center py-4">No pending items</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{-- USER PERFORMANCE TABLE --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">User Performance</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Individual metrics</p>
                    </div>
                    <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'user_performance']) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-xs font-semibold transition-all duration-200">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export
                    </a>
                </div>
                <div class="overflow-x-auto" style="min-height: 200px; max-height: 480px; overflow-y: auto;">
                    <table class="w-full">
                        <thead class="sticky top-0 z-10 bg-slate-50">
                            <tr class="border-b border-slate-100">
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Uploads</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Forwarded</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Processed</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Avg Response</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Avg Processing</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Approval</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider" style="min-width: 140px;">Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($userPerformanceMetrics as $metric)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ strtoupper(substr($metric['user']->first_name, 0, 1)) }}{{ strtoupper(substr($metric['user']->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $metric['user']->first_name }} {{ $metric['user']->last_name }}</p>
                                            <p class="text-xs text-slate-500 font-medium">{{ number_format($metric['total_documents_handled']) }} handled</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['uploads_count']) }}</td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['forwarded_count']) }}</td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['processed_count']) }}</td>
                                <td class="px-5 py-3.5 text-center text-xs font-medium text-slate-600 tabular-nums">{{ $metric['avg_response_time'] }}</td>
                                <td class="px-5 py-3.5 text-center text-xs font-medium text-slate-600 tabular-nums">{{ $metric['avg_processing_time'] }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $metric['approval_rate'] >= 80 ? 'bg-emerald-100 text-emerald-700' : ($metric['approval_rate'] >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                        {{ $metric['approval_rate'] }}%
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex-1 bg-slate-100/50 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-2.5 rounded-full transition-all duration-700 ease-out {{ $metric['performance_score'] >= 70 ? 'bg-emerald-100/500' : ($metric['performance_score'] >= 40 ? 'bg-amber-100/500' : 'bg-rose-500') }}"
                                                 style="width: {{ $metric['performance_score'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-900 w-8 text-right tabular-nums">{{ $metric['performance_score'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100/50 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">No performance data</p>
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
            <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Office Performance</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Team efficiency</p>
                    </div>
                    <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'office_performance']) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-xs font-semibold transition-all duration-200">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export
                    </a>
                </div>
                <div class="overflow-x-auto" style="min-height: 200px; max-height: 480px; overflow-y: auto;">
                    <table class="w-full">
                        <thead class="sticky top-0 z-10 bg-slate-50">
                            <tr class="border-b border-slate-100">
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Office</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Members</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Originated</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Received</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Processed</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Avg Processing</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider" style="min-width: 140px;">Efficiency</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($officePerformanceMetrics as $metric)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ strtoupper(substr($metric['office']->name, 0, 2)) }}
                                        </div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $metric['office']->name }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['user_count']) }}</td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['documents_originated']) }}</td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['documents_received']) }}</td>
                                <td class="px-5 py-3.5 text-center text-sm font-semibold text-slate-700 tabular-nums">{{ number_format($metric['workflows_processed']) }}</td>
                                <td class="px-5 py-3.5 text-center text-xs font-medium text-slate-600 tabular-nums">{{ $metric['avg_processing_time'] }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex-1 bg-slate-100/50 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-2.5 rounded-full transition-all duration-700 ease-out {{ $metric['efficiency_score'] >= 70 ? 'bg-emerald-100/500' : ($metric['efficiency_score'] >= 40 ? 'bg-amber-100/500' : 'bg-rose-500') }}"
                                                 style="width: {{ $metric['efficiency_score'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-900 w-8 text-right tabular-nums">{{ $metric['efficiency_score'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100/50 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">No office data</p>
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- User Storage --}}
                <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Storage by User</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Disk usage per member</p>
                        </div>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'user_storage']) }}" 
                           class="inline-flex items-center px-2.5 py-1 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-xs font-semibold transition-all duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                            Export
                        </a>
                    </div>
                    <div class="p-5">
                        <canvas id="userStorageChart" height="200"></canvas>
                    </div>
                    <div class="border-t border-slate-100">
                        <div class="max-h-48 overflow-y-auto">
                            <table class="w-full">
                                <thead class="sticky top-0 bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-2.5 text-left text-xs font-bold text-slate-500">User</th>
                                        <th class="px-5 py-2.5 text-right text-xs font-bold text-slate-500">Docs</th>
                                        <th class="px-5 py-2.5 text-right text-xs font-bold text-slate-500">Size</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($storageMetrics['user_storage'] as $userStorage)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-5 py-2.5 text-sm font-medium text-slate-700">{{ $userStorage['user']->first_name }} {{ $userStorage['user']->last_name }}</td>
                                        <td class="px-5 py-2.5 text-sm text-slate-500 text-right tabular-nums">{{ number_format($userStorage['count']) }}</td>
                                        <td class="px-5 py-2.5 text-sm font-semibold text-slate-900 text-right tabular-nums">{{ $userStorage['formatted_size'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Office Storage --}}
                <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Storage by Office</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Disk usage per team</p>
                        </div>
                        <a href="{{ route('reports.company-dashboard', ['start_date' => $startDate, 'end_date' => $endDate, 'export_table' => 'office_storage']) }}" 
                           class="inline-flex items-center px-2.5 py-1 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-xs font-semibold transition-all duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                            Export
                        </a>
                    </div>
                    <div class="p-5">
                        <canvas id="officeStorageChart" height="200"></canvas>
                    </div>
                    <div class="border-t border-slate-100">
                        <div class="max-h-48 overflow-y-auto">
                            <table class="w-full">
                                <thead class="sticky top-0 bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-2.5 text-left text-xs font-bold text-slate-500">Office</th>
                                        <th class="px-5 py-2.5 text-right text-xs font-bold text-slate-500">Docs</th>
                                        <th class="px-5 py-2.5 text-right text-xs font-bold text-slate-500">Size</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($storageMetrics['office_storage'] as $officeStorage)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-5 py-2.5 text-sm font-medium text-slate-700">{{ $officeStorage['office']->name }}</td>
                                        <td class="px-5 py-2.5 text-sm text-slate-500 text-right tabular-nums">{{ number_format($officeStorage['count']) }}</td>
                                        <td class="px-5 py-2.5 text-sm font-semibold text-slate-900 text-right tabular-nums">{{ $officeStorage['formatted_size'] }}</td>
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
            <div class="bg-white rounded-xl border border-slate-200/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Smart Insights</h3>
                        <p class="text-xs text-slate-600 font-medium">AI-powered recommendations</p>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php $insightCount = 0; @endphp

                    {{-- Bottleneck Warning --}}
                    @if($documentAging['critical_count'] > 0)
                    @php $insightCount++; @endphp
                    <div class="group flex gap-3 p-4 rounded-xl bg-gradient-to-br from-rose-50 to-rose-50/50 border border-rose-200 hover:border-rose-300 hover:shadow-md hover:shadow-rose-500/10 transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-rose-900">Overdue Documents</p>
                            <p class="text-sm text-rose-700 mt-1.5 leading-relaxed">{{ $documentAging['critical_count'] }} workflows pending 7+ days. Consider following up with responsible parties.</p>
                        </div>
                    </div>
                    @endif

                    {{-- Low Completion Rate --}}
                    @if($workflowCompletion['completion_rate'] < 60 && $workflowCompletion['total'] > 5)
                    @php $insightCount++; @endphp
                    <div class="group flex gap-3 p-4 rounded-xl bg-gradient-to-br from-amber-50 to-amber-50/50 border border-amber-200 hover:border-amber-300 hover:shadow-md hover:shadow-amber-500/10 transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-amber-900">Low Completion Rate</p>
                            <p class="text-sm text-amber-700 mt-1.5 leading-relaxed">Only {{ $workflowCompletion['completion_rate'] }}% completed. Review bottlenecks to identify stalling points.</p>
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
                    <div class="group flex gap-3 p-4 rounded-xl bg-gradient-to-br from-amber-50 to-amber-50/50 border border-amber-200 hover:border-amber-300 hover:shadow-md hover:shadow-amber-500/10 transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-amber-900">Performance Opportunity</p>
                            <p class="text-sm text-amber-700 mt-1.5 leading-relaxed">{{ $slowestUser['user']->first_name }} {{ $slowestUser['user']->last_name }} (score: {{ $slowestUser['performance_score'] }}). Consider training or workload review.</p>
                        </div>
                    </div>
                    @endif

                    @if($fastestUser && $fastestUser['performance_score'] > 80)
                    @php $insightCount++; @endphp
                    <div class="group flex gap-3 p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-50/50 border border-emerald-200 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/10 transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-emerald-900">Top Performer</p>
                            <p class="text-sm text-emerald-700 mt-1.5 leading-relaxed">{{ $fastestUser['user']->first_name }} {{ $fastestUser['user']->last_name }} (score: {{ $fastestUser['performance_score'] }}). Share best practices with team.</p>
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
                    <div class="group flex gap-3 p-4 rounded-xl bg-gradient-to-br {{ $percentChange > 0 ? 'from-indigo-50 to-indigo-50/50 border-indigo-200 hover:border-indigo-300 hover:shadow-indigo-500/10' : 'from-amber-50 to-amber-50/50 border-amber-200 hover:border-amber-300 hover:shadow-amber-500/10' }} border hover:shadow-md transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br {{ $percentChange > 0 ? 'from-indigo-500 to-indigo-600 shadow-indigo-500/30' : 'from-amber-500 to-amber-600 shadow-amber-500/30' }} flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold {{ $percentChange > 0 ? 'text-indigo-900' : 'text-amber-900' }}">Volume {{ $percentChange > 0 ? 'Spike' : 'Drop' }}</p>
                            <p class="text-sm {{ $percentChange > 0 ? 'text-indigo-700' : 'text-amber-700' }} mt-1.5 leading-relaxed">
                                {{ abs($percentChange) }}% {{ $percentChange > 0 ? 'increase' : 'decrease' }} vs previous month.
                                {{ $percentChange > 0 ? 'Ensure capacity.' : 'Investigate cause.' }}
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
                    <div class="group flex gap-3 p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-50/50 border border-purple-200 hover:border-purple-300 hover:shadow-md hover:shadow-purple-500/10 transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-purple-900">Storage Alert</p>
                            <p class="text-sm text-purple-700 mt-1.5 leading-relaxed">{{ $storageInsightUser['user']->first_name }} {{ $storageInsightUser['user']->last_name }} uses {{ round(($storageInsightUser['size'] / $totalStorage) * 100) }}% ({{ $storageInsightUser['formatted_size'] }}). Review practices.</p>
                        </div>
                    </div>
                    @endif

                    @if($insightCount === 0)
                    <div class="col-span-2 group flex gap-3 p-5 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-50/50 border border-emerald-200 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/10 transition-all duration-300">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-emerald-900">All Systems Healthy</p>
                            <p class="text-sm text-emerald-700 mt-1.5 leading-relaxed">All metrics within optimal ranges. Excellent performance across the board!</p>
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
    Chart.defaults.font.family = "'Inter var', 'Inter', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 14;
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
    Chart.defaults.plugins.legend.labels.boxHeight = 8;

    const palette = {
        blue: { bg: 'rgba(99,102,241,0.1)', border: 'rgba(99,102,241,0.8)' },
        red: { bg: 'rgba(244,63,94,0.1)', border: 'rgba(244,63,94,0.8)' },
        green: { bg: 'rgba(16,185,129,0.1)', border: 'rgba(16,185,129,0.8)' },
        amber: { bg: 'rgba(245,158,11,0.1)', border: 'rgba(245,158,11,0.8)' },
        indigo: { bg: 'rgba(99,102,241,0.1)', border: 'rgba(99,102,241,0.8)' },
        purple: { bg: 'rgba(168,85,247,0.1)', border: 'rgba(168,85,247,0.8)' },
    };
    const chartColors = ['#6366F1','#10B981','#F59E0B','#F43F5E','#A855F7','#EC4899','#14B8A6','#F97316','#3B82F6','#06B6D4'];

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
                    cutout: '70%',
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                boxWidth: 8, 
                                boxHeight: 8,
                                padding: 12, 
                                font: { size: 11, weight: '600' },
                                color: '#475569'
                            } 
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            cornerRadius: 8,
                            padding: 12,
                            displayColors: false,
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
            blueGrad.addColorStop(0, 'rgba(99,102,241,0.2)');
            blueGrad.addColorStop(1, 'rgba(99,102,241,0.01)');

            var violetGrad = trendsCtx.createLinearGradient(0, 0, 0, 300);
            violetGrad.addColorStop(0, 'rgba(139,92,246,0.15)');
            violetGrad.addColorStop(1, 'rgba(139,92,246,0.01)');

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: trendsData.months,
                    datasets: [
                        {
                            label: 'Documents Created',
                            data: trendsData.document_counts,
                            backgroundColor: blueGrad,
                            borderColor: '#6366F1',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#6366F1',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        },
                        {
                            label: 'Workflows Created',
                            data: trendsData.workflow_counts,
                            backgroundColor: violetGrad,
                            borderColor: '#8B5CF6',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#8B5CF6',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { 
                            position: 'top',
                            align: 'start',
                            labels: {
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 16,
                                font: { size: 12, weight: '600' },
                                color: '#475569'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            cornerRadius: 10,
                            padding: 14,
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            boxPadding: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { 
                                color: 'rgba(0,0,0,0.03)',
                                drawBorder: false
                            },
                            ticks: { 
                                color: '#94a3b8',
                                font: { size: 11, weight: '500' },
                                padding: 8
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: '#94a3b8',
                                font: { size: 11, weight: '500' },
                                padding: 8
                            },
                            border: { display: false }
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
                    cutout: '65%',
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                boxWidth: 8, 
                                boxHeight: 8,
                                padding: 12, 
                                font: { size: 11, weight: '600' },
                                color: '#475569',
                                usePointStyle: true
                            } 
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            cornerRadius: 10,
                            padding: 14,
                            displayColors: false
                        }
                    }
                }
            });
        } else {
            catCanvas.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full"><div class="w-12 h-12 rounded-xl bg-slate-100/50 flex items-center justify-center mb-3"><svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div><p class="text-sm font-medium text-slate-500">No category data</p></div>';
        }
    } catch(e) { console.error('Categories chart error:', e); }

    // ===== Status Doughnut =====
    try {
        var statusData = @json($statusDistribution ?? []);
        var statusCanvas = document.getElementById('statusChart');
        var statusColors = { 'approved': '#10B981', 'pending': '#F59E0B', 'rejected': '#F43F5E', 'received': '#6366F1', 'forwarded': '#A855F7', 'released': '#14B8A6' };
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
                    cutout: '65%',
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                boxWidth: 8, 
                                boxHeight: 8,
                                padding: 12, 
                                font: { size: 11, weight: '600' },
                                color: '#475569',
                                usePointStyle: true
                            } 
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            cornerRadius: 10,
                            padding: 14,
                            displayColors: false
                        }
                    }
                }
            });
        } else {
            statusCanvas.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full"><div class="w-12 h-12 rounded-xl bg-slate-100/50 flex items-center justify-center mb-3"><svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg></div><p class="text-sm font-medium text-slate-500">No status data</p></div>';
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
                            grid: { 
                                color: 'rgba(0,0,0,0.03)',
                                drawBorder: false
                            },
                            ticks: { 
                                callback: v => formatBytes(v, 1), 
                                color: '#94a3b8',
                                font: { size: 11, weight: '500' },
                                padding: 8
                            },
                            border: { display: false }
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { 
                                color: '#94a3b8', 
                                font: { size: 10, weight: '500' },
                                padding: 8
                            },
                            border: { display: false }
                        }
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
                            grid: { 
                                color: 'rgba(0,0,0,0.03)',
                                drawBorder: false
                            },
                            ticks: { 
                                callback: v => formatBytes(v, 1), 
                                color: '#94a3b8',
                                font: { size: 11, weight: '500' },
                                padding: 8
                            },
                            border: { display: false }
                        },
                        x: { 
                            grid: { display: false }, 
                            ticks: { 
                                color: '#94a3b8', 
                                font: { size: 10, weight: '500' },
                                padding: 8
                            },
                            border: { display: false }
                        }
                    }
                }
            });
        }
    } catch(e) { console.error('Office storage chart error:', e); }
});
</script>
