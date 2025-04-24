<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // If there's no authenticated user, deny access
        if (!$user) {
            abort(403, 'You need to be logged in to access this feature.');
        }
        
        // Check if user is a super-admin, they have access to all features
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }
        
        // For company admins, check if their company's active subscription plan has this feature
        $company = null;
        
        // Get the user's company
        if ($user->hasRole('company-admin')) {
            $company = $user->companies()->first();
        } else {
            // For regular users, get their associated company
            $company = $user->companies()->first();
        }
        
        // If no company found, deny access
        if (!$company) {
            abort(403, 'No company association found for your account.');
        }
        
        // Get company's active subscription
        $activeSubscription = $company->subscriptions()
            ->where('status', 'active')
            ->first();
        
        // If no active subscription, deny access
        if (!$activeSubscription) {
            abort(403, 'Your company does not have an active subscription to access this feature.');
        }
        
        // Get the subscription plan
        $plan = $activeSubscription->plan;
        
        // If no plan found, deny access
        if (!$plan) {
            abort(403, 'No subscription plan found for your company.');
        }
        
        // Check if the plan has the feature enabled
        if (!$plan->hasFeature($featureKey)) {
            abort(403, 'This feature is not available in your current subscription plan. Please upgrade to access it.');
        }
        
        return $next($request);
    }
}
