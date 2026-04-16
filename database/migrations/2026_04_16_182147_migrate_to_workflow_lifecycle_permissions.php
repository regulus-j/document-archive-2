<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new workflow lifecycle permissions
        $newPermissions = [
            'documents.manage',
            'documents.workflow.initiate',
            'documents.workflow.participate',
            'documents.workflow.admin',
        ];
        
        foreach ($newPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Migration mapping from old to new permissions
        $migrationMap = [
            // documents.manage = create + edit + delete
            'documents.manage' => ['document-create', 'documents.create', 'document-edit', 'documents.edit', 'document-delete', 'documents.delete'],
            
            // documents.workflow.initiate = release
            'documents.workflow.initiate' => ['document-release', 'documents.release'],
            
            // documents.workflow.participate = receive
            'documents.workflow.participate' => ['document-receive', 'documents.receive'],
        ];

        foreach ($migrationMap as $newPermission => $oldPermissions) {
            $newPerm = Permission::where('name', $newPermission)->first();
            if (!$newPerm) {
                continue;
            }

            foreach ($oldPermissions as $oldPermName) {
                $oldPerm = Permission::where('name', $oldPermName)->first();
                if (!$oldPerm) {
                    continue;
                }

                // Migrate role associations
                $roleIds = DB::table('role_has_permissions')
                    ->where('permission_id', $oldPerm->id)
                    ->pluck('role_id');

                foreach ($roleIds as $roleId) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $newPerm->id,
                        'role_id' => $roleId,
                    ]);
                }

                // Migrate user associations
                $modelPermissions = DB::table('model_has_permissions')
                    ->where('permission_id', $oldPerm->id)
                    ->get();

                foreach ($modelPermissions as $modelPerm) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $newPerm->id,
                        'model_type' => $modelPerm->model_type,
                        'model_id' => $modelPerm->model_id,
                    ]);
                }
            }
        }
        
        // Note: We keep old permissions for backward compatibility during transition
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove new workflow lifecycle permissions
        $newPermissions = [
            'documents.manage',
            'documents.workflow.initiate',
            'documents.workflow.participate',
            'documents.workflow.admin',
        ];
        
        foreach ($newPermissions as $permissionName) {
            Permission::where('name', $permissionName)->delete();
        }
    }
};
