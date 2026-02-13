<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'company_id',
    ];

    /**
     * The company this role belongs to.
     * NULL company_id means a global role (e.g. super-admin).
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyAccount::class, 'company_id');
    }

    /**
     * Check if this is a global (system-wide) role.
     */
    public function isGlobal(): bool
    {
        return is_null($this->company_id);
    }

    /**
     * Scope to roles belonging to a specific company (plus global roles).
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
              ->orWhereNull('company_id');
        });
    }

    /**
     * Scope to only company-specific roles (exclude global).
     */
    public function scopeCompanyOnly($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Create default roles (company-admin and user) for a given company.
     */
    public static function createDefaultRolesForCompany(int $companyId): void
    {
        $defaultRoles = [
            'company-admin' => [
                'role-list', 'role-create', 'role-edit', 'role-delete',
                'office-list', 'office-create', 'office-delete', 'office-edit',
                'document-list', 'document-create', 'document-edit', 'document-delete',
                'document-release', 'document-receive',
                'audit-list',
                'user-list', 'user-create', 'user-edit', 'user-delete',
            ],
            'user' => [
                'document-list', 'document-create', 'document-edit', 'document-delete',
                'document-release', 'document-receive',
            ],
        ];

        foreach ($defaultRoles as $roleName => $permissions) {
            $role = static::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
                'company_id' => $companyId,
            ]);

            $role->syncPermissions($permissions);
        }
    }
}
