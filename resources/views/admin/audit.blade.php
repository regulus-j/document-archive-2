<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">Site-Wide Audit</h2>
                <p class="text-sm text-slate-500 mt-0.5">Audit logs across all companies</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="bg-gradient-to-b from-slate-50 to-white min-h-screen pb-12">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- Filter Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Audit Filters</h3>
                <form method="GET" action="{{ route('admin.audit') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Scope</label>
                        <select name="scope" id="audit_scope_page" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="document.getElementById('company_wrap_page').style.display = this.value === 'all' ? 'none' : 'block'">
                            <option value="all" {{ request('scope') === 'all' ? 'selected' : '' }}>All Companies (Site-Wide)</option>
                            <option value="company" {{ request('scope', 'company') === 'company' && request('company_id') ? 'selected' : '' }}>Specific Company</option>
                        </select>
                    </div>
                    <div id="company_wrap_page" style="{{ request('scope') === 'all' ? 'display:none' : '' }}">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Company</label>
                        <select name="company_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select company...</option>
                            @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ $companyId == $c->id ? 'selected' : '' }}>{{ $c->company_name }} ({{ $c->employees_count }} users)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition w-full justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Generate
                        </button>
                    </div>
                </form>
            </div>

            @if($auditData)
                {{-- Export Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">Export Options</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Download the audit report for {{ $selectedCompany->company_name ?? 'Site-Wide' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.audit.export-pdf') }}?{{ http_build_query(request()->only(['scope', 'company_id', 'start_date', 'end_date'])) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export PDF
                        </a>
                        <a href="{{ route('admin.audit.export-excel') }}?{{ http_build_query(request()->only(['scope', 'company_id', 'start_date', 'end_date'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </a>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        <div class="text-xs font-medium text-slate-500 uppercase">Audit Logs</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $auditData['audit_logs']->count() }}</div>
                        <div class="text-xs text-slate-400">{{ $selectedCompany->company_name ?? 'Site-Wide' }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        <div class="text-xs font-medium text-slate-500 uppercase">Documents Uploaded</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $auditData['uploaded_documents']->count() }}</div>
                        <div class="text-xs text-slate-400">{{ $startDate }} to {{ $endDate }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        <div class="text-xs font-medium text-slate-500 uppercase">Workflows</div>
                        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $auditData['workflows']->count() }}</div>
                        <div class="text-xs text-slate-400">Transactions in range</div>
                    </div>
                </div>

                {{-- Audit Logs Table --}}
                @if($auditData['audit_logs']->count())
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-700">Audit Logs</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">Date</th><th class="px-5 py-2">User</th><th class="px-5 py-2">Action</th><th class="px-5 py-2">Document</th><th class="px-5 py-2">Details</th>
                            </tr></thead>
                            <tbody>
                                @foreach($auditData['audit_logs'] as $log)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5 text-slate-400 text-xs whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-5 py-2.5 text-slate-700">{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</td>
                                    <td class="px-5 py-2.5"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">{{ $log->action ?? 'N/A' }}</span></td>
                                    <td class="px-5 py-2.5 text-slate-600">{{ $log->document->title ?? 'N/A' }}</td>
                                    <td class="px-5 py-2.5 text-slate-500 text-xs max-w-xs truncate">{{ $log->details ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Uploaded Documents Table --}}
                @if($auditData['uploaded_documents']->count())
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-700">Uploaded Documents</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">Date</th><th class="px-5 py-2">Title</th><th class="px-5 py-2">Uploader</th><th class="px-5 py-2">Status</th><th class="px-5 py-2">Tracking #</th>
                            </tr></thead>
                            <tbody>
                                @foreach($auditData['uploaded_documents'] as $doc)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5 text-slate-400 text-xs whitespace-nowrap">{{ $doc->created_at->format('M d, Y') }}</td>
                                    <td class="px-5 py-2.5 font-medium text-slate-800">
                                        <a href="{{ route('documents.show', $doc->id) }}" class="text-indigo-600 hover:underline">{{ $doc->title }}</a>
                                    </td>
                                    <td class="px-5 py-2.5 text-slate-600">{{ $doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'N/A' }}</td>
                                    <td class="px-5 py-2.5"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">{{ $doc->status->status ?? 'N/A' }}</span></td>
                                    <td class="px-5 py-2.5 text-slate-500 text-xs">{{ $doc->trackingNumber->tracking_number ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Workflows Table --}}
                @if($auditData['workflows']->count())
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-700">Document Workflows / Transactions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="bg-slate-50 text-left text-xs text-slate-500 uppercase">
                                <th class="px-5 py-2">Date</th><th class="px-5 py-2">Document</th><th class="px-5 py-2">Sender</th><th class="px-5 py-2">Recipient</th><th class="px-5 py-2">Status</th>
                            </tr></thead>
                            <tbody>
                                @foreach($auditData['workflows'] as $wf)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-2.5 text-slate-400 text-xs whitespace-nowrap">{{ $wf->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-5 py-2.5 text-slate-700">{{ $wf->document->title ?? 'N/A' }}</td>
                                    <td class="px-5 py-2.5 text-slate-600">{{ $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A' }}</td>
                                    <td class="px-5 py-2.5 text-slate-600">{{ $wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'N/A' }}</td>
                                    <td class="px-5 py-2.5"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">{{ $wf->status ?? 'N/A' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            @else
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3 class="text-lg font-medium text-slate-700 mb-1">No Audit Data</h3>
                    <p class="text-sm text-slate-500">Select a scope and date range above, then click <strong>Generate</strong> to view audit data.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
