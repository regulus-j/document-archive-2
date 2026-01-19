<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // In AppServiceProvider or any other provider
    public function register(): void
    {
        // Binding the 'has.company' key to CompanyService
        $this->app->bind('has.company', function ($app) {
            return new CompanyService(); // Return an instance of the service
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}
