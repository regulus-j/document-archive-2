<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\CompanyAccount;
use App\Models\Document;
use App\Policies\CompanyPolicy;
use App\Policies\DocumentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        CompanyAccount::class => CompanyPolicy::class,
        Document::class => DocumentPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
