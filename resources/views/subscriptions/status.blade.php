<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription Status') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 bg-white border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg shadow-md"
                    role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-white border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Box -->
            <div class="bg-white rounded-xl shadow-xl mb-6 border border-blue-100 overflow-hidden">
                <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Subscription Status</h1>
                            <p class="text-sm text-gray-500">Manage your subscription and plan details</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Current Subscription Information -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 h-full">
                        <div class="bg-white p-6 border-b border-blue-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-800">Current Subscription</h2>
                            </div>
                        </div>

                        <div class="p-6">
                            @if($subscription)
                                                        <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                <div>
                                                                    <p class="text-sm text-gray-600">Plan Name</p>
                                                                    <p class="font-medium text-gray-800 text-lg">
                                                                        {{ $subscription->plan->plan_name ?? 'N/A' }}
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-sm text-gray-600">Status</p>
                                                                    <span
                                                                        class="px-2.5 py-1 text-xs font-medium rounded-full 
                                                                            {{ $subscription->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                                        {{ ucfirst($subscription->status ?? 'unknown') }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <p class="text-sm text-gray-600">Start Date</p>
                                                                    <p class="font-medium text-gray-800">
                                                                        @if($subscription->start_date)
                                                                            @if(is_string($subscription->start_date))
                                                                                {{ \Carbon\Carbon::parse($subscription->start_date)->format('M d, Y') }}
                                                                            @else
                                                                                {{ $subscription->start_date->format('M d, Y') }}
                                                                            @endif
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-sm text-gray-600">End Date</p>
                                                                    <p class="font-medium text-gray-800">
                                                                        @if($subscription->end_date)
                                                                            @if(is_string($subscription->end_date))
                                                                                {{ \Carbon\Carbon::parse($subscription->end_date)->format('M d, Y') }}
                                                                            @else
                                                                                {{ $subscription->end_date->format('M d, Y') }}
                                                                            @endif
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-sm text-gray-600">Auto Renewal</p>
                                                                    <p class="font-medium text-gray-800">
                                                                        @if($subscription->auto_renew ?? false)
                                                                            <span class="text-emerald-600 flex items-center">
                                                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path fill-rule="evenodd"
                                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                        clip-rule="evenodd"></path>
                                                                                </svg>
                                                                                Enabled
                                                                            </span>
                                                                        @else
                                                                            <span class="text-rose-600 flex items-center">
                                                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path fill-rule="evenodd"
                                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 101.414 1.414L10 11.414l1.293 1.293a1 1 001.414-1.414L11.414 10l1.293-1.293a1 1 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                                        clip-rule="evenodd"></path>
                                                                                </svg>
                                                                                Disabled
                                                                            </span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="mt-6">
                                                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mr-1"
                                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Plan Features
                                                                </h4>
                                                                <ul class="list-disc pl-5 text-gray-700 space-y-1">
                                                                    @php
                                                                        $features = [];
                                                                        if ($subscription->plan && isset($subscription->plan->features)) {
                                                                            $decodedFeatures = json_decode($subscription->plan->features);
                                                                            $features = is_array($decodedFeatures) ? $decodedFeatures : [];
                                                                        }
                                                                    @endphp

                                                                    @if(count($features) > 0)
                                                                        @foreach($features as $feature)
                                                                            <li>
                                                                                @if(is_string($feature))
                                                                                    {{ $feature }}
                                                                                @elseif(is_object($feature))
                                                                                    {{ $feature->name ?? json_encode($feature) }}
                                                                                @else
                                                                                    {{ json_encode($feature) }}
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    @else
                                                                        <li>{{ is_string($subscription->plan->features ?? '') ? $subscription->plan->features : 'Basic features' }}
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>

                                                            <div class="mt-6 pt-4 border-t border-blue-200">
                                                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mr-1"
                                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    </svg>
                                                                    Subscription Management
                                                                </h4>

                                                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                                                                    @if($subscription->status === 'active')
                                                                        <form action="{{ route('subscriptions.request-cancellation') }}" method="POST"
                                                                            class="inline">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="px-4 py-2 border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-sm transition duration-200 shadow-sm"
                                                                                onclick="return confirm('Are you sure you want to request cancellation of your subscription?')">
                                                                                <span class="flex items-center">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                                    </svg>
                                                                                    Request Cancellation
                                                                                </span>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                            @else
                                <div class="bg-blue-50 rounded-lg p-6 border border-blue-100 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400 mx-auto mb-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-gray-700">No active subscription found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Upgrade Options -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 h-full">
                        <div class="bg-white p-6 border-b border-blue-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-800">Available Plan Upgrades</h2>
                            </div>
                        </div>

                        <div class="p-6">
                            @if(isset($availablePlans) && count($availablePlans) > 0)
                                <div class="space-y-4">
                                    @foreach($availablePlans as $plan)
                                        <div
                                            class="border border-blue-100 rounded-lg p-4 hover:shadow-md transition duration-200 bg-blue-50">
                                            <h4 class="font-semibold text-gray-800">{{ $plan->name ?? 'No name' }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $plan->description ?? 'No description available' }}
                                            </p>
                                            <p class="mt-2 text-blue-600 font-medium">${{ number_format($plan->price ?? 0, 2) }}
                                                / {{ $plan->billing_period ?? 'month' }}</p>

                                            <div class="mt-3">
                                                <form action="{{ route('subscriptions.request-upgrade') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                    <button type="submit"
                                                        class="w-full px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm rounded-lg shadow-sm transition duration-200">
                                                        <span class="flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                            </svg>
                                                            Request Upgrade
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-blue-50 rounded-lg p-6 border border-blue-100 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400 mx-auto mb-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-700">No upgrade options available at this time.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>