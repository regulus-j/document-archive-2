@extends('layouts.app')

@section('content')
<div class="bg-slate-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6 sm:flex sm:items-center sm:justify-between">
                <h2 class="text-3xl font-extrabold text-slate-900 flex items-center">
                    <svg class="h-8 w-8 text-indigo-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Reports & Analytics') }}
                </h2>
                <div class="flex space-x-3 mt-4 sm:mt-0">
                    <a href="{{ route('reports.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Generate Reports') }}
                    </a>
                    
                    <a href="{{ route('reports.analytics', ['display_type' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId, 'office_id' => $officeId]) }}" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('Export to PDF') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <form action="{{ route('reports.analytics') }}" method="GET" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-slate-700">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-slate-700">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md">
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-slate-700">{{ __('User') }}</label>
                            <select name="user_id" id="user_id" class="mt-1 select2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md">
                                <option value="">{{ __('All Users') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="office_id" class="block text-sm font-medium text-slate-700">{{ __('Office') }}</label>
                            <select name="office_id" id="office_id" class="mt-1 select2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md">
                                <option value="">{{ __('All Offices') }}</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="display_type" class="block text-sm font-medium text-slate-700">{{ __('Display Type') }}</label>
                            <div class="mt-2 flex flex-wrap gap-3">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="display_type" value="table" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-slate-300" {{ $displayType == 'table' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-slate-700">{{ __('Table') }}</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="display_type" value="graph" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-slate-300" {{ $displayType == 'graph' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-slate-700">{{ __('Graph') }}</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="display_type" value="both" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-slate-300" {{ $displayType == 'both' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-slate-700">{{ __('Both') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" id="filter-button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Filter') }}
                            </button>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" name="display_type" value="pdf"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Export to PDF') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('Analytics Summary') }}</h3>
                
                @if($displayType == 'table' || $displayType == 'both')
                <!-- Table View -->
                <div class="mb-8">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Metric') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Value') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ __('Avg Time to Receive') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $averageTimeToReceive }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ __('Avg Time to Review') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $averageTimeToReview }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ __('Docs Forwarded') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $averageDocsForwarded }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ __('Docs Uploaded') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $documentsUploaded }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
                
                @if($displayType == 'graph' || $displayType == 'both')
                <!-- Chart View -->
                <div class="mt-8">
                    <h4 class="text-md font-semibold text-slate-700 mb-3">{{ __('Monthly Trends') }}</h4>
                    <div class="bg-white rounded-lg shadow p-4">
                        <canvas id="monthlyTrendsChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h4 class="text-md font-semibold text-slate-700 mb-3">{{ __('Processing Times') }}</h4>
                        <canvas id="processingTimesChart" width="400" height="200"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h4 class="text-md font-semibold text-slate-700 mb-3">{{ __('Document Statistics') }}</h4>
                        <canvas id="documentStatsChart" width="400" height="200"></canvas>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Include jQuery if not already loaded -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#user_id, #office_id').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: 'resolve'
            });
            
            // Validate form before submission
            $('#filter-button').click(function(e) {
                if ($('#start_date').val() === '' || $('#end_date').val() === '') {
                    e.preventDefault();
                    alert('Please select both start and end dates');
                }
            });
            
            @if($displayType == 'graph' || $displayType == 'both')
            // Monthly Trends Chart
            const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
            const monthlyTrendsChart = new Chart(monthlyTrendsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyData['months']) !!},
                    datasets: [
                        {
                            label: 'Docs Forwarded',
                            data: {!! json_encode($monthlyData['docsForwarded']) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        },
                        {
                            label: 'Docs Uploaded',
                            data: {!! json_encode($monthlyData['docsUploaded']) !!},
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Document Activity'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    }
                }
            });
            
            // Processing Times Chart
            const processingTimesCtx = document.getElementById('processingTimesChart').getContext('2d');
            const processingTimesChart = new Chart(processingTimesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyData['months']) !!},
                    datasets: [
                        {
                            label: 'Avg Time to Receive (min)',
                            data: {!! json_encode($monthlyData['receiveTimes']) !!},
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Avg Time to Review (min)',
                            data: {!! json_encode($monthlyData['reviewTimes']) !!},
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Average Processing Times'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const minutes = context.raw;
                                    if (minutes >= 60) {
                                        const hours = Math.floor(minutes / 60);
                                        const remainingMinutes = Math.round(minutes % 60);
                                        return `${context.dataset.label}: ${hours}h ${remainingMinutes}m`;
                                    }
                                    return `${context.dataset.label}: ${minutes} min`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Minutes'
                            }
                        }
                    }
                }
            });
            
            // Document Statistics Chart
            const documentStatsCtx = document.getElementById('documentStatsChart').getContext('2d');
            const documentStatsChart = new Chart(documentStatsCtx, {
                type: 'pie',
                data: {
                    labels: ['Forwarded', 'Uploaded'],
                    datasets: [{
                        data: [{{ $averageDocsForwarded }}, {{ $documentsUploaded }}],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(75, 192, 192, 0.6)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Document Distribution'
                        },
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
            @endif
        });
    </script>
@endsection