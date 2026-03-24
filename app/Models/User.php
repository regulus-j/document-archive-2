<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'verification_code',
        'verification_code_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Generate a verification code for the user.
     *
     * @param int $expiresInMinutes
     * @return string
     */
    public function generateVerificationCode(int $expiresInMinutes = 60): string
    {
        $code = Str::random(6); // Generate a 6-character random code

        $this->verification_code = $code;
        $this->verification_code_expires_at = Carbon::now()->addMinutes($expiresInMinutes);
        $this->save();

        return $code;
    }

    /**
     * Check if a verification code is valid.
     *
     * @param string $code
     * @return bool
     */
    public function isValidVerificationCode(string $code): bool
    {
        return $this->verification_code === $code &&
            $this->verification_code_expires_at !== null &&
            $this->verification_code_expires_at->gt(Carbon::now());
    }

    /**
     * Clear the verification code after use.
     *
     * @return void
     */
    public function clearVerificationCode(): void
    {
        $this->verification_code = null;
        $this->verification_code_expires_at = null;
        $this->save();
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null &&
            $this->email_verified_at !== '0000-00-00 00:00:00';
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploader');
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class)->select('offices.*');
    }

    // Teams (alias for offices)
    public function teams()
    {
        return $this->belongsToMany(Office::class)->select('offices.*');
    }




    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    public function company()
    {
        return $this->hasOne(CompanyAccount::class, 'user_id');
    }

    public function companySubscriptions()
    {
        return $this->hasMany(CompanySubscription::class, 'company_id');
    }

    public function companies()
    {
        return $this->belongsToMany(CompanyAccount::class, 'company_users', 'user_id', 'company_id');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('super-admin') || $this->hasRole('company-admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isCompanyAdmin(): bool
    {
        return $this->hasRole('company-admin');
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }
     public function companyAccount()
{
    return $this->hasOne(CompanyAccount::class, 'user_id');
}
}
