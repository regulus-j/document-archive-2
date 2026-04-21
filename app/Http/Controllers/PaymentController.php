<?php

namespace App\Http\Controllers;

use App\Models\CompanyAccount;
use App\Models\CompanySubscription;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPayment::with(['subscription.company'])
            ->when($request->search, function ($query, $search) {
                $query->where('transaction_reference', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->payment_method, function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('payment_date', $date);
            });

        $payments = $query->latest()->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function show(SubscriptionPayment $payment)
    {
        $payment->load('subscription.company');

        return view('payments.show', compact('payment'));
    }

    public function linkCreate(Plan $plan, string $billing = 'monthly')
    {
        $billing = $this->normalizeBillingCycle($billing);
        $referenceNumber = $this->generateTransactionReference();

        session([
            'selected_plan_id' => $plan->id,
            'selected_billing' => $billing,
            'selected_reference' => $referenceNumber,
        ]);

        try {
            $checkoutSession = $this->createPayMongoCheckoutSession($plan, $billing, $referenceNumber);

            session(['checkout_session_id' => $checkoutSession['checkoutSessionId']]);

            return redirect()->away($checkoutSession['checkoutUrl']);
        } catch (\Throwable $e) {
            \Log::error('PayMongo checkout session creation failed: ' . $e->getMessage(), [
                'plan_id' => $plan->id,
                'billing' => $billing,
                'reference' => $referenceNumber,
            ]);

            return redirect()->back()
                ->with('error', 'Unable to start checkout. Please try again later or contact support.');
        }
    }

    public function checkPaymentStatus($identifier)
    {
        try {
            if (str_starts_with($identifier, 'cs_')) {
                $checkoutSession = $this->fetchCheckoutSessionData($identifier);

                if (!$checkoutSession) {
                    return response('pending', 200)->header('Content-Type', 'text/plain');
                }

                $status = $this->checkoutSessionHasSuccessfulPayment($checkoutSession) ? 'successful' : 'pending';

                return response($status, 200)->header('Content-Type', 'text/plain');
            }

            $result = $this->fetchPayMongoLinkData($identifier);

            if (!$result) {
                return response('pending', 200)->header('Content-Type', 'text/plain');
            }

            $paymentLink = $result['data'] ?? null;
            if (isset($paymentLink[0])) {
                $paymentLink = $paymentLink[0];
            }

            $paymentStatus = $paymentLink['attributes']['status'] ?? 'pending';
            $status = $paymentStatus === 'paid' ? 'successful' : $paymentStatus;

            return response($status, 200)->header('Content-Type', 'text/plain');
        } catch (\Throwable $e) {
            \Log::error('PayMongo status check failed: ' . $e->getMessage(), [
                'identifier' => $identifier,
            ]);

            return response('error', 200)->header('Content-Type', 'text/plain');
        }
    }

    private function createOrUpdateSubscription($company)
    {
        $subscription = $company->subscriptions()->where('status', 'pending')->first();

        if (!$subscription) {
            $selectedPlanId = session('selected_plan_id') ?? session('selected_plan')?->id;
            if (!$selectedPlanId) {
                throw new \RuntimeException('Selected plan not found in session.');
            }

            $billing = $this->normalizeBillingCycle(session('selected_billing', 'monthly'));

            $subscription = CompanySubscription::create([
                'company_id' => $company->id,
                'plan_id' => $selectedPlanId,
                'start_date' => now(),
                'end_date' => $billing === 'yearly' ? now()->addYear() : now()->addMonth(),
                'status' => 'active',
                'auto_renew' => true,
            ]);
        } else {
            $subscription->update(['status' => 'active']);
        }

        return $subscription;
    }

    private function createPaymentRecord($subscription, $paymentData)
    {
        return SubscriptionPayment::create([
            'company_subscription_id' => $subscription->id,
            'payment_date' => now(),
            'amount' => $paymentData['attributes']['amount'] / 100,
            'payment_method' => 'paymongo',
            'status' => 'successful',
            'transaction_reference' => $paymentData['attributes']['reference_number'],
            'notes' => $paymentData['attributes']['remarks'],
        ]);
    }

    public function success(Request $request)
    {
        $referenceNumber = $request->query('reference');
        $payment = SubscriptionPayment::with(['subscription.plan'])
            ->where('transaction_reference', $referenceNumber)
            ->firstOrFail();

        return view('payments.success', [
            'message' => 'Payment completed successfully!',
            'payment' => $payment,
            'subscription' => $payment->subscription,
            'plan' => $payment->subscription->plan,
        ]);
    }

    public function create(Request $request, Plan $plan)
    {
        $billing = $this->normalizeBillingCycle($request->query('billing', 'monthly'));
        $referenceNumber = $this->generateTransactionReference();

        session([
            'selected_plan_id' => $plan->id,
            'selected_billing' => $billing,
            'selected_reference' => $referenceNumber,
        ]);

        try {
            $checkoutSession = $this->createPayMongoCheckoutSession($plan, $billing, $referenceNumber);
            session(['checkout_session_id' => $checkoutSession['checkoutSessionId']]);
        } catch (\Throwable $e) {
            \Log::error('PayMongo checkout session creation failed: ' . $e->getMessage(), [
                'plan_id' => $plan->id,
                'billing' => $billing,
                'reference' => $referenceNumber,
            ]);

            return view('payments.create', [
                'plan' => $plan,
                'billing' => $billing,
                'price' => $this->calculateDisplayedPrice($plan->price, $billing),
                'paymentLink' => null,
                'error' => 'Unable to start checkout. Please try again later or contact support.',
            ]);
        }

        return view('payments.create', [
            'plan' => $plan,
            'billing' => $billing,
            'price' => $checkoutSession['displayPrice'],
            'paymentLink' => $checkoutSession['checkoutUrl'],
        ]);
    }

    public function store(Request $request, Plan $plan)
    {
        $request->validate([
            'payment_method' => ['required', 'in:credit_card,paypal,bank_transfer,gcash'],
        ]);

        $user = auth()->user();
        $billing = $request->query('billing', 'monthly');
        $price = $this->calculatePrice($plan->price, $billing);

        try {
            DB::beginTransaction();

            $company = CompanyAccount::where('user_id', $user->id)->firstOrFail();

            $subscription = CompanySubscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => $billing === 'yearly' ? now()->addYear() : now()->addMonth(),
                'status' => 'pending',
                'auto_renew' => true,
            ]);

            $payment = SubscriptionPayment::create([
                'company_subscription_id' => $subscription->id,
                'payment_date' => now(),
                'amount' => $price,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'transaction_reference' => $this->generateTransactionReference(),
            ]);

            $payment->update(['status' => 'successful']);
            $subscription->update(['status' => 'active']);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Payment successful! Your subscription is now active.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function handleCallback(Request $request)
    {
        $referenceNumber = $request->query('reference') ?: session('selected_reference');
        $checkoutSessionId = $request->query('checkout_session_id') ?: session('checkout_session_id');

        if (!$referenceNumber || !$checkoutSessionId) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment callback.');
        }

        $existingPayment = SubscriptionPayment::where('transaction_reference', $referenceNumber)->first();
        if ($existingPayment && $existingPayment->status === 'successful') {
            return redirect()->route('payment.success', ['reference' => $referenceNumber]);
        }

        try {
            $checkoutSession = $this->fetchCheckoutSessionData($checkoutSessionId);
            if (!$checkoutSession || !$this->checkoutSessionHasSuccessfulPayment($checkoutSession)) {
                return redirect()->route('dashboard')->with('info', 'Payment is still being processed. You will be notified once confirmed.');
            }

            $user = auth()->user();
            if (!$user) {
                return redirect()->route('dashboard')->with('error', 'Your payment was received, but your account session expired. Please sign in again.');
            }

            $company = $user->companies()->first();
            if (!$company) {
                return redirect()->route('dashboard')->with('error', 'Your payment was received, but no company is linked to your account. Please contact support.');
            }

            DB::beginTransaction();

            $subscription = $this->createOrUpdateSubscription($company);
            $paymentData = $this->buildPaymentDataFromCheckoutSession($checkoutSession, $referenceNumber);
            $this->createPaymentRecord($subscription, $paymentData);

            DB::commit();

            session()->forget([
                'checkout_session_id',
                'selected_reference',
                'selected_plan_id',
                'selected_billing',
            ]);

            return redirect()->route('payment.success', ['reference' => $referenceNumber]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('PayMongo checkout callback failed: ' . $e->getMessage(), [
                'checkout_session_id' => $checkoutSessionId,
                'reference' => $referenceNumber,
            ]);

            return redirect()->route('dashboard')->with('error', 'Payment verification failed. Please contact support if you were charged.');
        }
    }

    private function calculatePrice($basePrice, $billing)
    {
        return $billing === 'yearly' ? $basePrice * 12 : $basePrice;
    }

    private function calculateDisplayedPrice($basePrice, $billing)
    {
        // Preserve the pricing currently shown on the public plan pages.
        return $billing === 'yearly' ? $basePrice * 1000 : $basePrice * 100;
    }

    private function normalizeBillingCycle(?string $billing): string
    {
        return $billing === 'yearly' ? 'yearly' : 'monthly';
    }

    private function resolvePlanName(Plan $plan): string
    {
        return $plan->plan_name ?? $plan->name ?? 'Subscription';
    }

    private function createPayMongoCheckoutSession(Plan $plan, string $billing, string $referenceNumber): array
    {
        $billing = $this->normalizeBillingCycle($billing);
        $displayPrice = $this->calculateDisplayedPrice($plan->price, $billing);
        $amount = (int) round($displayPrice * 100);

        $body = [
            'data' => [
                'attributes' => [
                    'cancel_url' => route('dashboard'),
                    'description' => 'Payment for subscription plan: ' . $this->resolvePlanName($plan),
                    'line_items' => [[
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'description' => ucfirst($billing) . ' subscription',
                        'name' => $this->resolvePlanName($plan),
                        'quantity' => 1,
                    ]],
                    'payment_method_types' => ['card', 'gcash'],
                    'reference_number' => $referenceNumber,
                    'send_email_receipt' => false,
                    'show_description' => true,
                    'show_line_items' => true,
                    'success_url' => route('payment.callback', ['reference' => $referenceNumber]),
                ],
            ],
        ];

        $client = new \GuzzleHttp\Client([
            'timeout' => 15,
            'connect_timeout' => 10,
        ]);

        $response = $client->request('POST', 'https://api.paymongo.com/v2/checkout_sessions', [
            'json' => $body,
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':'),
                'content-type' => 'application/json',
            ],
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);
        $checkoutUrl = $this->extractCheckoutUrl($responseData);

        if (!$checkoutUrl) {
            \Log::error('PayMongo checkout session response did not include a usable checkout URL.', [
                'response' => $responseData,
            ]);

            throw new \RuntimeException('Unable to extract a checkout URL from the PayMongo response.');
        }

        return [
            'checkoutSessionId' => $responseData['data']['id'] ?? null,
            'checkoutUrl' => $checkoutUrl,
            'displayPrice' => $displayPrice,
            'responseData' => $responseData,
        ];
    }

    private function extractCheckoutUrl(array $responseData): ?string
    {
        $attributes = $responseData['data']['attributes'] ?? [];
        $candidates = [
            $attributes['checkout_url'] ?? null,
            $attributes['redirect']['checkout_url'] ?? null,
            $attributes['url'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }
        }

        return null;
    }

    private function fetchCheckoutSessionData(string $checkoutSessionId): ?array
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 15,
            'connect_timeout' => 10,
        ]);

        $response = $client->request('GET', 'https://api.paymongo.com/v1/checkout_sessions/' . urlencode($checkoutSessionId), [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':'),
            ],
            'http_errors' => false,
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    private function checkoutSessionHasSuccessfulPayment(array $checkoutSession): bool
    {
        $attributes = $checkoutSession['data']['attributes'] ?? [];
        $payments = $attributes['payments'] ?? [];
        $paymentIntentStatus = $attributes['payment_intent']['attributes']['status'] ?? null;

        return !empty($payments) || $paymentIntentStatus === 'succeeded';
    }

    private function buildPaymentDataFromCheckoutSession(array $checkoutSession, string $referenceNumber): array
    {
        $attributes = $checkoutSession['data']['attributes'] ?? [];
        $payments = $attributes['payments'] ?? [];
        $paymentAmount = $payments[0]['attributes']['amount'] ?? null;

        if ($paymentAmount === null) {
            $paymentAmount = 0;

            foreach ($attributes['line_items'] ?? [] as $item) {
                $paymentAmount += ((int) ($item['amount'] ?? 0)) * ((int) ($item['quantity'] ?? 1));
            }
        }

        return [
            'attributes' => [
                'amount' => $paymentAmount,
                'reference_number' => $referenceNumber,
                'remarks' => $attributes['description'] ?? 'Checkout payment completed',
            ],
        ];
    }

    private function fetchPayMongoLinkData(string $identifier): ?array
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 15,
            'connect_timeout' => 10,
        ]);

        $headers = [
            'accept' => 'application/json',
            'authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':'),
        ];

        $endpoints = [
            'https://api.paymongo.com/v1/links/' . urlencode($identifier),
            'https://api.paymongo.com/v1/links?reference_number=' . urlencode($identifier),
        ];

        foreach ($endpoints as $endpoint) {
            $response = $client->request('GET', $endpoint, [
                'headers' => $headers,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        return null;
    }

    private function generateTransactionReference()
    {
        return 'TXN-' . strtoupper(uniqid()) . '-' . date('Ymd');
    }
}
