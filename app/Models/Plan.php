<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $fillable = [
        'plan_name',
        'description',
        'price',
        'billing_cycle',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    /**
     * Get the features for the plan
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_features')
                    ->withPivot('enabled', 'value')
                    ->withTimestamps();
    }

    /**
     * Check if the plan has a specific feature enabled
     */
    public function hasFeature(string $key): bool
    {
        return $this->features()
                    ->where('key', $key)
                    ->wherePivot('enabled', true)
                    ->exists();
    }

    /**
     * Get all features that are enabled for this plan
     */
    public function getEnabledFeatures()
    {
        return $this->features()->wherePivot('enabled', true)->get();
    }

    /**
     * Get the stored value for a specific enabled feature.
     */
    public function getFeatureValue(string $key): ?string
    {
        $feature = $this->features()
            ->where('key', $key)
            ->wherePivot('enabled', true)
            ->first();

        return $feature?->pivot?->value;
    }

    /**
     * Legacy getters for backwards compatibility
     */
    public function getFeature1Attribute()
    {
        return $this->hasFeature('document-storage');
    }

    public function getFeature2Attribute()
    {
        return $this->hasFeature('advanced-sharing');
    }

    public function getFeature3Attribute()
    {
        return $this->hasFeature('analytics');
    }
}

