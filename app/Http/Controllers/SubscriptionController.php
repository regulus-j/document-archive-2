<?php

namespace App\Http\Controllers;

use App\Models\CompanySubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Plan;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = CompanySubscription::with(['company', 'plan'])->get();
        return response()->json($subscriptions);
    }

    public function indexAdmin(Request $request)
    {
        $subscriptionsQuery = CompanySubscription::withoutGlobalScope('unexpired')->with(['company', 'plan']);

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $subscriptionsQuery->where(function ($query) use ($search) {
                $query->whereHas('company', function ($companyQuery) use ($search) {
                    $companyQuery->where('company_name', 'like', "%{$search}%")
                        ->orWhere('registered_name', 'like', "%{$search}%");
                })->orWhereHas('plan', function ($planQuery) use ($search) {
                    $planQuery->where('plan_name', 'like', "%{$search}%");
                })->orWhere('status', 'like', "%{$search}%");
            });
        }

        $status = $request->input('status');
        if (in_array($status, ['active', 'pending', 'canceled', 'expired'], true)) {
            $subscriptionsQuery->where('status', $status);
        }

        $subscriptions = $subscriptionsQuery->orderByDesc('start_date')->paginate(15)->withQueryString();
        return view('admin.subscriptions-index', compact('subscriptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:company_accounts,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'auto_renew' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $subscription = CompanySubscription::create([
                ...$validated,
                'status' => 'pending',
            ]);

            // Additional subscription setup logic here

            return response()->json($subscription, 201);
        });
    }

    public function update(Request $request, CompanySubscription $subscription)
    {
        $validated = $request->validate([
            'end_date' => 'nullable|date|after:start_date',
            'auto_renew' => 'boolean',
        ]);

        $subscription->update($validated);

        return response()->json($subscription);
    }

    public function cancel(CompanySubscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);

        return response()->json(['message' => 'Subscription canceled successfully']);
    }

    public function activate(CompanySubscription $subscription)
    {
        $subscription->update(['status' => 'active']);

        return response()->json(['message' => 'Subscription activated successfully']);
    }

    /**
     * Show form to assign subscription to a company admin
     */
    public function assignForm()
    {
        // Check if user is super admin
        if (!auth()->user()->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $companies = \App\Models\CompanyAccount::all();
        $plans = \App\Models\Plan::all();

        return view('admin.subscriptions.assign', compact('companies', 'plans'));
    }

    /**
     * Assign subscription to a company admin
     */
    public function assign(Request $request)
    {
        // Check if user is super admin
        if (!auth()->user()->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'company_id' => 'required|exists:company_accounts,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'auto_renew' => 'boolean',
            'status' => 'required|in:active,pending,canceled'
        ]);

        DB::transaction(function () use ($validated) {
            // Check if company already has an active subscription
            $existingSubscription = CompanySubscription::where('company_id', $validated['company_id'])
                ->where('status', 'active')
                ->first();

            if ($existingSubscription) {
                // Update existing subscription end date if requested
                if ($validated['status'] == 'active') {
                    $existingSubscription->update([
                        'end_date' => $validated['end_date'],
                        'auto_renew' => $validated['auto_renew'] ?? false
                    ]);
                    return redirect()->route('admin.subscriptions.index')
                        ->with('success', 'Subscription updated successfully.');
                } else {
                    // Cancel existing subscription if new one should be active
                    $existingSubscription->update(['status' => 'canceled']);
                }
            }

            // Create new subscription
            CompanySubscription::create($validated);
        });

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription assigned successfully.');
    }

    /**
     * Renew an existing subscription
     */
    public function renew(Request $request, CompanySubscription $subscription)
    {
        // Check if user is super admin
        if (!auth()->user()->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'end_date' => 'required|date|after:today',
            'auto_renew' => 'boolean',
        ]);

        // Extend the subscription
        $subscription->update([
            'end_date' => $validated['end_date'],
            'auto_renew' => $validated['auto_renew'] ?? $subscription->auto_renew,
            'status' => 'active'
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription renewed successfully until ' . $validated['end_date']);
    }

    /**
     * Update subscription status from super-admin dashboard controls.
     */
    public function updateStatus(Request $request, CompanySubscription $subscription)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'required|in:active,pending,canceled,expired',
        ]);

        $newStatus = $validated['status'];
        $updates = ['status' => $newStatus];

        // Keep status transitions consistent with date-based visibility rules.
        if (in_array($newStatus, ['canceled', 'expired'], true)) {
            $updates['auto_renew'] = false;
        }

        if (
            $newStatus === 'active' &&
            !empty($subscription->end_date) &&
            Carbon::parse($subscription->end_date)->lt(now()->startOfDay())
        ) {
            $updates['end_date'] = now()->toDateString();
        }

        if (
            $newStatus === 'expired' &&
            (empty($subscription->end_date) || Carbon::parse($subscription->end_date)->gt(now()->startOfDay()))
        ) {
            $updates['end_date'] = now()->toDateString();
        }

        $subscription->update($updates);

        return redirect()->back()->with('success', 'Subscription status updated successfully.');
    }

    /**
     * Handle automated subscription renewals
     * This should be called via a scheduled command
     */
    public function processAutoRenewals()
    {
        // Find subscriptions due to expire in the next 7 days that have auto-renew enabled
        $subscriptions = CompanySubscription::where('status', 'active')
            ->where('auto_renew', true)
            ->where('end_date', '<=', now()->addDays(7))
            ->where('end_date', '>=', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            // Extend by the original duration
            $originalDuration = $subscription->original_duration ?? 365; // Default to 1 year

            $subscription->update([
                'end_date' => Carbon::parse($subscription->end_date)->addDays($originalDuration),
                'renewal_count' => ($subscription->renewal_count ?? 0) + 1,
                // You could add payment processing logic here
            ]);

            // Notify company admin about renewal
            // Add notification logic here
        }

        return response()->json(['message' => count($subscriptions) . ' subscriptions renewed successfully']);
    }

    /**
     * Display subscription status for a company admin
     */
    public function showStatus()
    {
        // Check if user is a company admin
        if (!auth()->user()->hasRole('company-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Get the company for the authenticated user (first try owned company)
        $company = auth()->user()->company()->first();

        // If not found, check if user belongs to a company
        if (!$company) {
            $company = auth()->user()->companies()->first();
        }

        // Check if company exists
        if (!$company) {
            return redirect()->route('companies.create')->with('error', 'You need to create a company profile first.');
        }

        // Get current active subscription using the active scope
        $subscription = CompanySubscription::active()
            ->where('company_id', $company->id)
            ->with('plan')
            ->latest()  // Order by start_date (newest first)
            ->first();

        // // If no active subscription, redirect to plan selection with parameter to prevent redirect loop
        // if (!$subscription) {
        //     return redirect()->route('plans.select', ['from_subscription_status' => 1])
        //         ->with('info', 'Your company doesn\'t have an active subscription. Please select a plan.');
        // }

        $currentPlan = $subscription->plan;

        // Get available plans for potential upgrades
        // get all plans that are more expensive than current plan, plan->price

        $availablePlans = Plan::where('is_active', true)
            ->where('price', '>', $currentPlan->price)
            ->get();

        return view('subscriptions.status', compact('subscription', 'availablePlans'));
    }

    /**
     * Request cancellation of subscription
     */
    public function requestCancellation(Request $request)
    {
        // Check if user is a company admin
        if (!auth()->user()->hasRole('company-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Get the company for the authenticated user (first try owned company)
        $company = auth()->user()->company()->first();

        // If not found, check if user belongs to a company
        if (!$company) {
            $company = auth()->user()->companies()->first();
        }

        // Check if company exists
        if (!$company) {
            return redirect()->route('companies.create')->with('error', 'You need to create a company profile first.');
        }

        // Get current active subscription
        $subscription = CompanySubscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        // Update subscription auto_renew to false
        $subscription->update([
            'auto_renew' => false
        ]);

        return redirect()->back()
            ->with('success', 'Auto-renewal has been disabled. Your subscription will expire on ' . $subscription->end_date);
    }

    /**
     * Request plan upgrade
     */
    public function requestUpgrade(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        // Check if user is a company admin
        if (!auth()->user()->hasRole('company-admin')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Get the company for the authenticated user (first try owned company)
        $company = auth()->user()->company()->first();

        // If not found, check if user belongs to a company
        if (!$company) {
            $company = auth()->user()->companies()->first();
        }

        // Check if company exists
        if (!$company) {
            return redirect()->route('companies.create')->with('error', 'You need to create a company profile first.');
        }

        // Get current active subscription
        $subscription = CompanySubscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        $newPlan = \App\Models\Plan::find($validated['plan_id']);

        // Send upgrade request notification to administrators
        // This is a placeholder for actual notification logic

        return redirect()->back()
            ->with('success', 'Your plan upgrade request has been submitted. An administrator will contact you shortly.');
    }
}
