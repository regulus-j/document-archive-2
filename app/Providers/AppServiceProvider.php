<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\RateLimiter;
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
        if(config('app.env') === 'production') {
        \URL::forceScheme('https');
    }

        RateLimiter::for('chatbot', function ($request) {
            return Limit::perMinute(15)->by(optional($request->user())->id ?: $request->ip());
        });

        // Urgency Matrix: Schedule the urgency monitoring command every 30 minutes
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('documents:monitor-urgency')->everyThirtyMinutes();
            // Auto-archive: check office schedules every 6 hours
            $schedule->command('documents:auto-archive')->everySixHours();
        });
    }
}
