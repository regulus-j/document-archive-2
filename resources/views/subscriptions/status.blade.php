<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-200 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row">
                        <!-- Current Subscription Information -->
                        <div class="w-full md:w-2/3 pr-0 md:pr-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Current Subscription</h3>
                            
                            @if($subscription)
                                <div class="bg-gray-50 rounded-lg p-6 mb-6 shadow-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Plan Name</p>
                                            <p class="font-medium text-gray-800 text-lg">
                                                {{ $subscription->plan->plan_name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Status</p>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
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
                                                    <span class="text-green-600 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Enabled
                                                    </span>
                                                @else
                                                    <span class="text-red-600 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 101.414 1.414L10 11.414l1.293 1.293a1 1 001.414-1.414L11.414 10l1.293-1.293a1 1 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Disabled
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <h4 class="font-semibold text-gray-800 mb-2">Plan Features</h4>
                                        <ul class="list-disc pl-5 text-gray-700">
                                            @php
                                                $features = [];
                                                if($subscription->plan && isset($subscription->plan->features)) {
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
                                                <li>{{ is_string($subscription->plan->features ?? '') ? $subscription->plan->features : 'Basic features' }}</li>
                                            @endif
                                        </ul>
                                    </div>

                                    <div class="mt-6 pt-4 border-t border-gray-200">
                                        <h4 class="font-semibold text-gray-800 mb-2">Subscription Management</h4>
                                        
                                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                                            @if($subscription->status === 'active')
                                                <form action="{{ route('subscriptions.request-cancellation') }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md text-sm transition duration-200" onclick="return confirm('Are you sure you want to request cancellation of your subscription?')">
                                                        Request Cancellation
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 rounded-lg p-6 mb-6 shadow-sm text-center">
                                    <p class="text-gray-700">No active subscription found.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Upgrade Options -->
                        <div class="w-full md:w-1/3 mt-6 md:mt-0">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Available Plan Upgrades</h3>
                            
                            @if(isset($availablePlans) && count($availablePlans) > 0)
                                <div class="space-y-4">
                                    @foreach($availablePlans as $plan)
                                        <div class="border rounded-lg p-4 hover:shadow-md transition duration-200">
                                            <h4 class="font-semibold text-gray-800">{{ $plan->plan_name ?? 'No name' }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $plan->description ?? 'No description available' }}
                                            </p>
                                            <p class="mt-2 text-blue-600 font-medium">${{ number_format($plan->price ?? 0, 2) }} / {{ $plan->billing_cycle ?? 'month' }}</p>
                                            
                                            <div class="mt-3">
                                                <form action="{{ route('subscriptions.request-upgrade') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                    <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition duration-200">
                                                        Request Upgrade
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 rounded-lg p-4 text-gray-700 text-center">
                                    <p>No upgrade options available at this time.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>