<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueEmailInCompany implements ValidationRule
{
    protected $companyId;
    protected $ignoreUserId;

    /**
     * Create a new rule instance.
     *
     * @param int|null $companyId The company ID to check uniqueness within
     * @param int|null $ignoreUserId User ID to ignore (for updates)
     */
    public function __construct(?int $companyId = null, ?int $ignoreUserId = null)
    {
        $this->companyId = $companyId;
        $this->ignoreUserId = $ignoreUserId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->companyId) {
            return;
        }

        // Check if email exists for any user in this company
        $query = DB::table('users')
            ->join('company_users', 'users.id', '=', 'company_users.user_id')
            ->where('company_users.company_id', $this->companyId)
            ->where('users.email', $value)
            ->whereNull('users.deleted_at');

        // Ignore specific user if updating
        if ($this->ignoreUserId) {
            $query->where('users.id', '!=', $this->ignoreUserId);
        }

        if ($query->exists()) {
            $fail('This email address is already in use within your company.');
        }
    }
}
