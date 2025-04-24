@extends('layouts.app')

@section('content')
    <div class="bg-gradient-to-b from-blue-50 to-white min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscription Management</h1>

            <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-blue-100">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        Company</th>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        Plan</th>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        Status</th>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        Start Date</th>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        End Date</th>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        Auto Renew</th>
                                    <th
                                        class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($subscriptions as $subscription)
                                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                    {{ substr($subscription->company->company_name ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $subscription->company->company_name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">
                                                {{ $subscription->plan->plan_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full 
                                                        @if($subscription->status === 'active') bg-emerald-100 text-emerald-800
                                                        @elseif($subscription->status === 'pending') bg-amber-100 text-amber-800
                                                        @elseif($subscription->status === 'canceled') bg-rose-100 text-rose-800
                                                            @else bg-gray-100 text-gray-800
                                                        @endif">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ is_string($subscription->start_date) ? $subscription->start_date : ($subscription->start_date ? $subscription->start_date->format('Y-m-d') : 'N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ is_string($subscription->end_date) ? $subscription->end_date : ($subscription->end_date ? $subscription->end_date->format('Y-m-d') : 'N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold leading-tight rounded-full
                                                        {{ $subscription->auto_renew ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $subscription->auto_renew ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if($subscription->status !== 'active')
                                                    <button onclick="activateSubscription({{ $subscription->id }})"
                                                        class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-md hover:bg-emerald-100 transition-colors">
                                                        Activate
                                                    </button>
                                                @endif
                                                @if($subscription->status !== 'canceled')
                                                    <button onclick="cancelSubscription({{ $subscription->id }})"
                                                        class="px-3 py-1 bg-rose-50 text-rose-700 rounded-md hover:bg-rose-100 transition-colors">
                                                        Cancel
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No subscriptions found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function activateSubscription(id) {
            if (confirm('Are you sure you want to activate this subscription?')) {
                fetch(`/subscriptions/${id}/activate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    }
                });
            }
        }

        function cancelSubscription(id) {
            if (confirm('Are you sure you want to cancel this subscription?')) {
                fetch(`/subscriptions/${id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    }
                });
            }
        }
    </script>
@endsection