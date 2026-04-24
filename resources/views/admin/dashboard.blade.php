<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Super Admin Dashboard
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Site-wide overview &middot; {{ now()->format('F d, Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Super Admin
                </span>
                <a href="{{ route('admin.dashboard.export-pdf') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF Report
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{ activeTab: 'overview' }" class="bg-gradient-to-b from-slate-50 to-white min-h-screen pb-12">

        {{-- Tab Navigation --}}
        <div class="bg-white border-b border-slate-200 shadow-sm">
            <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex gap-1 overflow-x-auto py-0">
                    @foreach(['overview' => 'Overview', 'users' => 'Users', 'documents' => 'Documents', 'companies' => 'Companies', 'subscriptions' => 'Subscriptions', 'audit' => 'Audit'] as $tab => $label)
                    <button @click="activeTab = '{{ $tab }}'"
                            :class="activeTab === '{{ $tab }}' ? 'border-indigo-600 text-indigo-600 bg-indigo-100/50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            class="px-4 py-2.5 text-sm font-medium border-b-2 rounded-t-lg transition-colors whitespace-nowrap">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 mt-6">

            @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
            @endif

            {{-- ━━━ TAB: Overview ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div x-show="activeTab === 'overview'" x-cloak>

                {{-- KPI Row --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Revenue</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">P{{ number_format($totalRevenue, 2) }}</div>
                        <div class="text-xs text-slate-400 mt-1">Active subscriptions</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">MRR</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">P{{ number_format($mrr, 2) }}</div>
                        <div class="text-xs text-slate-400 mt-1">Monthly recurring</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Companies</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalCompanies) }}</div>
                        <div class="text-xs mt-1 {{ $companyGrowth >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $companyGrowth >= 0 ? '+' : '' }}{{ $companyGrowth }}% vs last mo</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Users</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalUsers) }}</div>
                        <div class="text-xs mt-1 {{ $userGrowth >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $userGrowth >= 0 ? '+' : '' }}{{ $userGrowth }}% vs last mo</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Documents</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalDocuments) }}</div>
                        <div class="text-xs mt-1 {{ $documentGrowth >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $documentGrowth >= 0 ? '+' : '' }}{{ $documentGrowth }}% vs last mo</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition">
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Active Subs</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($activeSubscriptions) }}</div>
                        <div class="text-xs mt-1 {{ $subscriptionGrowth >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $subscriptionGrowth >= 0 ? '+' : '' }}{{ $subscriptionGrowth }}% vs last mo</div>
                    </div>
                </div>

                {{-- Today's Snapshot --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-indigo-600 text-white rounded-xl p-4 flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                        <div><div class="text-2xl font-bold">{{ $newUsersToday }}</div><div class="text-indigo-200 text-sm">New users today</div></div>
                    </div>
                    <div class="bg-emerald-600 text-white rounded-xl p-4 flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <div><div class="text-2xl font-bold">{{ $newDocsToday }}</div><div class="text-emerald-200 text-sm">Documents today</div></div>
                    </div>
                    <div class="bg-amber-600 text-white rounded-xl p-4 flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                        <div><div class="text-2xl font-bold">{{ $newCompaniesToday }}</div><div class="text-amber-200 text-sm">New companies today</div></div>
                    </div>
                </div>

                {{-- Charts Row 1 --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Users & Companies Growth (12 mo)</h3>
                        <div style="height:250px"><canvas id="growthChart"></canvas></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Document Uploads (12 mo)</h3>
                        <div style="height:250px"><canvas id="docsChart"></canvas></div>
                    </div>
                </div>

                {{-- Charts Row 2 --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Subscription Plans</h3>
                        <div style="height:250px"><canvas id="planChart"></canvas></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Document Statuses</h3>
                        <div style="height:250px"><canvas id="statusChart"></canvas></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Revenue Trend (12 mo)</h3>
                        <div style="height:250px"><canvas id="revenueChart"></canvas></div>
                    </div>
                </div>

                {{-- Top Companies --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-slate-700">Top Companies by Users</h3>
                            <a href="{{ route('admin.dashboard.export-excel') }}?type=companies" class="text-xs text-indigo-600 hover:underline">Export</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase"><th class="px-5 py-2">Company</th><th class="px-5 py-2 text-right">Users</th></tr></thead>
                                <tbody>
                                    @forelse($topCompaniesByUsers as $c)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50"><td class="px-5 py-2.5 font-medium text-slate-800">{{ $c->company_name }}</td><td class="px-5 py-2.5 text-right text-slate-600">{{ $c->employees_count }}</td></tr>
                                    @empty
                                    <tr><td colspan="2" class="px-5 py-4 text-center text-slate-400">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Top Companies by Documents</h3></div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase"><th class="px-5 py-2">Company</th><th class="px-5 py-2 text-right">Documents</th></tr></thead>
                                <tbody>
                                    @forelse($topCompaniesByDocs as $c)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50"><td class="px-5 py-2.5 font-medium text-slate-800">{{ $c->company_name }}</td><td class="px-5 py-2.5 text-right text-slate-600">{{ $c->documents_count }}</td></tr>
                                    @empty
                                    <tr><td colspan="2" class="px-5 py-4 text-center text-slate-400">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Recent Activities + Expiring Subscriptions --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Recent Activity</h3></div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase"><th class="px-5 py-2">Action</th><th class="px-5 py-2">User</th><th class="px-5 py-2">Company</th><th class="px-5 py-2">Date</th><th class="px-5 py-2"></th></tr></thead>
                                <tbody>
                                    @foreach($recentActivities as $a)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                                        <td class="px-5 py-2.5"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $a['type'] === 'document' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800' }}">{{ $a['action'] }}</span></td>
                                        <td class="px-5 py-2.5 text-slate-700">{{ $a['user_name'] }}</td>
                                        <td class="px-5 py-2.5 text-slate-500">{{ $a['company_name'] }}</td>
                                        <td class="px-5 py-2.5 text-slate-400 text-xs">{{ \Carbon\Carbon::parse($a['created_at'])->diffForHumans() }}</td>
                                        <td class="px-5 py-2.5">
                                            @if($a['type'] === 'document')
                                                <a href="{{ route('documents.show', $a['id']) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                            @else
                                                <a href="{{ route('users.show', $a['id']) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Expiring Soon (30d)</h3></div>
                        <div class="divide-y divide-slate-100">
                            @forelse($expiringSubscriptions as $sub)
                            <div class="px-5 py-3">
                                <div class="font-medium text-slate-800 text-sm">{{ $sub->company->company_name ?? 'N/A' }}</div>
                                <div class="flex justify-between mt-1 text-xs">
                                    <span class="text-slate-500">{{ $sub->plan->plan_name ?? 'N/A' }}</span>
                                    <span class="text-red-600 font-medium">{{ \Carbon\Carbon::parse($sub->end_date)->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="px-5 py-6 text-center text-slate-400 text-sm">No expiring subscriptions</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- ━━━ TAB: Users ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div x-show="activeTab === 'users'" x-cloak>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-700">All Users ({{ $totalUsers }})</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dashboard.export-excel') }}?type=users" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">Add User</a>
                            <a href="{{ route('admin.users-index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Manage Users</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">#</th><th class="px-5 py-2">Name</th><th class="px-5 py-2">Email</th><th class="px-5 py-2">Company</th><th class="px-5 py-2">Roles</th><th class="px-5 py-2">Joined</th><th class="px-5 py-2">Actions</th>
                            </tr></thead>
                            <tbody>
                                @foreach($usersTable as $user)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5 text-slate-400">{{ $user->id }}</td>
                                    <td class="px-5 py-2.5 font-medium text-slate-800">{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td class="px-5 py-2.5 text-slate-600">{{ $user->email }}</td>
                                    <td class="px-5 py-2.5 text-slate-500">{{ $user->companies->pluck('company_name')->join(', ') ?: '-' }}</td>
                                    <td class="px-5 py-2.5">
                                        @foreach($user->roles as $role)
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700 mr-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-5 py-2.5 text-slate-400 text-xs">{{ $user->created_at?->format('M d, Y') }}</td>
                                    <td class="px-5 py-2.5">
                                        <div class="flex gap-1">
                                            <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-amber-600 hover:underline text-xs ml-2">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($usersTable->hasPages())
                    <div class="px-5 py-3 border-t border-slate-100">{{ $usersTable->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ━━━ TAB: Documents ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div x-show="activeTab === 'documents'" x-cloak>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-700">All Documents ({{ $totalDocuments }})</h3>
                        <a href="{{ route('admin.dashboard.export-excel') }}?type=documents" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">#</th><th class="px-5 py-2">Title</th><th class="px-5 py-2">Uploader</th><th class="px-5 py-2">Status</th><th class="px-5 py-2">Category</th><th class="px-5 py-2">Created</th><th class="px-5 py-2">Actions</th>
                            </tr></thead>
                            <tbody>
                                @foreach($documentsTable as $doc)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5 text-slate-400">{{ $doc->id }}</td>
                                    <td class="px-5 py-2.5 font-medium text-slate-800 max-w-xs truncate">{{ $doc->title }}</td>
                                    <td class="px-5 py-2.5 text-slate-600">{{ $doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'N/A' }}</td>
                                    <td class="px-5 py-2.5">
                                        @php $s = $doc->status->status ?? 'unknown'; @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $s === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($s === 'pending' ? 'bg-amber-100 text-amber-700' : ($s === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-slate-100/50 text-slate-600')) }}">{{ ucfirst($s) }}</span>
                                    </td>
                                    <td class="px-5 py-2.5 text-slate-500 text-xs">{{ $doc->categories->pluck('category')->join(', ') ?: ($doc->category ?? '-') }}</td>
                                    <td class="px-5 py-2.5 text-slate-400 text-xs">{{ $doc->created_at?->format('M d, Y') }}</td>
                                    <td class="px-5 py-2.5">
                                        <div class="flex gap-2">
                                            <a href="{{ route('documents.show', $doc->id) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                            <a href="{{ route('documents.edit', $doc->id) }}" class="text-amber-600 hover:underline text-xs">Edit</a>
                                            <form method="POST" action="{{ route('documents.destroy', $doc->id) }}" class="inline" onsubmit="return confirm('Delete this document?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 hover:underline text-xs">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($documentsTable->hasPages())
                    <div class="px-5 py-3 border-t border-slate-100">{{ $documentsTable->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ━━━ TAB: Companies ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div x-show="activeTab === 'companies'" x-cloak>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-700">All Companies ({{ $totalCompanies }})</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dashboard.export-excel') }}?type=companies" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Manage Companies</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">Company</th><th class="px-5 py-2 text-right">Users</th><th class="px-5 py-2 text-right">Teams</th><th class="px-5 py-2 text-right">Subscriptions</th><th class="px-5 py-2">Actions</th>
                            </tr></thead>
                            <tbody>
                                @foreach($companiesTable as $c)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">{{ substr($c->company_name, 0, 1) }}</div>
                                            <span class="font-medium text-slate-800">{{ $c->company_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-2.5 text-right text-slate-600">{{ $c->employees_count }}</td>
                                    <td class="px-5 py-2.5 text-right text-slate-600">{{ $c->offices_count }}</td>
                                    <td class="px-5 py-2.5 text-right text-slate-600">{{ $c->subscriptions_count }}</td>
                                    <td class="px-5 py-2.5">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('companies.show', $c->id) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                            <a href="{{ route('users.create', ['company_id' => $c->id]) }}" class="text-emerald-600 hover:underline text-xs">Add User</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($companiesTable->hasPages())
                    <div class="px-5 py-3 border-t border-slate-100">{{ $companiesTable->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ━━━ TAB: Subscriptions ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div x-show="activeTab === 'subscriptions'" x-cloak>
                {{-- Plans overview cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    @foreach($plansTable as $plan)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-semibold text-slate-800">{{ $plan->plan_name }}</div>
                                <div class="text-xl font-bold text-indigo-600 mt-1">P{{ number_format($plan->price / 100, 2) }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ ucfirst($plan->billing_cycle) }}</div>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100/50 text-slate-500' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <div class="text-xs text-slate-400 mt-2">{{ $plan->subscriptions_count }} subscriptions</div>
                    </div>
                    @endforeach
                </div>

                {{-- Subscriptions table --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-700">All Subscriptions ({{ $totalSubscriptions }})</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dashboard.export-excel') }}?type=subscriptions" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Export Excel
                            </a>
                            <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Manage</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">Company</th><th class="px-5 py-2">Plan</th><th class="px-5 py-2">Status</th><th class="px-5 py-2">Start</th><th class="px-5 py-2">End</th><th class="px-5 py-2">Auto Renew</th><th class="px-5 py-2">Actions</th>
                            </tr></thead>
                            <tbody>
                                @foreach($subscriptionsTable as $sub)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5 font-medium text-slate-800">{{ $sub->company->company_name ?? 'N/A' }}</td>
                                    <td class="px-5 py-2.5"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">{{ $sub->plan->plan_name ?? 'N/A' }}</span></td>
                                    <td class="px-5 py-2.5">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $sub->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($sub->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($sub->status === 'canceled' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-700')) }}">{{ ucfirst($sub->status) }}</span>
                                    </td>
                                    <td class="px-5 py-2.5 text-slate-500 text-xs">{{ $sub->start_date }}</td>
                                    <td class="px-5 py-2.5 text-slate-500 text-xs">{{ $sub->end_date ?? 'N/A' }}</td>
                                    <td class="px-5 py-2.5 text-slate-500 text-xs">{{ $sub->auto_renew ? 'Yes' : 'No' }}</td>
                                    <td class="px-5 py-2.5">
                                        <form method="POST" action="{{ route('admin.subscriptions.status.update', $sub->id) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="status" class="rounded-md border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                @foreach(['active', 'pending', 'canceled', 'expired'] as $statusOption)
                                                    <option value="{{ $statusOption }}" {{ $sub->status === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">Save</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($subscriptionsTable->hasPages())
                    <div class="px-5 py-3 border-t border-slate-100">{{ $subscriptionsTable->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ━━━ TAB: Audit ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div x-show="activeTab === 'audit'" x-cloak>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Site-Wide Audit</h3>
                    <form method="GET" action="{{ route('admin.audit') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Scope</label>
                            <select name="scope" id="audit_scope" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="document.getElementById('company_select_wrap').style.display = this.value === 'all' ? 'none' : 'block'">
                                <option value="all">All Companies (Site-Wide)</option>
                                <option value="company">Specific Company</option>
                            </select>
                        </div>
                        <div id="company_select_wrap" style="display:none">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Company</label>
                            <select name="company_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select company...</option>
                                @foreach($companiesList as $c)
                                <option value="{{ $c->id }}">{{ $c->company_name }} ({{ $c->employees_count }} users)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ now()->subDays(30)->toDateString() }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ now()->toDateString() }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-4">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Generate Audit Report
                            </button>
                        </div>
                    </form>
                </div>
                <p class="text-sm text-slate-500">Select a scope and date range above, then click <strong>Generate Audit Report</strong>. Results will open on the dedicated audit page.</p>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($monthlyStats['labels']);
        const p = { indigo: 'rgba(99,102,241,', emerald: 'rgba(16,185,129,', amber: 'rgba(245,158,11,', rose: 'rgba(244,63,94,' };
        const chartOpts = (legend = false) => ({ responsive: true, maintainAspectRatio: false, plugins: { legend: legend ? { position: 'bottom', labels: { boxWidth: 12, padding: 10 } } : { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } });
        const doughnutOpts = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } } } };

        new Chart(document.getElementById('growthChart'), {
            type: 'line',
            data: { labels, datasets: [
                { label: 'Users', data: @json($monthlyStats['usersData']), borderColor: p.indigo+'1)', backgroundColor: p.indigo+'0.1)', fill: true, tension: 0.3 },
                { label: 'Companies', data: @json($monthlyStats['companiesData']), borderColor: p.emerald+'1)', backgroundColor: p.emerald+'0.1)', fill: true, tension: 0.3 },
            ]},
            options: chartOpts(true)
        });

        new Chart(document.getElementById('docsChart'), {
            type: 'bar',
            data: { labels, datasets: [{ label: 'Documents', data: @json($monthlyStats['documentsData']), backgroundColor: p.amber+'0.7)', borderColor: p.amber+'1)', borderWidth: 1 }] },
            options: chartOpts()
        });

        const planLabels = @json($planDistribution->keys());
        const planData = @json($planDistribution->values());
        const dColors = ['rgba(99,102,241,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)','rgba(244,63,94,0.8)','rgba(139,92,246,0.8)','rgba(14,165,233,0.8)'];
        new Chart(document.getElementById('planChart'), {
            type: 'doughnut',
            data: { labels: planLabels, datasets: [{ data: planData, backgroundColor: dColors.slice(0, planLabels.length), borderWidth: 2 }] },
            options: doughnutOpts
        });

        const sLabels = @json($documentStatuses->keys());
        const sData = @json($documentStatuses->values());
        const sColors = { forwarded:'rgba(99,102,241,0.7)', pending:'rgba(245,158,11,0.7)', approved:'rgba(16,185,129,0.7)', rejected:'rgba(244,63,94,0.7)', archived:'rgba(100,116,139,0.7)', received:'rgba(14,165,233,0.7)' };
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: { labels: sLabels, datasets: [{ data: sData, backgroundColor: sLabels.map(l => sColors[l] || 'rgba(148,163,184,0.7)'), borderWidth: 2 }] },
            options: doughnutOpts
        });

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Revenue', data: @json($monthlyStats['revenueData']), borderColor: p.rose+'1)', backgroundColor: p.rose+'0.1)', fill: true, tension: 0.3 }] },
            options: chartOpts()
        });
    });
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</x-app-layout>
