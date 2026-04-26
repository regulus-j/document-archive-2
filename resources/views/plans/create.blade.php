@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-8 sm:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Create New Plan</h1>
                <p class="mt-1 text-sm text-slate-500">Define a billing plan and attach configurable feature entitlements.</p>
            </div>

            <a href="{{ route('plans.index') }}" class="ds-btn-secondary gap-2 inline-flex">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Plans
            </a>
        </div>

        @if ($errors->any())
            <div class="ds-alert-error mb-6">
                <div class="font-semibold">Whoops! Something went wrong.</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Layout: Sidebar (Desktop) + Form -->
        <div class="grid gap-6 lg:grid-cols-12">
            <!-- Sidebar - Hidden on mobile, sticky on desktop -->
            <aside class="hidden space-y-4 lg:block lg:col-span-3 lg:sticky lg:top-8 lg:h-fit">
                <div class="ds-card p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Summary</p>
                    <h2 class="mt-2 text-lg font-bold text-slate-900">Plan setup</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">A compact pricing-builder layout keeps the plan details and feature rows scannable.</p>

                    <div class="mt-5 grid grid-cols-1 gap-3">
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 whitespace-nowrap">Selected</p>
                            <p class="mt-1.5 text-xl font-bold text-slate-900" data-selected-count>0</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 whitespace-nowrap">Available</p>
                            <p class="mt-1.5 text-xl font-bold text-slate-900">{{ $features->count() }}</p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900">
                        Set the plan once, then define each enabled feature with a numeric limit and a unit that comes from the feature itself.
                    </div>
                </div>

                <div class="ds-card p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tips</p>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                        <li>Use short, customer-friendly values.</li>
                        <li>Disable features that should not appear on the plan.</li>
                        <li>Search features before scanning the full list.</li>
                    </ul>
                </div>
            </aside>

            <!-- Form Container -->
            <form action="{{ route('plans.store') }}" method="POST" class="space-y-6 lg:col-span-9">
                @csrf

                <div class="ds-card overflow-hidden">
                    <div class="ds-card-header">
                        <h2 class="text-lg font-semibold text-slate-900">Plan details</h2>
                        <p class="mt-1 text-sm text-slate-500">These fields define the billing plan itself.</p>
                    </div>

                    <div class="ds-card-body grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="plan_name" class="ds-label">Plan Name</label>
                            <input type="text" name="plan_name" id="plan_name" value="{{ old('plan_name') }}" placeholder="Growth Plan" required
                                class="ds-input px-3 py-2.5">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="ds-label">Description</label>
                            <textarea name="description" id="description" rows="4" placeholder="A short description customers will understand at a glance."
                                class="ds-input px-3 py-2.5">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="price" class="ds-label">Price</label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required
                                    class="ds-input pl-7 pr-3 py-2.5">
                            </div>
                        </div>

                        <div>
                            <label for="billing_cycle" class="ds-label">Billing Cycle</label>
                            <select name="billing_cycle" id="billing_cycle" required
                                class="ds-input px-3 py-2.5">
                                <option value="">Select Billing Cycle</option>
                                <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="custom" {{ old('billing_cycle') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="inline-flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600">
                                Active Plan
                            </label>
                        </div>
                    </div>
                </div>

                <div class="ds-card overflow-hidden">
                    <div class="ds-card-header">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg font-semibold text-slate-900">Plan Features</h2>
                                <p class="mt-1 text-sm text-slate-500">Search the catalog and set each feature’s enabled state and value.</p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <div class="relative w-full sm:w-72">
                                    <input type="text" id="featureSearch" placeholder="Search features..."
                                        class="ds-input h-11 pl-10 pr-4 py-2.5 w-full text-left">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button" data-select-all class="ds-btn-secondary whitespace-nowrap">Select all</button>
                                    <button type="button" data-clear-all class="ds-btn-secondary whitespace-nowrap">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ds-card-body">
                        <div class="grid gap-4" data-feature-list>
                            @foreach($features as $feature)
                                @php
                                    $featureConfig = old('features.' . $feature->id, []);
                                    $isEnabled = filter_var(data_get($featureConfig, 'enabled', false), FILTER_VALIDATE_BOOLEAN);
                                    $featureAmount = data_get($featureConfig, 'amount', '');
                                    $unitLabel = $feature->unit_label ?: 'units';
                                @endphp
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm transition hover:border-indigo-200 hover:bg-white" data-feature-row data-feature-searchable>
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0 flex-1 space-y-2">
                                            <div class="min-w-0">
                                                <h3 class="text-base font-semibold text-slate-900" data-feature-name>{{ $feature->name }}</h3>
                                                <p class="text-sm text-slate-500" data-feature-description>{{ $feature->description ?: 'No description provided.' }}</p>
                                            </div>
                                        </div>

                                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] lg:w-[520px]">
                                            <div>
                                                <label for="feature_amount_{{ $feature->id }}" class="ds-label">Amount</label>
                                                <div class="mt-1 flex gap-2">
                                                    <input type="number" name="features[{{ $feature->id }}][amount]" id="feature_amount_{{ $feature->id }}" value="{{ $featureAmount }}"
                                                        min="0" step="1"
                                                        placeholder="0"
                                                        class="ds-input px-3 py-2.5 disabled:bg-slate-100 flex-1"
                                                        data-feature-amount {{ $isEnabled ? '' : 'disabled' }}>
                                                </div>
                                                @error('features.' . $feature->id . '.amount')
                                                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="feature_unit_label_{{ $feature->id }}" class="ds-label">Unit label</label>
                                                <input type="text" name="features[{{ $feature->id }}][unit_label]" id="feature_unit_label_{{ $feature->id }}"
                                                    value="{{ old('features.' . $feature->id . '.unit_label', $feature->unit_label) }}"
                                                    placeholder="{{ $unitLabel }}"
                                                    class="ds-input px-3 py-2.5">
                                            </div>

                                            <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                                                <input type="checkbox" name="features[{{ $feature->id }}][enabled]" value="1" {{ $isEnabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600" data-feature-toggle>
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pb-4">
                    <button type="button" onclick="window.location='{{ route('plans.index') }}'" class="ds-btn-secondary">Cancel</button>
                    <button type="submit" class="ds-btn-primary">Create Plan</button>
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
            const amountInput = row.querySelector('[data-feature-amount]');

            if (!toggle || !amountInput) {
                return;
            }

            amountInput.disabled = !toggle.checked;
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