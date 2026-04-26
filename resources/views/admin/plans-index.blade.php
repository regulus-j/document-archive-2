@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-200 bg-white px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl space-y-3">
                            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700 shadow-sm">
                                Super Admin
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Subscription Plans</h1>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Search plans, filter by status, and review enabled features.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('plans.create') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add New Plan
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                    <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Plan directory</h2>
                            <p class="text-sm text-slate-500">Search by plan name, description, billing cycle, or feature.</p>
                        </div>

                        <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                            <div class="relative w-full md:w-80">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search plans..." class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <select name="status" class="rounded-xl border-slate-300 bg-white py-2.5 pl-4 pr-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">No</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Plan Name</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Description</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Price</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Billing Cycle</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Features</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</th>
                                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($plans as $plan)
                                        <tr class="transition hover:bg-slate-50" data-plan-row data-plan-status="{{ $plan->is_active ? 'active' : 'inactive' }}">
                                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-500">{{ $loop->iteration }}</td>
                                            <td class="px-5 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white shadow-sm">
                                                        {{ substr($plan->plan_name, 0, 1) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-slate-900">{{ $plan->plan_name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="max-w-xs px-5 py-4 text-sm text-slate-600 truncate">{{ $plan->description }}</td>
                                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-900">P{{ number_format($plan->price / 100, 2) }}</td>
                                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ $plan->billing_cycle }}</td>
                                            <td class="px-5 py-4">
                                                @php
                                                    $enabledFeatures = $plan->features->filter(function ($feature) {
                                                        return (bool) $feature->pivot->enabled;
                                                    });
                                                @endphp

                                                @if($enabledFeatures->isNotEmpty())
                                                    <ul class="max-w-sm space-y-1 text-xs text-slate-600">
                                                        @foreach($enabledFeatures as $feature)
                                                            <li class="flex items-start gap-2">
                                                                <span class="mt-1.5 h-1.5 w-1.5 flex-none rounded-full bg-indigo-500"></span>
                                                                <span class="min-w-0">
                                                                    <span class="font-medium text-slate-800">{{ $feature->name }}</span>
                                                                    @if($feature->pivot->amount !== null)
                                                                        <span class="text-slate-500">- {{ $feature->pivot->amount }} {{ $feature->unit_label ?: 'units' }}</span>
                                                                    @endif
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-xs text-slate-400">No features enabled</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                @if($plan->is_active)
                                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Active</span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium">
                                                <div class="inline-flex items-center gap-2">
                                                    <a href="{{ route('plans.show', $plan->id) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                                        View
                                                    </a>
                                                    <a href="{{ route('plans.edit', $plan->id) }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-14 text-center">
                                                <div class="mx-auto max-w-sm space-y-2">
                                                    <p class="text-base font-semibold text-slate-900">No plans found</p>
                                                    <p class="text-sm text-slate-500">Try adjusting the search or status filter, or create a new plan.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($plans->hasPages())
                        <div class="mt-6">
                            {{ $plans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
