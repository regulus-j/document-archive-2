@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Success / Error Messages --}}
        @if (session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-lg shadow-sm" role="alert">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-lg shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- PAGE HEADER --}}
        <div class="bg-white rounded-lg shadow-card border border-slate-200/80 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">{{ __('Audit Report') }}</h1>
                        <p class="text-sm text-slate-500 mt-0.5">Generate detailed audit trails for users or offices</p>
                    </div>
                </div>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Reports
                </a>
            </div>
        </div>

        {{-- AUDIT FORM --}}
        <div class="bg-white rounded-lg shadow-card border border-slate-200/80 p-6 mb-6" x-data="auditForm()">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Configure Audit</h2>
            <form action="{{ route('reports.audit.generate') }}" method="POST" id="auditForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                    {{-- Audit Target --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Audit Target</label>
                        <select name="audit_target" x-model="auditTarget"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="user">User</option>
                            <option value="office">Office / Team</option>
                        </select>
                    </div>

                    {{-- User Searchable Dropdown --}}
                    <div x-show="auditTarget === 'user'" x-cloak>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Select User</label>
                        <div class="relative" x-data="searchableSelect({
                            items: {{ \Illuminate\Support\Js::from($users->map(fn($u) => ['id' => $u->id, 'name' => $u->first_name . ' ' . ($u->middle_name ? $u->middle_name . ' ' : '') . $u->last_name, 'email' => $u->email])) }},
                            fieldName: 'user_id',
                            selectedId: '{{ old('user_id', request('user_id')) }}'
                        })">
                            <input type="hidden" name="user_id" :value="selectedId">
                            <div class="relative">
                                <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                       placeholder="Search users..."
                                       class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 pr-8"
                                       autocomplete="off">
                                <button type="button" @click="clearSelection()" x-show="selectedId"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div x-show="open && filteredItems.length > 0" x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <button type="button" @click="selectItem(item)"
                                            class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 flex flex-col border-b border-slate-100 last:border-0 transition-colors">
                                        <span class="text-sm font-medium text-slate-900" x-text="item.name"></span>
                                        <span class="text-xs text-slate-400" x-text="item.email"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="open && search.length > 0 && filteredItems.length === 0" x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg p-4 text-center text-sm text-slate-400">
                                No users found
                            </div>
                        </div>
                    </div>

                    {{-- Office Searchable Dropdown --}}
                    <div x-show="auditTarget === 'office'" x-cloak>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Select Office</label>
                        <div class="relative" x-data="searchableSelect({
                            items: {{ \Illuminate\Support\Js::from($offices->map(fn($o) => ['id' => $o->id, 'name' => $o->name, 'email' => ''])) }},
                            fieldName: 'office_id',
                            selectedId: '{{ old('office_id', request('office_id')) }}'
                        })">
                            <input type="hidden" name="office_id" :value="selectedId">
                            <div class="relative">
                                <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                       placeholder="Search offices..."
                                       class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 pr-8"
                                       autocomplete="off">
                                <button type="button" @click="clearSelection()" x-show="selectedId"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div x-show="open && filteredItems.length > 0" x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <button type="button" @click="selectItem(item)"
                                            class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 flex flex-col border-b border-slate-100 last:border-0 transition-colors">
                                        <span class="text-sm font-medium text-slate-900" x-text="item.name"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="open && search.length > 0 && filteredItems.length === 0" x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg p-4 text-center text-sm text-slate-400">
                                No offices found
                            </div>
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->subMonth()->format('Y-m-d')) }}" required
                               class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date', now()->format('Y-m-d')) }}" required
                               class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Filter Checkboxes --}}
                <div class="mt-6">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">What to Audit</label>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $filterOptions = [
                                'actions' => ['label' => 'Actions', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'indigo'],
                                'uploads' => ['label' => 'Documents Uploaded', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12', 'color' => 'emerald'],
                                'received' => ['label' => 'Documents Received', 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => 'blue'],
                                'attachments' => ['label' => 'Attachments Added', 'icon' => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13', 'color' => 'amber'],
                                'reviewed' => ['label' => 'Documents Reviewed', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'purple'],
                            ];
                        @endphp
                        @foreach($filterOptions as $value => $option)
                        <label class="relative flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 cursor-pointer
                                      hover:border-{{ $option['color'] }}-300 hover:bg-{{ $option['color'] }}-50/50 transition-all
                                      has-[:checked]:border-{{ $option['color'] }}-400 has-[:checked]:bg-{{ $option['color'] }}-50 has-[:checked]:ring-1 has-[:checked]:ring-{{ $option['color'] }}-300">
                            <input type="checkbox" name="filters[]" value="{{ $value }}"
                                   {{ in_array($value, old('filters', $filters ?? ['actions', 'uploads', 'received', 'attachments', 'reviewed'])) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-{{ $option['color'] }}-600 focus:ring-{{ $option['color'] }}-500">
                            <svg class="w-4 h-4 text-{{ $option['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $option['icon'] }}"/>
                            </svg>
                            <span class="text-sm font-medium text-slate-700">{{ $option['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-6 flex flex-wrap items-center gap-3 pt-4 border-t border-slate-200">
                    <button type="submit" name="output" value="view"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Preview Report
                    </button>
                    <p class="text-xs text-slate-400">Preview first, then export as PDF or Excel from the results.</p>
                </div>
            </form>
        </div>

        {{-- RESULTS --}}
        @if(!empty($hasResults))
        <div class="space-y-6">
            {{-- Summary Header --}}
            <div class="bg-white rounded-lg shadow-card border border-slate-200/80 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Audit Report: {{ $target_label }}
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}
                            <span class="mx-2 text-slate-300">|</span>
                            Generated {{ $generated_at }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Export PDF button --}}
                        <form action="{{ route('reports.audit.generate') }}" method="POST" class="inline" target="_blank">
                            @csrf
                            <input type="hidden" name="audit_target" value="{{ $audit_target }}">
                            @if($audit_target === 'user')
                                <input type="hidden" name="user_id" value="{{ $target_id }}">
                            @else
                                <input type="hidden" name="office_id" value="{{ $target_id }}">
                            @endif
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            @foreach($filters as $f)
                                <input type="hidden" name="filters[]" value="{{ $f }}">
                            @endforeach
                            <input type="hidden" name="output" value="pdf">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                PDF
                            </button>
                        </form>
                        {{-- Export Excel button --}}
                        <form action="{{ route('reports.audit.generate') }}" method="POST" class="inline" target="_blank">
                            @csrf
                            <input type="hidden" name="audit_target" value="{{ $audit_target }}">
                            @if($audit_target === 'user')
                                <input type="hidden" name="user_id" value="{{ $target_id }}">
                            @else
                                <input type="hidden" name="office_id" value="{{ $target_id }}">
                            @endif
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            @foreach($filters as $f)
                                <input type="hidden" name="filters[]" value="{{ $f }}">
                            @endforeach
                            <input type="hidden" name="output" value="excel">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Excel
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Summary Stats Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @if(isset($audit_logs))
                    <div class="bg-indigo-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-indigo-700">{{ $audit_logs->count() }}</div>
                        <div class="text-xs text-indigo-600 font-medium mt-1">Actions</div>
                    </div>
                    @endif
                    @if(isset($uploaded_documents))
                    <div class="bg-emerald-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-700">{{ $uploaded_documents->count() }}</div>
                        <div class="text-xs text-emerald-600 font-medium mt-1">Uploaded</div>
                    </div>
                    @endif
                    @if(isset($received_workflows))
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-700">{{ $received_workflows->count() }}</div>
                        <div class="text-xs text-blue-600 font-medium mt-1">Received</div>
                    </div>
                    @endif
                    @if(isset($attachments_added))
                    <div class="bg-amber-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-amber-700">{{ $attachments_added->count() }}</div>
                        <div class="text-xs text-amber-600 font-medium mt-1">Attachments</div>
                    </div>
                    @endif
                    @if(isset($reviewed_workflows))
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-700">{{ $reviewed_workflows->count() }}</div>
                        <div class="text-xs text-purple-600 font-medium mt-1">Reviewed</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions Table --}}
            @if(isset($audit_logs))
            <div class="bg-white rounded-lg shadow-card border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-indigo-50/50">
                    <h3 class="text-base font-semibold text-indigo-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Actions Log ({{ $audit_logs->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                @if($audit_target === 'office')<th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>@endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($audit_logs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                @if($audit_target === 'office')<td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'N/A' }}</td>@endif
                                <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $log->document->title ?? 'Document #' . $log->document_id }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ ucfirst($log->action) }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ ucfirst($log->status ?? '-') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500 max-w-xs truncate">{{ $log->details ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="px-4 py-8 text-center text-sm text-slate-400">No actions found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Uploaded Documents Table --}}
            @if(isset($uploaded_documents))
            <div class="bg-white rounded-lg shadow-card border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-emerald-50/50">
                    <h3 class="text-base font-semibold text-emerald-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Documents Uploaded ({{ $uploaded_documents->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                @if($audit_target === 'office')<th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Uploaded By</th>@endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tracking #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($uploaded_documents as $doc)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $doc->created_at->format('M d, Y h:i A') }}</td>
                                @if($audit_target === 'office')<td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'N/A' }}</td>@endif
                                <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $doc->title }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $doc->trackingNumber->tracking_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $doc->categories->pluck('category')->join(', ') ?: ($doc->category ?? '-') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ ucfirst($doc->status->status ?? 'N/A') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="px-4 py-8 text-center text-sm text-slate-400">No uploads found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Received Documents Table --}}
            @if(isset($received_workflows))
            <div class="bg-white rounded-lg shadow-card border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-blue-50/50">
                    <h3 class="text-base font-semibold text-blue-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        Documents Received ({{ $received_workflows->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                @if($audit_target === 'office')<th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Received By</th>@endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Sent By</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Purpose</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($received_workflows as $wf)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $wf->created_at->format('M d, Y h:i A') }}</td>
                                @if($audit_target === 'office')<td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'Office' }}</td>@endif
                                <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $wf->document->title ?? 'Document #' . $wf->document_id }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ ucfirst($wf->purpose ?? '-') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ ucfirst($wf->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="px-4 py-8 text-center text-sm text-slate-400">No received documents found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Attachments Added Table --}}
            @if(isset($attachments_added))
            <div class="bg-white rounded-lg shadow-card border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-amber-50/50">
                    <h3 class="text-base font-semibold text-amber-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Attachments Added ({{ $attachments_added->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                @if($audit_target === 'office')<th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Added By</th>@endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Filename</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Size</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attachments_added as $att)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $att->created_at->format('M d, Y h:i A') }}</td>
                                @if($audit_target === 'office')<td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $att->uploader ? $att->uploader->first_name . ' ' . $att->uploader->last_name : 'N/A' }}</td>@endif
                                <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $att->document->title ?? 'Document #' . $att->document_id }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $att->filename }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $att->mime_type ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $att->storage_size ? number_format($att->storage_size / 1024, 1) . ' KB' : '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="px-4 py-8 text-center text-sm text-slate-400">No attachments found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Reviewed Documents Table --}}
            @if(isset($reviewed_workflows))
            <div class="bg-white rounded-lg shadow-card border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-purple-50/50">
                    <h3 class="text-base font-semibold text-purple-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Documents Reviewed ({{ $reviewed_workflows->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                @if($audit_target === 'office')<th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Reviewed By</th>@endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Sent By</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Decision</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reviewed_workflows as $wf)
                            @php
                                $statusColors = [
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'returned' => 'bg-yellow-100 text-yellow-700',
                                    'commented' => 'bg-blue-100 text-blue-700',
                                    'acknowledged' => 'bg-purple-100 text-purple-700',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $wf->created_at->format('M d, Y h:i A') }}</td>
                                @if($audit_target === 'office')<td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $wf->recipient ? $wf->recipient->first_name . ' ' . $wf->recipient->last_name : 'Office' }}</td>@endif
                                <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $wf->document->title ?? 'Document #' . $wf->document_id }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $wf->sender ? $wf->sender->first_name . ' ' . $wf->sender->last_name : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$wf->status] ?? 'bg-slate-100 text-slate-700' }}">{{ ucfirst($wf->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-500 max-w-xs truncate">{{ $wf->remarks ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $audit_target === 'office' ? 6 : 5 }}" class="px-4 py-8 text-center text-sm text-slate-400">No reviewed documents found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    function auditForm() {
        return {
            auditTarget: '{{ old('audit_target', 'user') }}',
        }
    }

    function searchableSelect({ items, fieldName, selectedId }) {
        return {
            items: items,
            search: '',
            open: false,
            selectedId: selectedId || '',
            get filteredItems() {
                if (!this.search) return this.items;
                const s = this.search.toLowerCase();
                return this.items.filter(i =>
                    i.name.toLowerCase().includes(s) ||
                    (i.email && i.email.toLowerCase().includes(s))
                );
            },
            selectItem(item) {
                this.selectedId = item.id;
                this.search = item.name;
                this.open = false;
            },
            clearSelection() {
                this.selectedId = '';
                this.search = '';
            },
            init() {
                if (this.selectedId) {
                    const found = this.items.find(i => String(i.id) === String(this.selectedId));
                    if (found) this.search = found.name;
                }
            }
        }
    }
</script>
@endpush
@endsection
