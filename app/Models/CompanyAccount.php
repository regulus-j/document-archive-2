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
        'industry',
        'company_size',
    ];

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
            'company_phone' => 'required|string|max:20',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'company_users', 'company_id', 'user_id');
    }


    public function offices()
    {
        return $this->hasMany(Office::class, 'company_id');
    }

    //company address
    public function address(): HasMany
    {
        return $this->hasMany(CompanyAddress::class, 'company_id');
    }
    
    /**
     * Alias for address() relationship to maintain compatibility with existing code
     */
    public function addresses(): HasMany
    {
        return $this->address();
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
    
    /**
     * Get all documents belonging to this company
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'company_id');
    }
}

