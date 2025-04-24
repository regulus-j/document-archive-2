<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class FeatureChecker
{
    /**
     * Check if the current user has access to a specific feature.
     *
     * @param string $featureKey
     * @return bool
     */
    public function hasAccess(string $featureKey): bool
    {
        $user = Auth::user();
        
        // If user is not authenticated, they don't have access
        if (!$user) {
            return false;
        }
        
        // Super admins have access to all features
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        // Get the user's company
        $company = $user->companies()->first();
        if (!$company) {
            return false;
        }
        
        // Get the company's active subscription
        $subscription = $company->subscriptions()
            ->where('status', 'active')
            ->first();
            
        if (!$subscription) {
            return false;
        }
        
        // Get the subscription plan and check if it has the feature
        $plan = $subscription->plan;
        if (!$plan) {
            return false;
        }
        
        return $plan->hasFeature($featureKey);
    }
    
    /**
     * Check if the current user's subscription plan has a specific feature,
     * with optional fallback value for when no user is logged in.
     *
     * @param string $featureKey
     * @param bool $fallback
     * @return bool
     */
    public function check(string $featureKey, bool $fallback = false): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return $fallback;
        }
        
        return $this->hasAccess($featureKey);
    }
    
    /**
     * Get all enabled features for the current user's subscription plan.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllFeatures()
    {
        $user = Auth::user();
        
        // If user is not authenticated or is a super-admin, return an empty collection
        if (!$user || $user->hasRole('super-admin')) {
            return collect();
        }
        
        // Get the user's company
        $company = $user->companies()->first();
        if (!$company) {
            return collect();
        }
        
        // Get the company's active subscription
        $subscription = $company->subscriptions()
            ->where('status', 'active')
            ->first();
            
        if (!$subscription || !$subscription->plan) {
            return collect();
        }
        
        return $subscription->plan->getEnabledFeatures();
    }
}