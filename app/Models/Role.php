<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
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
                // Access Management
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                
                // Organization Management
                'offices.view', 'offices.create', 'offices.edit', 'offices.delete',
                
                // Document Lifecycle (new workflow-aligned permissions)
                'documents.manage',
                'documents.workflow.initiate',
                'documents.workflow.participate',
                'documents.workflow.admin',
                
                // Audit
                'audit.view',
            ],
            'user' => [
                // Document Lifecycle (basic user)
                'documents.manage',
                'documents.workflow.initiate',
                'documents.workflow.participate',
            ],
        ];

        foreach ($defaultRoles as $roleName => $permissions) {
            try {
                $role = static::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'company_id' => $companyId,
                ]);

                // Ensure all required permissions exist before syncing
                static::ensurePermissionsExist($permissions);

                $role->syncPermissions($permissions);
            } catch (\Exception $e) {
                Log::error('Failed to create default role for company', [
                    'company_id' => $companyId,
                    'role_name' => $roleName,
                    'error' => $e->getMessage(),
                ]);
                
                // Don't fail registration if role creation fails
                // The company admin can set up roles manually later
            }
        }
    }

    /**
     * Ensure all required permissions exist in the database.
     * Creates missing permissions automatically.
     */
    protected static function ensurePermissionsExist(array $permissionNames): void
    {
        foreach ($permissionNames as $permissionName) {
            try {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create permission', [
                    'permission' => $permissionName,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
