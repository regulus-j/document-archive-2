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
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Main content -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach ([
                            [
                                'title' => 'Documents Received',
                                'value' => $totalDocuments ?? 0,
                                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'accent' => 'border-l-indigo-400',
                                'iconBg' => 'bg-indigo-50',
                                'iconColor' => 'text-indigo-500'
                            ],
                            [
                                'title' => 'Pending Documents',
                                'value' => $pendingDocuments ?? 0,
                                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                                'accent' => 'border-l-amber-400',
                                'iconBg' => 'bg-amber-50',
                                'iconColor' => 'text-amber-500'
                            ],
                            [
                                'title' => 'Processed Documents',
                                'value' => $countRecentDocs ?? 0,
                                'icon' => 'M5 13l4 4L19 7',
                                'accent' => 'border-l-emerald-400',
                                'iconBg' => 'bg-emerald-50',
                                'iconColor' => 'text-emerald-500'
                            ],
                            [
                                'title' => 'Documents Today',
                                'value' => $todayDocuments ?? 0,
                                'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                'accent' => 'border-l-slate-400',
                                'iconBg' => 'bg-slate-100',
                                'iconColor' => 'text-slate-500'
                            ]
                        ] as $stat)
                            <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] {{ $stat['accent'] }} shadow-card">
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

                    <!-- Document Trend Chart -->
                    <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Documents Trend</h3>
                                <p class="text-xs text-slate-500">Your activity over the last week.</p>
                            </div>
                            <div class="text-sm font-semibold text-slate-900">{{ array_sum($documentTrendCounts ?? []) }}</div>
                        </div>
                        @php
                            $trendCount = count($documentTrendCounts ?? []);
                            $trendMax = max($documentTrendCounts ?? [1]) ?: 1;
                            $trendPoints = collect($documentTrendCounts ?? [])->values()->map(function ($value, $index) use ($trendCount, $trendMax) {
                                $x = $trendCount > 1 ? ($index / ($trendCount - 1)) * 100 : 0;
                                $y = 100 - (($value / $trendMax) * 70 + 15);
                                return $x . ',' . $y;
                            })->implode(' ');
                        @endphp
                        <div class="h-40 w-full">
                            <svg viewBox="0 0 100 100" class="h-full w-full">
                                <defs>
                                    <linearGradient id="trendFillUser" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <polyline fill="url(#trendFillUser)" stroke="none" points="{{ $trendPoints }} 100,100 0,100" />
                                <polyline fill="none" stroke="#6366f1" stroke-width="2" points="{{ $trendPoints }}" />
                            </svg>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-400 mt-2">
                            @foreach($documentTrendLabels ?? [] as $label)
                                <span>{{ $label }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Office Lead Section (Conditional) -->
                    @if(isset($isOfficeLead) && $isOfficeLead && isset($ledOffice) && $ledOffice)
                    <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-slate-900">{{ $ledOffice->name }} Team Snapshot</h3>
                            <span class="text-xs text-slate-400">Office lead view</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-indigo-400 shadow-card">
                                <div class="flex flex-col items-center text-center">
                                    <div class="bg-indigo-50 rounded-lg p-3 mb-3">
                                        <svg class="h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Documents</div>
                                        <div class="text-2xl font-bold text-slate-900">{{ $officeDocumentCount ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-emerald-400 shadow-card">
                                <div class="flex flex-col items-center text-center">
                                    <div class="bg-emerald-50 rounded-lg p-3 mb-3">
                                        <svg class="h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Documents Today</div>
                                        <div class="text-2xl font-bold text-slate-900">{{ $officeDocumentsTodayCount ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-amber-400 shadow-card">
                                <div class="flex flex-col items-center text-center">
                                    <div class="bg-amber-50 rounded-lg p-3 mb-3">
                                        <svg class="h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Pending Workflows</div>
                                        <div class="text-2xl font-bold text-slate-900">{{ $officePendingWorkflowsCount ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200/80 border-l-[3px] border-l-slate-400 shadow-card">
                                <div class="flex flex-col items-center text-center">
                                    <div class="bg-slate-100 rounded-lg p-3 mb-3">
                                        <svg class="h-6 w-6 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Team Members</div>
                                        <div class="text-2xl font-bold text-slate-900">{{ $officeMembers->count() ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(isset($officeDocuments) && $officeDocuments->count() > 0)
                        <div>
                            <h4 class="text-base font-medium text-slate-700 mb-3">Recent Team Documents</h4>
                            <div class="rounded-lg border border-slate-200/80 overflow-hidden">
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
                                            <td class="px-4 py-3">
                                                <a href="{{ route('documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                                    {{ Str::limit($document->title, 30) }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $document->user ? $document->user->first_name . ' ' . $document->user->last_name : 'Unknown' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $document->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @forelse($document->categories as $category)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-medium rounded-full bg-slate-100 text-slate-700">
                                                            {{ $category->category ?? 'Unnamed Category' }}
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

                    <!-- Recent Documents -->
                    <div class="bg-white rounded-lg border border-slate-200/80 shadow-card overflow-hidden">
                        <div class="flex items-center justify-between p-6 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Recent Documents</h3>
                                <p class="text-xs text-slate-500">Latest items relevant to you.</p>
                            </div>
                            <a href="{{ route('documents.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">View all</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Document</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Updated</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @forelse($dashboardRecentDocuments ?? [] as $document)
                                        @php
                                            $statusValue = $document->effective_status ?? optional($document->status)->status ?? 'unknown';
                                            $statusClass = match ($statusValue) {
                                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'returned' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('documents.show', $document) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                                    {{ Str::limit($document->title, 40) }}
                                                </a>
                                                <div class="text-xs text-slate-400">{{ $document->classification ?? 'Unclassified' }}</div>
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
                                            <td colspan="3" class="px-6 py-6 text-center text-sm text-slate-500">No recent documents yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                        <h3 class="text-sm font-semibold text-slate-700 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('documents.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span class="text-xs font-medium text-slate-700">Browse Docs</span>
                            </a>
                            <a href="{{ route('documents.workflow-dashboard') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span class="text-xs font-medium text-slate-700">Receive Docs</span>
                            </a>
                            <a href="{{ route('documents.workflows') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span class="text-xs font-medium text-slate-700">My Workflows</span>
                            </a>
                            <a href="{{ route('documents.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 transition">
                                <svg class="w-5 h-5 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span class="text-xs font-medium text-slate-700">New Doc</span>
                            </a>
                        </div>
                    </div>

                    <!-- Workflow Status -->
                    <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-slate-700">Workflow Status</h3>
                            <span class="text-xs text-slate-400">Current</span>
                        </div>
                        @php $statusMax = isset($workflowStatusCounts) ? $workflowStatusCounts->max() : 1; $statusMax = $statusMax ?: 1; @endphp
                        <div class="space-y-3">
                            @forelse($workflowStatusCounts ?? [] as $status => $count)
                                @php
                                    $label = ucfirst(str_replace('_', ' ', $status));
                                    $barColor = match ($status) {
                                        'approved' => 'bg-emerald-500',
                                        'rejected' => 'bg-rose-500',
                                        'pending' => 'bg-amber-500',
                                        default => 'bg-slate-400',
                                    };
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between text-xs text-slate-600">
                                        <span class="font-medium text-slate-700">{{ $label }}</span>
                                        <span>{{ $count }}</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full mt-1">
                                        <div class="h-2 {{ $barColor }} rounded-full" style="width: {{ ($count / $statusMax) * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-slate-500">No workflow activity yet.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Categories -->
                    <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-slate-700">Top Categories</h3>
                            <span class="text-xs text-slate-400">Recent</span>
                        </div>
                        @php
                            $categoryCounts = isset($dashboardRecentDocuments)
                                ? $dashboardRecentDocuments
                                    ->flatMap(function ($document) {
                                        return $document->categories->pluck('category');
                                    })
                                    ->filter()
                                    ->countBy()
                                    ->sortDesc()
                                    ->take(5)
                                : collect([]);
                            $categoryMax = $categoryCounts->max() ?: 1;
                        @endphp
                        <div class="space-y-3">
                            @forelse($categoryCounts as $category => $count)
                                <div>
                                    <div class="flex items-center justify-between text-xs text-slate-600">
                                        <span class="font-medium text-slate-700">{{ $category }}</span>
                                        <span>{{ $count }}</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full mt-1">
                                        <div class="h-2 bg-indigo-500 rounded-full" style="width: {{ ($count / $categoryMax) * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-slate-500">No categories yet.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Find Document / Barcode Scanner -->
                    <div class="bg-white rounded-lg p-6 border border-slate-200/80 shadow-card">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Find Document</h3>
                        <p class="text-xs text-slate-500 mb-4">Search by tracking number or scan a barcode.</p>
                        <form id="ou-quick-action-form" action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="action" value="find">

                            <input type="text" name="tracking_number" id="ou-tracking-number"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-slate-900 placeholder:text-slate-400"
                                placeholder="Enter tracking number (e.g. ADM-20250101-000001)">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <label title="Upload barcode image" class="inline-flex items-center justify-center px-3 py-2.5 border border-slate-300 text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Upload
                                    <input type="file" id="ou-qr-image-input" accept="image/*" class="hidden" onchange="ouDecodeQrFromImage(this)">
                                </label>
                                <button type="button" onclick="ouToggleScanner()" class="inline-flex items-center justify-center px-3 py-2.5 border border-slate-300 text-xs font-medium text-slate-600 bg-white hover:bg-slate-50 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span id="ou-scanner-btn-text">Scan</span>
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2.5 border border-indigo-600 text-xs font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Find
                                </button>
                            </div>

                            <div id="ou-qr-decode-status" class="hidden"></div>

                            <div id="ou-reader-wrapper" class="hidden">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs text-slate-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                        Camera active — point at a barcode
                                    </p>
                                    <button type="button" onclick="ouStopScanner()" class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Close
                                    </button>
                                </div>
                                <div id="ou-reader" class="rounded-lg overflow-hidden shadow-lg border border-indigo-100"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let ouHtml5QrCode = null;
        let ouScannerActive = false;

        function ouToggleScanner() {
            const wrapper = document.getElementById('ou-reader-wrapper');
            const btn = document.getElementById('ou-scanner-btn-text');
            if (!ouScannerActive) {
                ouStartScanner();
                wrapper.classList.remove('hidden');
                btn.textContent = 'Stop';
            } else {
                ouStopScanner();
                wrapper.classList.add('hidden');
                btn.textContent = 'Scan';
            }
        }

        function ouStartScanner() {
            if (!ouHtml5QrCode) {
                ouHtml5QrCode = new Html5Qrcode('ou-reader');
            }
            ouHtml5QrCode.start(
                { facingMode: 'environment' },
                { fps: 30, qrbox: 250 },
                (decodedText) => {
                    document.getElementById('ou-tracking-number').value = decodedText;
                    ouStopScanner();
                },
                console.log
            ).catch(err => console.error('Failed to start camera:', err));
            ouScannerActive = true;
        }

        function ouStopScanner() {
            if (ouHtml5QrCode && ouScannerActive) {
                ouHtml5QrCode.stop().then(() => {
                    ouScannerActive = false;
                }).catch(console.error);
            }
        }

        function ouDecodeQrFromImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    Html5Qrcode.scanFile(e.target.result, true)
                        .then(decodedText => {
                            document.getElementById('ou-tracking-number').value = decodedText;
                        })
                        .catch(err => {
                            document.getElementById('ou-qr-decode-status').textContent = 'Failed to decode image';
                            document.getElementById('ou-qr-decode-status').classList.remove('hidden');
                        });
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-app-layout>
