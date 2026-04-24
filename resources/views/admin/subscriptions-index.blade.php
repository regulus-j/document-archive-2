@extends('layouts.app')

@section('content')
    <div class="bg-gradient-to-b from-indigo-50 to-white min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-6">Subscription Management</h1>

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

            <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-indigo-100">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        Company</th>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        Plan</th>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        Status</th>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        Start Date</th>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        End Date</th>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        Auto Renew</th>
                                    <th
                                        class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($subscriptions as $subscription)
                                    <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                                                    {{ substr($subscription->company->company_name ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-slate-900">
                                                        {{ $subscription->company->company_name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold leading-tight text-indigo-700 bg-indigo-100 rounded-full">
                                                {{ $subscription->plan->plan_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full 
                                                        @if($subscription->status === 'active') bg-emerald-100 text-emerald-800
                                                        @elseif($subscription->status === 'pending') bg-amber-100 text-amber-800
                                                        @elseif($subscription->status === 'canceled') bg-rose-100 text-rose-800
                                                            @else bg-slate-100/50 text-slate-800
                                                        @endif">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                            {{ \Illuminate\Support\Carbon::parse($subscription->start_date)->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                            {{ $subscription->end_date ? \Illuminate\Support\Carbon::parse($subscription->end_date)->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold leading-tight rounded-full
                                                        {{ $subscription->auto_renew ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100/50 text-slate-800' }}">
                                                {{ $subscription->auto_renew ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" action="{{ route('admin.subscriptions.status.update', $subscription->id) }}" class="flex items-center gap-2">
                                                @csrf
                                                <select name="status" class="rounded-md border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                    @foreach(['active', 'pending', 'canceled', 'expired'] as $statusOption)
                                                        <option value="{{ $statusOption }}" {{ $subscription->status === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">
                                                    Save
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-slate-500">
                                            No subscriptions found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($subscriptions->hasPages())
                <div class="mt-4 px-6 pb-4">
                    {{ $subscriptions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
