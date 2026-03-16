<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures all required permissions exist in the database.
     * This prevents 500 errors during registration when roles try to sync permissions.
     */
    public function up(): void
    {
        $permissions = [
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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't delete permissions on rollback as they may be in use
        // Manual cleanup required if needed
    }
};
