<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\CompanySubscription;
use App\Models\CompanyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlanSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function select(Request $request)
    {
        // Get the authenticated user's company
        $company = CompanyAccount::where('user_id', auth()->user()->id)->first();
        
        if (!$company) {
            return redirect()->route('companies.create')->with('error', 'Please set up your company profile first.');
        }
        
        // Check if the company already has an active subscription
        // Using the active scope from the model for consistent behavior
        $activeSubscription = CompanySubscription::active()
            ->where('company_id', $company->id)
            ->first();
        
        // Break the redirect loop by checking if we were redirected from subscription status
        if ($activeSubscription && !$request->has('from_subscription_status')) {
            return redirect()->route('subscriptions.status')->with('info', 'You already have an active subscription.');
        }
        
        $plans = Plan::where('is_active', true)->get();
        return view('plans.select', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        return redirect()->route('payments.create', $plan);
    }
}

