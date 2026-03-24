<x-app-layout>
    <section id="pricing" class="py-20 bg-indigo-50" x-data="{ billingCycle: 'monthly' }">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 text-indigo-900">Plan & Pricing</h2>
                <p class="text-lg text-indigo-700 max-w-2xl mx-auto mb-8">
                    Choose the plan that fits your needs.
                </p>
                
                <!-- Billing Toggle -->
                <div class="inline-flex border-2 border-indigo-500 rounded-md overflow-hidden">
                    <button 
                        class="py-2 px-6 focus:outline-none text-base font-medium" 
                        :class="{ 'bg-indigo-600 text-white': billingCycle === 'monthly', 'text-indigo-700': billingCycle !== 'monthly' }" 
                        @click="billingCycle = 'monthly'">
                        Monthly
                    </button>
                    <button 
                        class="py-2 px-6 focus:outline-none text-base font-medium" 
                        :class="{ 'bg-indigo-600 text-white': billingCycle === 'yearly', 'text-indigo-700': billingCycle !== 'yearly' }" 
                        @click="billingCycle = 'yearly'">
                        Yearly
                    </button>
                </div>
            </div>

            @php
            $activeSubscription = auth()->user()->companies()->first()?->subscription ?? null;
            @endphp

            @if(isset($plans) && $plans->count() > 0)
            <!-- Pricing Cards -->
            <div class="relative max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Free Tier -->
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                        <div class="p-8 border-b border-slate-100">
                            <h3 class="text-sm font-medium text-indigo-500 uppercase tracking-wider mb-1">Free Tier</h3>
                            <div class="flex items-end">
                                <span class="text-4xl font-bold text-indigo-900">
                                    ₱0
                                </span>
                                <span class="text-lg ml-1 text-indigo-600 mb-1">/month</span>
                            </div>
                            <p class="text-indigo-700 mt-2">Basic features with limited functionality.</p>
                        </div>
                        <div class="p-8">
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-indigo-700">3 Users</span>
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-indigo-700">1 Team</span>
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-indigo-700">500 MB Storage</span>
                                </li>
                            </ul>
                            @if(!$activeSubscription)
                            <div class="mt-8 p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-center text-sm text-green-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Current Plan
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @foreach($plans ?? [] as $index => $plan)
                    <!-- {{ $plan->name }} Plan -->
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                        <div class="p-8 border-b border-slate-100 {{ $index === 0 ? 'relative' : '' }}">
                            @if($index === 0)
                            <span class="bg-indigo-600 text-white px-3 py-1 text-xs absolute right-0 top-0 rounded-bl font-semibold">Popular</span>
                            @endif
                            <h3 class="text-sm font-medium text-indigo-500 uppercase tracking-wider mb-1">
                                    @if($index === 0)
                                        Basic Plan
                                    @elseif($index === 1)
                                        Standard Plan
                                    @else
                                        Premium Plan
                                    @endif
                                </h3>
                            <div class="flex items-end">
                                <span class="text-4xl font-bold text-indigo-900">
                                    ₱ <span x-text="billingCycle === 'yearly' ? '{{ number_format($plan->price * 10 *100) }}' : '{{ number_format($plan->price * 100) }}'"></span>
                                </span>
                                <span class="text-lg ml-1 text-indigo-600 mb-1" x-text="billingCycle === 'monthly' ? '/month' : '/year'"></span>
                            </div>
                            <p class="text-indigo-700 mt-2">{{ $plan->description ?? 'Best for your needs' }}</p>
                        </div>
                        <div class="p-8">
                            <ul class="space-y-4">
                                @foreach($plan->getEnabledFeatures() as $feature)
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-indigo-700">{{ $feature->name }}</span>
                                </li>
                                @endforeach
                            </ul>
                            <!-- Payment Link or Subscription Status -->
                            @if($activeSubscription && $activeSubscription->plan_id == $plan->id)
                            <div class="mt-8 p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-center text-sm text-green-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Current Plan (Active until {{ $activeSubscription->expires_at ? date('M d, Y', strtotime($activeSubscription->expires_at)) : 'ongoing' }})
                                </p>
                            </div>
                            @else
                            <a 
                                x-bind:href="'{{ route('payment.generate', ['plan' => $plan->id]) }}' + '/' + billingCycle"
                                class="mt-8 block w-full bg-indigo-600 text-white text-center py-3 rounded-md hover:bg-indigo-700 transition-colors font-medium">
                                {{ $activeSubscription ? 'Switch Plan' : 'Subscribe Now' }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-indigo-700 text-lg">No plans available at the moment. Please check back later.</p>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>