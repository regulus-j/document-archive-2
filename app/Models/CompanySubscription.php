<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class CompanySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
        'renewal_count',
        'original_duration',
    ];

    // Add global scope to automatically filter out expired subscriptions 
    // when querying active subscriptions
    protected static function booted()
    {
        static::addGlobalScope('unexpired', function (Builder $builder) {
            $builder->where(function ($query) {
                $query->where('status', '!=', 'active')
                    ->orWhere(function($q) {
                        $q->where('status', 'active')
                          ->where(function($dateQuery) {
                              $dateQuery->whereNull('end_date')
                                  ->orWhere('end_date', '>=', Carbon::now()->toDateString());
                          });
                    });
            });
        });
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(CompanyAccount::class, 'company_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
    
    // Scope for truly active subscriptions (both status and not expired)
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', Carbon::now()->toDateString());
            });
    }
    
    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active' && 
            (is_null($this->end_date) || Carbon::parse($this->end_date)->gte(Carbon::now()));
    }

    public function isExpired(): bool
    {
        return $this->status === 'active' && 
            (!is_null($this->end_date) && Carbon::parse($this->end_date)->lt(Carbon::now()));
    }
}

