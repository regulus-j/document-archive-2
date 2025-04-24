<x-app-layout>
    <div x-data="{ billing: 'monthly' }" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">Plan & Pricing</h2>
                <p class="mt-4 text-lg leading-6 text-gray-600">Choose the plan that fits your needs.</p>
                
                <!-- Billing Toggle -->
                <div class="mt-6 inline-flex rounded-md shadow-sm">
                    <button 
                        @click="billing = 'monthly'" 
                        :class="{ 'bg-blue-500 text-white': billing === 'monthly', 'border border-blue-500 text-blue-500': billing !== 'monthly' }"
                        class="px-4 py-2 text-sm font-medium rounded-l-md">
                        Monthly
                    </button>
                    <button 
                        @click="billing = 'yearly'" 
                        :class="{ 'bg-blue-500 text-white': billing === 'yearly', 'border border-blue-500 text-blue-500': billing !== 'yearly' }"
                        class="px-4 py-2 text-sm font-medium rounded-r-md">
                        Yearly
                    </button>
                </div>
            </div>

            @php
                $activeSubscription = auth()->user()->companies()->first()?->subscription ?? null;
            @endphp

            <div class="mt-16 grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
                @foreach($plans ?? [] as $index => $plan)
                <div class="relative flex flex-col rounded-2xl border border-gray-200 p-8 shadow-sm">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $plan->plan_name }}</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-5xl font-bold tracking-tight text-gray-900" x-text="billing === 'monthly' ? 'P{{ number_format($plan->price * 100, 2) }}' : 'P{{ number_format($plan->price * 10 * 100, 2) }}'"></span>   
                            <span class="ml-1 text-sm font-semibold text-gray-500" x-text="'/' + billing"></span>
                        </div>
                    </div>
                    
                    <ul class="mt-8 space-y-4 flex-1">
                        @foreach($plan->getEnabledFeatures() as $feature)
                        <li class="flex items-center">
                            <div class="rounded-full p-1 bg-blue-500">
                                <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="ml-3 text-gray-600">{{ $feature->name }}</span>
                        </li>
                        @endforeach
                    </ul>
                    
                    <!-- Payment Link or Subscription Status -->
                    <div class="mt-8">
                        @if($activeSubscription && $activeSubscription->plan_id == $plan->id)
                            <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-center text-sm text-green-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Current Plan (Active until {{ $activeSubscription->expires_at ? date('M d, Y', strtotime($activeSubscription->expires_at)) : 'ongoing' }})
                                </p>
                            </div>
                        @else
                            <a 
                                x-bind:href="'{{ route('payment.generate', ['plan' => $plan->id]) }}' + '/' + billing" 
                                class="block w-full text-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-400 rounded-md hover:from-blue-700 hover:to-blue-500 shadow-lg transform transition hover:-translate-y-0.5">
                                {{ $activeSubscription ? 'Switch Plan' : 'Subscribe Now' }}
                            </a>
                        @endif
                    </div>

                    <p class="mt-6 text-center text-sm text-gray-500">{{ $plan->description }}</p>
                </div>
                @endforeach
            </div>
            
            <!-- Start Free Trial Button commented out -->
            {{-- @if(auth()->user()->isAdmin())
            <div class="mt-12 flex justify-center">
                <a href="{{ route('trial.start') }}"
                class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-400 rounded-md hover:from-blue-700 hover:to-blue-500 shadow-lg transform transition hover:-translate-y-0.5">
                    Start Free Trial
                    <svg class="w-4 h-4 ml-2 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                </a>
            </div>
            @endif --}}
        </div>
    </div>
</x-app-layout>
