<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create the global super-admin role here.
        // Company-specific roles (company-admin, user) are auto-created
        // when a company is created via CompanyAccount::booted().

        $allPermissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'office-list',
            'office-create',
            'office-delete',
            'office-edit',
            'document-list',
            'document-create',
            'document-edit',
            'document-delete',
            'document-release',
            'document-receive',
            'audit-list',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete'
        ];

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);
        $superAdminRole->syncPermissions($allPermissions);
    }
}
