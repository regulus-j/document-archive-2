<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // New dotted notation permissions
        $newPermissions = [
           // Roles
           'roles.view',
           'roles.create',
           'roles.edit',
           'roles.delete',
           
           // Users
           'users.view',
           'users.create',
           'users.edit',
           'users.delete',
           
           // Offices
           'offices.view',
           'offices.create',
           'offices.edit',
           'offices.delete',
           
           // Documents (new workflow lifecycle permissions)
           'documents.manage',
           'documents.workflow.initiate',
           'documents.workflow.participate',
           'documents.workflow.admin',
           
           // Audit
           'audit.view',
        ];
        
        // Legacy permissions (kept for backward compatibility during transition)
        $legacyPermissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'document-list',
           'document-create',
           'document-edit',
           'document-delete',
           'document-release',
           'document-receive',
           'audit-list',
           'user-list',
           'user-create',
           'user-delete',
           'user-edit',
           'office-list',
           'office-create',
           'office-delete',
           'office-edit',
        ];
        
        // Create new permissions
        foreach ($newPermissions as $permission) {
             Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
             ]);
        }
        
        // Create legacy permissions for backward compatibility
        foreach ($legacyPermissions as $permission) {
             Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
             ]);
        }
    }
}