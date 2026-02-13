@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gray-50 via-white to-blue-50 min-h-screen py-8">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Success / Error Messages --}}
        @if (session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl shadow-sm" role="alert">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        {{-- ============================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ============================================= --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Analytics & Reports') }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Analyze document workflows and generate custom reports</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('reports.index', ['display_type' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId, 'office_id' => $officeId]) }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- FILTER BAR --}}
        {{-- ============================================= --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
            <form action="{{ route('reports.index') }}" method="GET">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[140px]">
                        <label for="start_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" required
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex-1 min-w-[140px]">
                        <label for="end_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" required
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label for="user_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('User') }}</label>
                        <select name="user_id" id="user_id" class="w-full select2 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Users') }}</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label for="office_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Office') }}</label>
                        <select name="office_id" id="office_id" class="w-full select2 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Offices') }}</option>
                            @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-shrink-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('View') }}</label>
                        <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden">
                            <label class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium cursor-pointer {{ $displayType == 'table' ? 'bg-blue-50 text-blue-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                                <input type="radio" name="display_type" value="table" class="sr-only" {{ $displayType == 'table' ? 'checked' : '' }}>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Table
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium cursor-pointer border-l border-r border-gray-200 {{ $displayType == 'graph' ? 'bg-blue-50 text-blue-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                                <input type="radio" name="display_type" value="graph" class="sr-only" {{ $displayType == 'graph' ? 'checked' : '' }}>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Chart
                            </label>
                            <label class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium cursor-pointer {{ $displayType == 'both' ? 'bg-blue-50 text-blue-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                                <input type="radio" name="display_type" value="both" class="sr-only" {{ $displayType == 'both' ? 'checked' : '' }}>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                Both
                            </label>
                        </div>
                    </div>
                    <button type="submit" id="filter-button"
                            class="flex-shrink-0 inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================= --}}
        {{-- KPI SUMMARY CARDS --}}
        {{-- ============================================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Time to Receive</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $averageTimeToReceive }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-100">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Time to Review</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $averageTimeToReview }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-100">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </span>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Docs Forwarded</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $averageDocsForwarded }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-100">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </span>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Docs Uploaded</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $documentsUploaded }}</p>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- TABLE VIEW --}}
        {{-- ============================================= --}}
        @if($displayType == 'table' || $displayType == 'both')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Analytics Summary') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Key metrics for the selected period</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Metric') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                {{ __('Average Time to Receive') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $averageTimeToReceive }}</td>
                        </tr>
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                {{ __('Average Time to Review') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $averageTimeToReview }}</td>
                        </tr>
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                {{ __('Documents Forwarded') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $averageDocsForwarded }}</td>
                        </tr>
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                {{ __('Documents Uploaded') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $documentsUploaded }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ============================================= --}}
        {{-- CHART VIEW --}}
        {{-- ============================================= --}}
        @if($displayType == 'graph' || $displayType == 'both')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Monthly Trends') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Document activity over time</p>
            </div>
            <div class="p-6">
                <canvas id="monthlyTrendsChart" height="100"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Processing Times') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Average receive & review times by month</p>
                </div>
                <div class="p-6">
                    <canvas id="processingTimesChart" height="220"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Document Distribution') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Forwarded vs Uploaded breakdown</p>
                </div>
                <div class="p-6 flex items-center justify-center" style="min-height: 260px">
                    <canvas id="documentStatsChart"></canvas>
                </div>
            </div>
        </div>
        @endif

        {{-- ============================================= --}}
        {{-- REPORT GENERATOR --}}
        {{-- ============================================= --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Audit Report Generator') }}</h3>
                    <p class="text-xs text-gray-500">Generate audit history reports with export options</p>
                </div>
            </div>

            <div class="lg:flex divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                {{-- Left Panel: Form --}}
                <div class="lg:w-[380px] flex-shrink-0 p-6">
                    <form action="{{ route('reports.generate') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="report_type" value="audit_history">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Quick Date Range') }}</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="setDateRange('week', event)" data-preset="week"
                                    class="date-preset-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:border-blue-300 transition">
                                    Past Week
                                </button>
                                <button type="button" onclick="setDateRange('month', event)" data-preset="month"
                                    class="date-preset-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:border-blue-300 transition">
                                    Past Month
                                </button>
                                <button type="button" onclick="setDateRange('quarter', event)" data-preset="quarter"
                                    class="date-preset-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:border-blue-300 transition">
                                    Past Quarter
                                </button>
                                <button type="button" onclick="setDateRange('year', event)" data-preset="year"
                                    class="date-preset-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-700 hover:bg-blue-50 hover:border-blue-300 transition">
                                    Past Year
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Custom Date Range') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">From</label>
                                    <input type="date" name="start_date" id="audit_start_date" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">To</label>
                                    <input type="date" name="end_date" id="audit_end_date" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Export Format') }}</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="relative flex flex-col items-center p-3 rounded-lg border-2 cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                    <input type="radio" name="export_format" value="none" checked class="sr-only">
                                    <svg class="w-5 h-5 text-gray-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="text-xs font-medium text-gray-700">Preview</span>
                                </label>
                                <label class="relative flex flex-col items-center p-3 rounded-lg border-2 cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                    <input type="radio" name="export_format" value="pdf" class="sr-only">
                                    <svg class="w-5 h-5 text-red-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-medium text-gray-700">PDF</span>
                                </label>
                                <label class="relative flex flex-col items-center p-3 rounded-lg border-2 cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                    <input type="radio" name="export_format" value="excel" class="sr-only">
                                    <svg class="w-5 h-5 text-green-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-medium text-gray-700">Excel</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" id="generate-report-btn"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ __('Generate Report') }}
                        </button>
                    </form>
                </div>

                {{-- Right Panel: Preview --}}
                <div class="flex-1 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h3 class="text-sm font-semibold text-gray-700">{{ __('Report Preview') }}</h3>
                    </div>

                    @if (isset($data) && $data->count())
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($data as $item)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-5 py-3 text-sm text-gray-600 font-medium">{{ $item->id }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $item->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-500 max-w-xs truncate">{{ json_encode($item->toArray()) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="border-2 border-dashed border-gray-200 rounded-xl h-80 flex items-center justify-center">
                        <div class="text-center px-4">
                            <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700">No report generated yet</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Choose a report type and date range, then click Generate') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== Select2 =====
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#user_id, #office_id').select2({ placeholder: "Select an option", allowClear: true, width: '100%', dropdownParent: $('body') });
    }

    // ===== Display type toggle =====
    document.querySelectorAll('input[name="display_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // ===== Form validation =====
    const startDateInput = document.getElementById('audit_start_date');
    const endDateInput = document.getElementById('audit_end_date');
    const generateButton = document.getElementById('generate-report-btn');

    function validateForm() {
        if (!generateButton) return;
        const start = startDateInput ? startDateInput.value : '';
        const end = endDateInput ? endDateInput.value : '';
        generateButton.disabled = !(start && end);
    }

    if (startDateInput) startDateInput.addEventListener('change', validateForm);
    if (endDateInput) endDateInput.addEventListener('change', validateForm);
    validateForm();

    // ===== Date Preset Buttons =====
    window.setDateRange = function(preset, e) {
        const today = new Date();
        let start = new Date();
        switch (preset) {
            case 'week': start.setDate(today.getDate() - 7); break;
            case 'month': start.setMonth(today.getMonth() - 1); break;
            case 'quarter': start.setMonth(today.getMonth() - 3); break;
            case 'year': start.setFullYear(today.getFullYear() - 1); break;
        }
        if (startDateInput) startDateInput.value = start.toISOString().split('T')[0];
        if (endDateInput) endDateInput.value = today.toISOString().split('T')[0];

        // Highlight active preset button
        document.querySelectorAll('.date-preset-btn').forEach(function(b) {
            b.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
            b.classList.add('border-gray-200', 'text-gray-600');
        });
        // Find the clicked button (from event) or the matching data-preset button (programmatic)
        var btn = e ? e.target.closest('.date-preset-btn') : document.querySelector('.date-preset-btn[data-preset="' + preset + '"]');
        if (btn) {
            btn.classList.remove('border-gray-200', 'text-gray-600');
            btn.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
        }
        validateForm();
    };

    // Default to past month on load
    setDateRange('month', null);

    // ===== Chart defaults =====
    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;

    @if($displayType == 'graph' || $displayType == 'both')
    // ===== Monthly Trends =====
    try {
        var trendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
        var blueGrad = trendsCtx.createLinearGradient(0, 0, 0, 300);
        blueGrad.addColorStop(0, 'rgba(59,130,246,0.25)');
        blueGrad.addColorStop(1, 'rgba(59,130,246,0.02)');
        var greenGrad = trendsCtx.createLinearGradient(0, 0, 0, 300);
        greenGrad.addColorStop(0, 'rgba(16,185,129,0.25)');
        greenGrad.addColorStop(1, 'rgba(16,185,129,0.02)');

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData['months']) !!},
                datasets: [{
                    label: 'Docs Forwarded',
                    data: {!! json_encode($monthlyData['docsForwarded']) !!},
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
                    label: 'Docs Uploaded',
                    data: {!! json_encode($monthlyData['docsUploaded']) !!},
                    backgroundColor: greenGrad,
                    borderColor: 'rgba(16,185,129,1)',
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
    } catch(e) { console.error('Monthly trends chart error:', e); }

    // ===== Processing Times =====
    try {
        new Chart(document.getElementById('processingTimesChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyData['months']) !!},
                datasets: [{
                    label: 'Avg Time to Receive (min)',
                    data: {!! json_encode($monthlyData['receiveTimes']) !!},
                    backgroundColor: 'rgba(239,68,68,0.15)',
                    borderColor: 'rgba(239,68,68,1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    barPercentage: 0.6
                }, {
                    label: 'Avg Time to Review (min)',
                    data: {!! json_encode($monthlyData['reviewTimes']) !!},
                    backgroundColor: 'rgba(139,92,246,0.15)',
                    borderColor: 'rgba(139,92,246,1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.9)',
                        cornerRadius: 8,
                        padding: 12,
                        callbacks: {
                            label: function(ctx) {
                                const m = ctx.raw;
                                if (m >= 60) {
                                    return ctx.dataset.label + ': ' + Math.floor(m/60) + 'h ' + Math.round(m%60) + 'm';
                                }
                                return ctx.dataset.label + ': ' + m + ' min';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#94a3b8' }, title: { display: true, text: 'Minutes', color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    } catch(e) { console.error('Processing times chart error:', e); }

    // ===== Document Stats Doughnut =====
    try {
        new Chart(document.getElementById('documentStatsChart'), {
            type: 'doughnut',
            data: {
                labels: ['Forwarded', 'Uploaded'],
                datasets: [{
                    data: [{{ $averageDocsForwarded }}, {{ $documentsUploaded }}],
                    backgroundColor: ['rgba(59,130,246,0.7)', 'rgba(16,185,129,0.7)'],
                    borderColor: ['rgba(59,130,246,1)', 'rgba(16,185,129,1)'],
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } }
                }
            }
        });
    } catch(e) { console.error('Document stats chart error:', e); }
    @endif
});
</script>
@endpush
