@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.16),_transparent_35%),linear-gradient(180deg,_rgba(15,23,42,0.98),_rgba(248,250,252,1)_20%)] py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700 shadow-sm">
                    Plan Builder
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Create New Plan</h1>
                <p class="text-sm leading-6 text-slate-600">
                    Build a plan like a modern SaaS billing surface: define the product once, then enable features with their own limits or notes.
                </p>
            </div>

            <a href="{{ route('plans.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Plans
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50/90 px-4 py-4 text-sm text-rose-700 shadow-sm">
                <div class="font-semibold">Whoops! Something went wrong.</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="space-y-4 xl:sticky xl:top-8 xl:h-fit">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Summary</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">Plan setup</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Keep the form focused: define the plan, then tune the feature rows like a pricing matrix.
                    </p>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Selected</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900" data-selected-count>0</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Available</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $features->count() }}</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 text-sm text-indigo-900">
                        <p class="font-semibold">Pattern used here</p>
                        <p class="mt-1 leading-6 text-indigo-900/80">
                            Inspired by SaaS billing builders: each feature is an entitlement, not a static checkbox row.
                        </p>
                    </div>

                    <div class="mt-6 space-y-2 text-sm text-slate-600">
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span>Plan status</span>
                            <span class="font-semibold text-slate-900">Active toggle below</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span>Billing cycle</span>
                            <span class="font-semibold text-slate-900">Monthly / yearly / custom</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span>Feature style</span>
                            <span class="font-semibold text-slate-900">Toggle + value</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tips</p>
                    <ul class="mt-3 space-y-3 text-sm leading-6 text-slate-600">
                        <li>Keep values short and human-readable, like “10 users” or “50 GB”.</li>
                        <li>Leave a feature disabled if the plan should not expose it at all.</li>
                        <li>Use feature search to jump straight to the item you want to tune.</li>
                    </ul>
                </div>
            </aside>

            <form action="{{ route('plans.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                        <h2 class="text-lg font-semibold text-slate-900">Plan details</h2>
                        <p class="mt-1 text-sm text-slate-500">These fields define the product card and subscription framing.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 px-6 py-6 sm:px-8 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="plan_name" class="block text-sm font-medium text-slate-700">Plan Name</label>
                            <input type="text" name="plan_name" id="plan_name" value="{{ old('plan_name') }}"
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Growth Plan"
                                required>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="A short description customers will understand at a glance.">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-slate-700">Price</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price') }}"
                                    class="block w-full rounded-xl border-slate-300 pl-7 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <div>
                            <label for="billing_cycle" class="block text-sm font-medium text-slate-700">Billing Cycle</label>
                            <select name="billing_cycle" id="billing_cycle"
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                                <option value="">Select Billing Cycle</option>
                                <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="custom" {{ old('billing_cycle') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Active Plan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Plan Features</h2>
                                <p class="mt-1 text-sm text-slate-500">Search the catalog, enable a feature, and assign its plan-specific value or limit.</p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <div class="relative w-full sm:w-72">
                                    <input type="text" id="featureSearch" placeholder="Search features..."
                                        class="w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" data-select-all class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                        Select all
                                    </button>
                                    <button type="button" data-clear-all class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-6 sm:px-8">
                        <div class="grid gap-4" data-feature-list>
                            @foreach($features as $feature)
                                @php
                                    $featureConfig = old('features.' . $feature->id, []);
                                    $isEnabled = filter_var(data_get($featureConfig, 'enabled', false), FILTER_VALIDATE_BOOLEAN);
                                    $featureValue = data_get($featureConfig, 'value', '');
                                @endphp
                                <div class="group rounded-2xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm transition hover:border-indigo-200 hover:bg-white" data-feature-row data-feature-searchable>
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0 flex-1 space-y-2">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-500 text-sm font-bold text-white shadow-sm">
                                                    {{ strtoupper(substr($feature->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <h3 class="truncate text-base font-semibold text-slate-900" data-feature-name>{{ $feature->name }}</h3>
                                                    <p class="text-sm text-slate-500" data-feature-description>{{ $feature->description ?: 'No description provided.' }}</p>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                                <span class="rounded-full bg-white px-2.5 py-1 shadow-sm">Feature</span>
                                                <span class="rounded-full bg-white px-2.5 py-1 shadow-sm">Custom value supported</span>
                                            </div>
                                        </div>

                                        <div class="grid gap-3 lg:w-[420px]">
                                            <label class="inline-flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                                                <span>Enabled</span>
                                                <input type="checkbox" name="features[{{ $feature->id }}][enabled]" value="1"
                                                    {{ $isEnabled ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                    data-feature-toggle>
                                            </label>

                                            <div>
                                                <label for="feature_value_{{ $feature->id }}" class="block text-sm font-medium text-slate-700">Value or limit</label>
                                                <input type="text" name="features[{{ $feature->id }}][value]" id="feature_value_{{ $feature->id }}"
                                                    value="{{ $featureValue }}"
                                                    class="mt-1 block w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100"
                                                    placeholder="Examples: 10 users, 50 GB, Unlimited"
                                                    data-feature-value
                                                    {{ $isEnabled ? '' : 'disabled' }}>
                                                @error('features.' . $feature->id . '.value')
                                                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pb-4">
                    <button type="button" onclick="window.location='{{ route('plans.index') }}'"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Create Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const featureRows = Array.from(document.querySelectorAll('[data-feature-row]'));
        const selectedCount = document.querySelector('[data-selected-count]');
        const searchInput = document.getElementById('featureSearch');
        const selectAllButton = document.querySelector('[data-select-all]');
        const clearAllButton = document.querySelector('[data-clear-all]');

        function updateSelectedCount() {
            if (!selectedCount) {
                return;
            }

            selectedCount.textContent = featureRows.filter(function(row) {
                const toggle = row.querySelector('[data-feature-toggle]');
                return toggle && toggle.checked;
            }).length;
        }

        function syncFeatureState(row) {
            const toggle = row.querySelector('[data-feature-toggle]');
            const valueInput = row.querySelector('[data-feature-value]');

            if (!toggle || !valueInput) {
                return;
            }

            valueInput.disabled = !toggle.checked;
        }

        function filterFeatures() {
            const query = (searchInput?.value || '').toLowerCase().trim();

            featureRows.forEach(function(row) {
                const name = (row.querySelector('[data-feature-name]')?.textContent || '').toLowerCase();
                const description = (row.querySelector('[data-feature-description]')?.textContent || '').toLowerCase();
                const matches = !query || name.includes(query) || description.includes(query);
                row.style.display = matches ? '' : 'none';
            });
        }

        featureRows.forEach(function(row) {
            const toggle = row.querySelector('[data-feature-toggle]');

            if (!toggle) {
                return;
            }

            toggle.addEventListener('change', function() {
                syncFeatureState(row);
                updateSelectedCount();
            });

            syncFeatureState(row);
        });

        if (searchInput) {
            searchInput.addEventListener('input', filterFeatures);
        }

        if (selectAllButton) {
            selectAllButton.addEventListener('click', function() {
                featureRows.forEach(function(row) {
                    const toggle = row.querySelector('[data-feature-toggle]');
                    if (toggle) {
                        toggle.checked = true;
                        syncFeatureState(row);
                    }
                });
                updateSelectedCount();
            });
        }

        if (clearAllButton) {
            clearAllButton.addEventListener('click', function() {
                featureRows.forEach(function(row) {
                    const toggle = row.querySelector('[data-feature-toggle]');
                    if (toggle) {
                        toggle.checked = false;
                        syncFeatureState(row);
                    }
                });
                updateSelectedCount();
            });
        }

        updateSelectedCount();
    });
</script>
@endsection