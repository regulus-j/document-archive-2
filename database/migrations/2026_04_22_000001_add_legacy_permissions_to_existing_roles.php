<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds legacy permissions to existing company roles
     * to ensure backward compatibility with navigation and views.
     */
    public function up(): void
    {
        // Define the permission mapping for each role
        $rolePermissions = [
            'company-admin' => [
                // Legacy permissions that may be missing
                'role-list', 'role-create', 'role-edit', 'role-delete',
                'user-list', 'user-create', 'user-edit', 'user-delete',
                'office-list', 'office-create', 'office-edit', 'office-delete',
                'document-list', 'document-create', 'document-edit', 'document-delete',
                'document-release', 'document-receive',
                'audit-list',
            ],
            'user' => [
                // Legacy permissions that may be missing
                'document-list', 'document-create', 'document-edit', 'document-delete',
                'document-release', 'document-receive',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            // Find all company-specific roles with this name
            $roles = Role::where('name', $roleName)
                ->whereNotNull('company_id')
                ->get();

            foreach ($roles as $role) {
                try {
                    // Get existing permissions for this role
                    $existingPermissions = $role->permissions->pluck('name')->toArray();
                    
                    // Find missing permissions
                    $missingPermissions = array_diff($permissions, $existingPermissions);
                    
                    if (count($missingPermissions) > 0) {
                        // Ensure all missing permissions exist in database
                        foreach ($missingPermissions as $permissionName) {
                            Permission::firstOrCreate([
                                'name' => $permissionName,
                                'guard_name' => 'web',
                            ]);
                        }
                        
                        // Add missing permissions to the role
                        $role->givePermissionTo($missingPermissions);
                        
                        Log::info('Added missing permissions to role', [
                            'role_id' => $role->id,
                            'role_name' => $role->name,
                            'company_id' => $role->company_id,
                            'added_permissions' => $missingPermissions,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to add permissions to role', [
                        'role_id' => $role->id,
                        'role_name' => $role->name,
                        'company_id' => $role->company_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to remove permissions as they should remain for backward compatibility
    }
};
