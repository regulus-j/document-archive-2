@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header Box -->
            <div class="bg-white rounded-xl shadow-xl mb-6 border border-blue-100 overflow-hidden">
                <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ __('Report Management') }}</h1>
                            <p class="text-sm text-gray-500">Generate and view custom reports</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('reports.analytics') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('See Analytics') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-white border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-md"
                    role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
                <div class="lg:flex">
                    <!-- Left Panel -->
                    <div class="lg:w-1/3 bg-white p-6 border-b lg:border-b-0 lg:border-r border-blue-100">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Create a Report') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Create a custom report based on collected data.') }}
                            </p>
                        </div>

                        <form action="{{ route('reports.generate') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label for="report_type"
                                    class="block text-sm font-medium text-gray-700">{{ __('Choose report type') }}</label>
                                <select name="report_type" id="report_type"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="audit_history">{{ __('Audit History') }}</option>
                                    <option value="company_performance">{{ __('Company Performance') }}</option>
                                </select>
                                <p class="text-xs text-gray-500">Select the type of report you want to generate</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Choose date range') }}</label>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="date" name="start_date"
                                            class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all"
                                            placeholder="{{ __('From') }}">
                                    </div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="date" name="end_date"
                                            class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all"
                                            placeholder="{{ __('To') }}">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">Select the date range for your report data</p>
                            </div>

                            <div class="pt-4">
                                <button type="submit" id="generate-report-btn"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ __('Generate Report') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Panel -->
                    <div class="lg:flex-1 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('Generated Report Preview') }}
                        </h3>

                        @if (isset($data) && $data->count())
                            <div class="overflow-x-auto">
                                @if(isset($reportType) && $reportType === 'audit_history')
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Date/Time</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Document</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">User</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Action</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Status</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($data as $item)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                                        {{ $item->created_at->format('M d, Y H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700">
                                                        {{ $item->document?->title ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                                        {{ $item->user?->first_name }} {{ $item->user?->last_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ ucfirst($item->action) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                                        {{ $item->status }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">
                                                        {{ $item->details }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @elseif(isset($reportType) && $reportType === 'company_performance')
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Date</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Document</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Sender</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Recipient</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Status</th>
                                                <th class="bg-white px-4 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($data as $item)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                                        {{ $item->created_at->format('M d, Y H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700">
                                                        {{ $item->document?->title ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                                        {{ $item->sender?->first_name }} {{ $item->sender?->last_name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                                        {{ $item->recipient?->first_name }} {{ $item->recipient?->last_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        @php
                                                            $statusColor = match($item->status) {
                                                                'approved' => 'emerald',
                                                                'rejected' => 'rose',
                                                                'pending' => 'amber',
                                                                default => 'blue',
                                                            };
                                                        @endphp
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                            {{ ucfirst($item->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">
                                                        {{ $item->remarks ?? '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">ID</th>
                                                <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Created At</th>
                                                <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($data as $item)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->id }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->created_at }}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->details ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        @else
                            <div
                                class="bg-gray-50 border-2 border-dashed border-blue-200 rounded-lg h-96 flex items-center justify-center">
                                <div class="text-center px-4">
                                    <svg class="mx-auto h-12 w-12 text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No report generated yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('Your generated report will appear here') }}
                                    </p>
                                    <p class="mt-3 text-xs text-gray-500">
                                        Use the form on the left to generate a new report
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report_type');
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        const generateButton = document.getElementById('generate-report-btn');
        
        // Initial validation on page load
        validateForm();
        
        // Add event listeners to all form inputs
        reportTypeSelect.addEventListener('change', validateForm);
        startDateInput.addEventListener('change', validateForm);
        endDateInput.addEventListener('change', validateForm);
        
        function validateForm() {
            // Check if all required fields are filled
            const isReportTypeSelected = reportTypeSelect.value !== '';
            const isStartDateFilled = startDateInput.value !== '';
            const isEndDateFilled = endDateInput.value !== '';
            
            // Enable/disable the generate button based on validation
            if (isReportTypeSelected && isStartDateFilled && isEndDateFilled) {
                generateButton.disabled = false;
                generateButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                generateButton.disabled = true;
                generateButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    });
</script>
@endsection