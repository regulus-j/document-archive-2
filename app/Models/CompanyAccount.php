<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'registered_name',
        'company_email',
        'company_phone',
    ];

    /**
     * Boot the model.
     * Auto-create default roles when a new company is created.
     */
    protected static function booted(): void
    {
        static::created(function (CompanyAccount $company) {
            Role::createDefaultRolesForCompany($company->id);
        });
    }

    // Custom validation rules
    public static function rules($userId = null)
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($userId) {
                    $query = self::where('user_id', $value);

                    // If updating an existing record, exclude the current record
                    if ($userId) {
                        $query->where('id', '!=', $userId);
                    }

                    if ($query->exists()) {
                        $fail('This user already owns a company.');
                    }
                }
            ],
            'company_name' => 'required|string|max:255',
            'registered_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users', 'company_id', 'user_id');
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'company_users', 'company_id', 'user_id');
    }


    public function offices()
    {
        return $this->hasMany(Office::class, 'company_id');
    }

    /**
     * Get the roles that belong to this company.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'company_id');
    }

    //company address
    public function address(): HasMany
    {
        return $this->hasMany(CompanyAddress::class, 'company_id');
    }

    public function latestSubscriptionByStartDate(): HasOne
    {
        return $this->hasOne(CompanySubscription::class, 'company_id')
            ->orderBy('start_date', 'desc');
    }

    public function latestSubscriptionByEndDate(): HasOne
    {
        return $this->hasOne(CompanySubscription::class, 'company_id')
            ->orderBy('end_date', 'desc');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class, 'company_id');
    }

    public function userLimit()
    {
        // Check if there is currently a subscription
        $subscription = $this->subscriptions()->with('plan.features')->first();

        if (!$subscription) {
            return 3;
        }

        $plan = $subscription->plan;
        // Check which user limit feature the plan has
        if ($plan->hasFeature('users-100')) {
            return 100;
        } elseif ($plan->hasFeature('users-30')) {
            return 30;
        } elseif ($plan->hasFeature('users-10')) {
            return 10;
        } else {
            // Fallback to free tier limit if no user limit feature found
            return 3;
        }
    }
    public function canAddUser()
    {
        return $this->employees->count() < $this->userLimit();
    }

    public function teamLimit()
    {
        // Check if there is currently a subscription
        $subscription = $this->subscriptions()->with('plan.features')->first();

        if (!$subscription) {
            return 1;
        }

        $plan = $subscription->plan;
        // Check which user limit feature the plan has
        if ($plan->hasFeature('users-20')) {
            return 20;
        } elseif ($plan->hasFeature('users-10')) {
            return 10;
        } elseif ($plan->hasFeature('users-3')) {
            return 3;
        } else {
            // Fallback to free tier limit if no user limit feature found
            return 1;
        }
    }

    public function canAddTeam()
    {
        return $this->offices->count() < $this->teamLimit();
    }

    // $plan->hasFeature('storage-2gb');
}
