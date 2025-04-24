<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Successful
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-blue-100">
                <div class="p-6">
                    <div class="text-center mb-8">
                        <div class="inline-flex h-16 w-16 rounded-full bg-emerald-100 p-3 mb-4">
                            <svg class="h-full w-full text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold text-gray-900">{{ $message }}</h2>
                        <p class="mt-2 text-gray-600">Your transaction has been processed successfully.</p>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-6 mb-6 border border-blue-100">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Payment Details
                        </h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Transaction Reference</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $payment->transaction_reference }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Amount Paid</dt>
                                <dd class="mt-1 text-sm text-gray-900">₱{{ number_format($payment->amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y H:i:s') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Subscription Details
                        </h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Plan Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $plan->plan_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subscription Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($subscription->start_date)->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($subscription->end_date)->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>