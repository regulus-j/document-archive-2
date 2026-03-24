<?php

namespace App\Policies;

use App\Models\CompanyAccount;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Determine if the user can view any companies (list all companies).
     * Only super-admins can see the list of all companies.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the company.
     */
    public function view(User $user, CompanyAccount $company): bool
    {
        // Super-admin can view any company
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Company admin can view their own company
        return $user->companies->contains($company->id);
    }

    /**
     * Determine if the user can create companies.
     */
    public function create(User $user): bool
    {
        // Super admins can always create companies
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Regular users can create a company if they don't own one yet
        return $user->company === null;
    }

    /**
     * Determine if the user can update the company.
     */
    public function update(User $user, CompanyAccount $company): bool
    {
        // Super-admin can update any company
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Company admin can only update their own company
        return $user->companies->contains($company->id);
    }

    /**
     * Determine if the user can delete the company.
     */
    public function delete(User $user, CompanyAccount $company): bool
    {
        // Only super admins can delete companies
        return $user->isSuperAdmin();
    }
}
