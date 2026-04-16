<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use App\Models\PermissionMetadata;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mapping = PermissionMetadata::getLegacyMapping();
        
        // Create all new permissions
        foreach (PermissionMetadata::getAllNewPermissions() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Migrate role-permission relationships from old to new permissions
        foreach ($mapping as $oldName => $newName) {
            // Get the old permission
            $oldPermission = Permission::where('name', $oldName)->first();
            $newPermission = Permission::where('name', $newName)->first();

            if ($oldPermission && $newPermission) {
                // Copy all role associations from old to new permission
                $roleIds = DB::table('role_has_permissions')
                    ->where('permission_id', $oldPermission->id)
                    ->pluck('role_id');

                foreach ($roleIds as $roleId) {
                    // Insert new permission-role relationship if it doesn't exist
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $newPermission->id,
                        'role_id' => $roleId,
                    ]);
                }

                // Copy all user associations from old to new permission
                $modelIds = DB::table('model_has_permissions')
                    ->where('permission_id', $oldPermission->id)
                    ->get();

                foreach ($modelIds as $modelPermission) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $newPermission->id,
                        'model_type' => $modelPermission->model_type,
                        'model_id' => $modelPermission->model_id,
                    ]);
                }
            }
        }

        // Note: We keep old permissions for backward compatibility during transition
        // They will be removed in a future migration after code is fully updated
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove new permissions
        foreach (PermissionMetadata::getAllNewPermissions() as $permissionName) {
            Permission::where('name', $permissionName)->delete();
        }
    }
};
