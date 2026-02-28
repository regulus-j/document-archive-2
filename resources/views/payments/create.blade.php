<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-slate-200">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-slate-900">Complete Your Payment</h2>
                        <p class="mt-2 text-sm text-slate-600">Selected plan details:</p>
                    </div>

                    <!-- Plan Summary -->
                    <div class="mb-8 p-4 bg-slate-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $plan->plan_name }}</h3>
                                <p class="text-sm text-slate-600">Billing cycle: {{ ucfirst($billing) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold">₱{{ number_format($price, 2) }}</p>
                                <p class="text-sm text-slate-600">/{{ $billing }}</p>
                            </div>
                        </div>
                    </div>

                    @if($paymentLink)
                        <div class="mt-6">
                            <a href="{{ $paymentLink }}" 
                               class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Proceed to Payment
                            </a>
                            <p class="mt-2 text-sm text-slate-600 text-center">
                                You will be redirected to our secure payment gateway
                            </p>
                        </div>
                    @else
                        <div class="p-4 rounded-md bg-red-50">
                            <p class="text-sm text-red-600">
                                Unable to generate payment link. Please try again later or contact support.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
