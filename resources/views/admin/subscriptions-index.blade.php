@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="ds-alert-success mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="ds-alert-error mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-200 bg-white px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl space-y-3">
                            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700 shadow-sm">
                                Super Admin
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Subscription Management</h1>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review subscriptions, search companies and plans, and update subscription statuses.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                    <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Subscription directory</h2>
                            <p class="text-sm text-slate-500">Search by company, plan, or status.</p>
                        </div>

                        <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                            <div class="relative w-full md:w-80">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscriptions..." class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <select name="status" class="rounded-xl border-slate-300 bg-white py-2.5 pl-4 pr-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All statuses</option>
                                @foreach(['active', 'pending', 'canceled', 'expired'] as $statusOption)
                                    <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Filter</button>

                            @if(request()->filled('search') || request()->filled('status'))
                                <a href="{{ url()->current() }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Company</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Plan</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Start Date</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">End Date</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Auto Renew</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($subscriptions as $subscription)
                                        <tr class="transition hover:bg-slate-50">
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white shadow-sm">
                                                        {{ substr($subscription->company->company_name ?? 'N/A', 0, 1) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-slate-900">{{ $subscription->company->company_name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800">{{ $subscription->plan->plan_name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold leading-tight {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($subscription->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($subscription->status === 'canceled' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700')) }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ \Illuminate\Support\Carbon::parse($subscription->start_date)->format('Y-m-d') }}</td>
                                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">{{ $subscription->end_date ? \Illuminate\Support\Carbon::parse($subscription->end_date)->format('Y-m-d') : 'N/A' }}</td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold leading-tight {{ $subscription->auto_renew ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ $subscription->auto_renew ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium">
                                                <form method="POST" action="{{ route('admin.subscriptions.status.update', $subscription->id) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    <select name="status" class="ds-input px-2.5 py-1.5 text-xs">
                                                        @foreach(['active', 'pending', 'canceled', 'expired'] as $statusOption)
                                                            <option value="{{ $statusOption }}" {{ $subscription->status === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="ds-btn-primary px-2.5 py-1.5 text-xs">Save</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-14 text-center">
                                                <div class="mx-auto max-w-sm space-y-2">
                                                    <p class="text-base font-semibold text-slate-900">No subscriptions found</p>
                                                    <p class="text-sm text-slate-500">Try adjusting the search or status filter.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($subscriptions->hasPages())
                        <div class="mt-6">
                            {{ $subscriptions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
