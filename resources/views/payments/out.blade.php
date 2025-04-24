<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <!-- Header Box -->
        <div class="bg-white rounded-xl shadow-xl mb-6 border border-blue-100 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Awaiting Payment</h1>
                        <p class="text-sm text-gray-500">Complete your transaction to continue</p>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Retrieve and decode payment data from the JSON response
            $decoded = json_decode($responseData, true);
            $paymentData = $decoded['data'] ?? null;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Payment Details -->
            <div class="lg:col-span-2">
                @if($paymentData)
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 h-full">
                        <div class="bg-white p-6 border-b border-blue-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-800">Payment Details</h2>
                            </div>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <div class="text-sm text-gray-500 mb-1">Reference Number</div>
                                    <div class="font-medium text-gray-800">{{ $paymentData['attributes']['reference_number'] ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <div class="text-sm text-gray-500 mb-1">Amount</div>
                                    <div class="font-medium text-gray-800">{{ number_format($paymentData['attributes']['amount'] / 100, 2) }} {{ $paymentData['attributes']['currency'] ?? '' }}</div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <div class="text-sm text-gray-500 mb-1">Description</div>
                                <div class="font-medium text-gray-800">{{ $paymentData['attributes']['description'] ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <div class="text-sm text-gray-500 mb-1">Remarks</div>
                                <div class="font-medium text-gray-800">{{ $paymentData['attributes']['remarks'] ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="flex items-center p-4 rounded-lg border border-amber-200 bg-amber-50">
                                <div class="mr-3 bg-amber-100 p-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-amber-800">Status: {{ $paymentData['attributes']['status'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-amber-700">Waiting for your payment to be completed</div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <a href="{{ $paymentData['attributes']['checkout_url'] ?? '#' }}" target="_blank" 
                                   class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Complete Payment
                                </a>
                                <p class="text-sm text-gray-500 mt-3 text-center">
                                    Click the button above to complete your payment. Once confirmed, you will receive a confirmation message.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 p-6">
                        <div class="flex flex-col items-center justify-center py-6">
                            <svg class="h-12 w-12 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 text-base">No payment details available</p>
                            <p class="text-gray-400 text-sm mt-1">Please try generating the payment link again</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Payment Status -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 h-full">
                    <div class="bg-white p-6 border-b border-blue-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-800">Payment Status</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div id="payment-status" class="text-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                            <p class="mt-4 text-gray-700">Please wait while we process your payment...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Instructions -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 mb-8">
            <div class="bg-white p-6 border-b border-blue-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Payment Instructions</h2>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex">
                        <div class="mr-4 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full h-10 w-10 flex items-center justify-center font-bold">1</div>
                        <div>
                            <h3 class="font-medium text-gray-800 mb-1">Click the payment link</h3>
                            <p class="text-sm text-gray-600">Use the "Complete Payment" button to proceed to the payment gateway</p>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex">
                        <div class="mr-4 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full h-10 w-10 flex items-center justify-center font-bold">2</div>
                        <div>
                            <h3 class="font-medium text-gray-800 mb-1">Complete the payment</h3>
                            <p class="text-sm text-gray-600">Follow the instructions on the payment gateway to complete your transaction</p>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex">
                        <div class="mr-4 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full h-10 w-10 flex items-center justify-center font-bold">3</div>
                        <div>
                            <h3 class="font-medium text-gray-800 mb-1">Wait for confirmation</h3>
                            <p class="text-sm text-gray-600">This page will automatically update once your payment is confirmed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pollPaymentStatus() {
            // Parse data properly
            const decoded = JSON.parse(@json($responseData));
            const referenceNumber = decoded.data.attributes.reference_number;
            
            if (!referenceNumber) {
                console.error('No reference number available');
                return;
            }

            let pollingAttempts = 0;
            const maxAttempts = 30; // 5 minutes with 10-second intervals

            const checkStatus = async () => {
                try {
                    console.log('Checking payment status for:', referenceNumber);
                    const response = await fetch(`{{ url('/payment/check-status') }}/${referenceNumber}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`Network response error: ${response.status}`);
                    }
                    
                    const status = await response.text();
                    console.log('Payment status:', status);
                    
                    if (status === 'successful') {
                        window.location.href = `{{ route('payment.success') }}?reference=${referenceNumber}`;
                        return;
                    } 
                    
                    // Important: Continue polling even on 'error' responses
                    // This allows recovery if there's a temporary error
                    pollingAttempts++;
                    if (pollingAttempts >= maxAttempts) {
                        document.getElementById('payment-status').innerHTML = `
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-amber-700 font-medium">Your payment may still be processing.</p>
                                <p class="text-amber-600 text-sm mt-1">Please check your email for confirmation or contact support.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    // Show error message but continue polling
                    if (status === 'error') {
                        document.getElementById('payment-status').innerHTML = `
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-amber-700 font-medium">We're still waiting for payment confirmation.</p>
                                <p class="text-amber-600 text-sm mt-1">This page will update automatically when your payment is confirmed.</p>
                            </div>
                        `;
                    }
                    
                    // Continue polling regardless of status
                    setTimeout(checkStatus, 10000); // Check every 10 seconds
                } catch (error) {
                    console.error('Error checking payment status:', error);
                    pollingAttempts++;
                    if (pollingAttempts < maxAttempts) {
                        setTimeout(checkStatus, 10000);
                    }
                }
            };

            // Start polling
            checkStatus();
        }

        // Ensure the DOM is fully loaded before starting
        document.addEventListener('DOMContentLoaded', pollPaymentStatus);
    </script>
    @endpush
</x-app-layout>
