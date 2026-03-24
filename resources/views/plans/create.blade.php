@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-100 to-slate-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6 sm:p-10">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-4 sm:mb-0">
                        Create New Plan
                    </h1>
                    <a href="{{ route('plans.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-slate-200 border border-transparent rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-300 active:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Plans
                    </a>
                </div>

                @if ($errors->any())
                    <div class="mb-6">
                        <div class="font-medium text-red-600">
                            {{ __('Whoops! Something went wrong.') }}
                        </div>
                        <div class="mt-3 bg-red-50 p-4 rounded-md">
                            <div class="text-sm text-red-600">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('plans.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="plan_name" class="block text-sm font-medium text-slate-700">Plan Name</label>
                            <input type="text" name="plan_name" id="plan_name" value="{{ old('plan_name') }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-slate-700">Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-slate-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price') }}"
                                    class="mt-1 block w-full pl-7 rounded-md border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <div>
                            <label for="billing_cycle" class="block text-sm font-medium text-slate-700">Billing Cycle</label>
                            <select name="billing_cycle" id="billing_cycle"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                                <option value="">Select Billing Cycle</option>
                                <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="custom" {{ old('billing_cycle') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm font-medium text-slate-700">
                                    Active Plan
                                </label>
                            </div>
                        </div>

                        <div class="sm:col-span-2 border-t border-slate-200 pt-6">
                            <h3 class="text-lg font-medium text-slate-900">Plan Features</h3>
                            <p class="mt-1 text-sm text-slate-500">Select the features included in this plan</p>
                            
                            <div class="mt-4 space-y-4">
                                @foreach($features as $feature)
                                <div class="flex items-center">
                                    <input type="checkbox" name="features[]" id="feature_{{ $feature->id }}" value="{{ $feature->id }}" {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                    <label for="feature_{{ $feature->id }}" class="ml-2 block text-sm font-medium text-slate-700">
                                        {{ $feature->name }}
                                    </label>
                                    @if($feature->description)
                                    <span class="ml-2 text-xs text-slate-500">{{ $feature->description }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="pt-5">
                        <div class="flex justify-end">
                            <button type="button" onclick="window.location='{{ route('plans.index') }}'"
                                class="bg-white py-2 px-4 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Plan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection