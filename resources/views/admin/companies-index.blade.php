@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8" x-data="{ activeToolsModal: null }" @keydown.escape.window="activeToolsModal = null">
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
                                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Company Management</h1>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                                    Review companies, owners, subscription state, and assigned plans from one admin screen.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('companies.create') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Legacy Create
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                    <h2 class="text-lg font-semibold text-slate-900">Create New Company</h2>
                    <p class="mb-4 text-sm text-slate-500">Superadmin quick-create flow with owner assignment.</p>
                    <form method="POST" action="{{ route('admin.companies.store') }}" class="grid gap-3 md:grid-cols-5">
                        @csrf
                        <input type="text" name="company_name" required placeholder="Company name" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="text" name="registered_name" required placeholder="Registered name" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="email" name="company_email" placeholder="Email" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <select name="owner_id" required class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select owner</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Create</button>
                    </form>
                </div>

                <div class="grid gap-4 border-b border-slate-200 px-6 py-6 sm:grid-cols-2 lg:grid-cols-3 sm:px-8">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Companies</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $companies->total() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Visible on page</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $companies->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Scope</p>
                        <p class="mt-2 text-sm font-medium text-slate-700">Admin-only company administration</p>
                    </div>
                </div>

                <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                    <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Company directory</h2>
                            <p class="text-sm text-slate-500">Search the list by company name, owner, status, or plan.</p>
                        </div>

                        <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row md:items-center">
                            <div class="relative w-full md:w-80">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..." class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <select name="status" class="rounded-xl border-slate-300 bg-white py-2.5 pl-4 pr-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="no_subscription" {{ request('status') === 'no_subscription' ? 'selected' : '' }}>No subscription</option>
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
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Owner</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Plan</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">End Date</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Auto Renew</th>
                                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($companies as $company)
                                        @php
                                            $latestSub = $company->subscriptions->first();
                                            $subStatus = $latestSub ? $latestSub->status : null;
                                        @endphp
                                        <tr class="transition hover:bg-slate-50">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white shadow-sm">
                                                        {{ substr($company->company_name ?? 'N/A', 0, 1) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-slate-900">{{ $company->company_name }}</div>
                                                        <div class="text-xs text-slate-500">{{ $company->company_email ?? 'No email' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                @if($company->user)
                                                    <div class="text-sm font-medium text-slate-900">{{ $company->user->first_name }} {{ $company->user->last_name }}</div>
                                                    <div class="text-xs text-slate-500">{{ $company->user->email }}</div>
                                                @else
                                                    <span class="text-xs text-slate-400">No owner</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                @if($latestSub && $latestSub->plan)
                                                    <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800">{{ $latestSub->plan->plan_name }}</span>
                                                @else
                                                    <span class="text-xs text-slate-400">No plan</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                @if($latestSub)
                                                    <form method="POST" action="{{ route('admin.subscriptions.status.update', $latestSub->id) }}" class="inline-flex items-center gap-2">
                                                        @csrf
                                                        <select name="status" onchange="this.form.submit()" class="rounded-lg border-slate-300 bg-white px-2.5 py-1.5 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            @foreach(['active', 'pending', 'canceled', 'expired'] as $statusOption)
                                                                <option value="{{ $statusOption }}" {{ $subStatus === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">No subscription</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                                {{ $latestSub && $latestSub->end_date ? \Illuminate\Support\Carbon::parse($latestSub->end_date)->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                @if($latestSub)
                                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold leading-tight {{ $latestSub->auto_renew ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700' }}">
                                                        {{ $latestSub->auto_renew ? 'Yes' : 'No' }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium">
                                                <button type="button" @click="activeToolsModal = {{ $company->id }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                    Tools
                                                </button>

                                                <div x-show="activeToolsModal === {{ $company->id }}" x-transition class="fixed inset-0 z-50" style="display: none;">
                                                    <div class="absolute inset-0 bg-slate-900/50" @click="activeToolsModal = null"></div>
                                                    <div class="absolute inset-0 flex items-center justify-center p-4">
                                                        <div class="w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-2xl" x-data="{ activeToolsTab: 'details' }">
                                                            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                                                                <div>
                                                                    <h3 class="text-sm font-semibold text-slate-900">Company Tools</h3>
                                                                    <p class="text-xs text-slate-500">{{ $company->company_name }}</p>
                                                                </div>
                                                                <button type="button" @click="activeToolsModal = null" class="rounded-md px-2 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100">Close</button>
                                                            </div>

                                                            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                                                                <div class="flex flex-wrap gap-2">
                                                                    <button type="button" @click="activeToolsTab = 'details'" :class="activeToolsTab === 'details' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'" class="rounded-full px-3 py-1.5 text-xs font-semibold border border-slate-200 transition">Company</button>
                                                                    <button type="button" @click="activeToolsTab = 'members'" :class="activeToolsTab === 'members' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'" class="rounded-full px-3 py-1.5 text-xs font-semibold border border-slate-200 transition">Members</button>
                                                                    <button type="button" @click="activeToolsTab = 'subscription'" :class="activeToolsTab === 'subscription' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'" class="rounded-full px-3 py-1.5 text-xs font-semibold border border-slate-200 transition">Subscription</button>
                                                                </div>
                                                            </div>

                                                            <div class="max-h-[68vh] overflow-y-auto p-4">
                                                                <div x-show="activeToolsTab === 'details'" x-transition class="space-y-3" style="display: none;">
                                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Company Details</p>
                                                                        <form method="POST" action="{{ route('admin.companies.details.update', $company->id) }}" class="grid grid-cols-2 gap-2">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <input type="text" name="company_name" value="{{ $company->company_name }}" required class="rounded-lg border-slate-300 text-xs">
                                                                            <input type="text" name="registered_name" value="{{ $company->registered_name }}" required class="rounded-lg border-slate-300 text-xs">
                                                                            <input type="email" name="company_email" value="{{ $company->company_email }}" class="rounded-lg border-slate-300 text-xs">
                                                                            <input type="text" name="company_phone" value="{{ $company->company_phone }}" class="rounded-lg border-slate-300 text-xs">
                                                                            <select name="owner_id" required class="col-span-2 rounded-lg border-slate-300 text-xs">
                                                                                @foreach($availableUsers as $user)
                                                                                    <option value="{{ $user->id }}" {{ (int) $company->user_id === (int) $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <button type="submit" class="col-span-2 rounded-lg bg-slate-900 px-2 py-1.5 text-xs font-semibold text-white">Save Company Details</button>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                                <div x-show="activeToolsTab === 'members'" x-transition class="space-y-3" style="display: none;">
                                                                    <div class="grid gap-3 lg:grid-cols-2">
                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Add Existing Member</p>
                                                                            <form method="POST" action="{{ route('admin.companies.members.add', $company->id) }}" class="flex items-center gap-2">
                                                                                @csrf
                                                                                <select name="user_id" required class="flex-1 rounded-lg border-slate-300 text-xs">
                                                                                    <option value="">Choose user</option>
                                                                                    @foreach($availableUsers as $user)
                                                                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <button type="submit" class="rounded-lg bg-indigo-600 px-2 py-1.5 text-xs font-semibold text-white">Add</button>
                                                                            </form>
                                                                        </div>

                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Create User and Add</p>
                                                                            <form method="POST" action="{{ route('admin.companies.members.create', $company->id) }}" class="grid grid-cols-2 gap-2">
                                                                                @csrf
                                                                                <input type="text" name="first_name" placeholder="First name" required class="rounded-lg border-slate-300 text-xs">
                                                                                <input type="text" name="last_name" placeholder="Last name" required class="rounded-lg border-slate-300 text-xs">
                                                                                <input type="email" name="email" placeholder="Email" required class="col-span-2 rounded-lg border-slate-300 text-xs">
                                                                                <input type="password" name="password" placeholder="Password" required class="col-span-2 rounded-lg border-slate-300 text-xs">
                                                                                <button type="submit" class="col-span-2 rounded-lg bg-indigo-600 px-2 py-1.5 text-xs font-semibold text-white">Create User and Add</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                    <div class="rounded-xl border border-slate-200 bg-white p-3">
                                                                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Current Members</p>
                                                                        <div class="space-y-2">
                                                                            @foreach($company->users as $member)
                                                                                <div class="flex items-center justify-between rounded border border-slate-200 px-3 py-2">
                                                                                    <span class="text-xs text-slate-700">{{ $member->first_name }} {{ $member->last_name }}</span>
                                                                                    @if((int) $member->id !== (int) $company->user_id)
                                                                                        <form method="POST" action="{{ route('admin.companies.members.remove', [$company->id, $member->id]) }}">
                                                                                            @csrf
                                                                                            @method('DELETE')
                                                                                            <button type="submit" class="text-xs font-semibold text-rose-600">Remove</button>
                                                                                        </form>
                                                                                    @else
                                                                                        <span class="text-[10px] font-semibold text-indigo-600">Owner</span>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div x-show="activeToolsTab === 'subscription'" x-transition class="space-y-3" style="display: none;">
                                                                    <div class="grid gap-3 lg:grid-cols-2">
                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Change Plan</p>
                                                                            <form method="POST" action="{{ route('admin.companies.subscription.plan.update', $company->id) }}" class="flex items-center gap-2">
                                                                                @csrf
                                                                                <select name="plan_id" required class="flex-1 rounded-lg border-slate-300 text-xs">
                                                                                    @foreach($plans as $plan)
                                                                                        <option value="{{ $plan->id }}" {{ $latestSub && (int) $latestSub->plan_id === (int) $plan->id ? 'selected' : '' }}>{{ $plan->plan_name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <button type="submit" class="rounded-lg bg-indigo-600 px-2 py-1.5 text-xs font-semibold text-white">Set Plan</button>
                                                                            </form>
                                                                        </div>

                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Manual Renewal</p>
                                                                            <form method="POST" action="{{ route('admin.companies.subscription.renew', $company->id) }}" class="grid grid-cols-2 gap-2">
                                                                                @csrf
                                                                                <select name="plan_id" required class="col-span-2 rounded-lg border-slate-300 text-xs">
                                                                                    @foreach($plans as $plan)
                                                                                        <option value="{{ $plan->id }}">{{ $plan->plan_name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <input type="date" name="start_date" value="{{ now()->toDateString() }}" required class="rounded-lg border-slate-300 text-xs">
                                                                                <input type="date" name="end_date" value="{{ now()->addMonth()->toDateString() }}" required class="rounded-lg border-slate-300 text-xs">
                                                                                <label class="col-span-2 inline-flex items-center gap-2 text-xs text-slate-700">
                                                                                    <input type="checkbox" name="auto_renew" value="1" class="rounded border-slate-300"> Auto-renew
                                                                                </label>
                                                                                <button type="submit" class="col-span-2 rounded-lg bg-emerald-600 px-2 py-1.5 text-xs font-semibold text-white">Manual Renewal</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-14 text-center">
                                                <div class="mx-auto max-w-sm space-y-2">
                                                    <p class="text-base font-semibold text-slate-900">No companies found</p>
                                                    <p class="text-sm text-slate-500">Try adjusting the search or status filter, or create a new company record.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($companies->hasPages())
                        <div class="mt-6">
                            {{ $companies->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
