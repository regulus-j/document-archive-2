@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-white to-slate-50 py-12">
    <div class="mx-auto max-w-3xl px-6 lg:px-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Edit Plan</h2>
        </div>

        @if($errors->any())
            <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <form action="{{ route('plans.update', $plan) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="plan_name" class="block text-sm font-medium text-slate-700">Plan Name</label>
                        <input type="text" name="plan_name" id="plan_name" 
                               class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               value="{{ old('plan_name', $plan->plan_name) }}" required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $plan->description) }}</textarea>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-700">Price</label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-slate-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="price" id="price" step="0.01" min="0"
                                   class="block w-full rounded-md border-slate-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500"
                                   value="{{ old('price', $plan->price) }}" required>
                        </div>
                    </div>

                    <div>
                        <label for="billing_cycle" class="block text-sm font-medium text-slate-700">Billing Cycle</label>
                        <select name="billing_cycle" id="billing_cycle" 
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="custom" {{ old('billing_cycle', $plan->billing_cycle) == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" 
                                    value="1"
                                   class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                   {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-slate-700">Active Plan</label>
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-medium text-slate-900">Plan Features</h3>
                                    <p class="mt-1 text-sm text-slate-500">Update which catalog features are enabled and adjust their plan-specific values.</p>
                                </div>
                            </div>

                            <div class="mt-5 space-y-4">
                                @foreach($features as $feature)
                                    @php
                                        $featureEnabled = old('features.' . $feature->id . '.enabled', data_get($planFeatures, $feature->id . '.enabled'));
                                        $featureValue = old('features.' . $feature->id . '.value', data_get($planFeatures, $feature->id . '.value'));
                                        $isEnabled = filter_var($featureEnabled, FILTER_VALIDATE_BOOLEAN);
                                    @endphp
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm" data-feature-row>
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">
                                                        {{ strtoupper(substr($feature->name, 0, 1)) }}
                                                    </span>
                                                    <div>
                                                        <h4 class="text-base font-semibold text-slate-900">{{ $feature->name }}</h4>
                                                        <p class="text-sm text-slate-500">{{ $feature->description ?: 'No description provided.' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] lg:w-[420px]">
                                                <div>
                                                    <label for="feature_value_{{ $feature->id }}" class="block text-sm font-medium text-slate-700">Custom value / limit</label>
                                                    <input type="text" name="features[{{ $feature->id }}][value]" id="feature_value_{{ $feature->id }}"
                                                        value="{{ $featureValue }}"
                                                        class="mt-1 block w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100"
                                                        placeholder="Example: 10 users, 50 GB, Unlimited"
                                                        data-feature-value
                                                        {{ $isEnabled ? '' : 'disabled' }}>
                                                    @error('features.' . $feature->id . '.value')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                                                    <input type="checkbox" name="features[{{ $feature->id }}][enabled]" value="1"
                                                        {{ $isEnabled ? 'checked' : '' }}
                                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                        data-feature-toggle>
                                                    Enabled
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('plans.show', $plan) }}" 
                       class="inline-flex items-center px-4 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-feature-row]').forEach(function(row) {
            const toggle = row.querySelector('[data-feature-toggle]');
            const valueInput = row.querySelector('[data-feature-value]');

            function syncFeatureState() {
                valueInput.disabled = !toggle.checked;
            }

            toggle.addEventListener('change', syncFeatureState);
            syncFeatureState();
        });
    });
</script>
@endsection
