<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyAccount;

class FeatureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('feature.checker', function ($app) {
            return new \App\Services\FeatureChecker();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register a global helper for feature checking in Blade templates
        Blade::if('feature', function ($featureKey) {
            return $this->hasFeatureAccess($featureKey);
        });
    }

    /**
     * Check if the current user has access to a specific feature.
     *
     * @param string $featureKey
     * @return bool
     */
    private function hasFeatureAccess(string $featureKey): bool
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
}
