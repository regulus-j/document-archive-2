<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds company_id to roles table to make roles company-specific.
     * Global roles (like super-admin) have company_id = NULL.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('guard_name');
            $table->foreign('company_id')->references('id')->on('company_accounts')->onDelete('cascade');
        });

        // Drop the existing unique index on name+guard_name
        // and create a new one that includes company_id
        Schema::table('roles', function (Blueprint $table) {
            // Spatie creates: roles_name_guard_name_unique
            $table->dropUnique('roles_name_guard_name_unique');
            $table->unique(['name', 'guard_name', 'company_id'], 'roles_name_guard_name_company_unique');
        });

        // Migrate existing data: create company-specific copies of company-admin and user roles
        // for each existing company
        $companies = DB::table('company_accounts')->pluck('id');
        $adminRole = DB::table('roles')->where('name', 'company-admin')->whereNull('company_id')->first();
        $userRole = DB::table('roles')->where('name', 'user')->whereNull('company_id')->first();

        foreach ($companies as $companyId) {
            // Create company-specific company-admin role
            if ($adminRole) {
                $newAdminId = DB::table('roles')->insertGetId([
                    'name' => 'company-admin',
                    'guard_name' => 'web',
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Copy permissions from the global role
                $adminPermissions = DB::table('role_has_permissions')
                    ->where('role_id', $adminRole->id)
                    ->pluck('permission_id');
                foreach ($adminPermissions as $permId) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permId,
                        'role_id' => $newAdminId,
                    ]);
                }

                // Re-assign users who belong to this company and have the global company-admin role
                $companyUserIds = DB::table('company_users')
                    ->where('company_id', $companyId)
                    ->pluck('user_id');

                DB::table('model_has_roles')
                    ->where('role_id', $adminRole->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->whereIn('model_id', $companyUserIds)
                    ->update(['role_id' => $newAdminId]);
            }

            // Create company-specific user role
            if ($userRole) {
                $newUserId = DB::table('roles')->insertGetId([
                    'name' => 'user',
                    'guard_name' => 'web',
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Copy permissions from the global role
                $userPermissions = DB::table('role_has_permissions')
                    ->where('role_id', $userRole->id)
                    ->pluck('permission_id');
                foreach ($userPermissions as $permId) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permId,
                        'role_id' => $newUserId,
                    ]);
                }

                // Re-assign users who belong to this company and have the global user role
                $companyUserIds = DB::table('company_users')
                    ->where('company_id', $companyId)
                    ->pluck('user_id');

                DB::table('model_has_roles')
                    ->where('role_id', $userRole->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->whereIn('model_id', $companyUserIds)
                    ->update(['role_id' => $newUserId]);
            }
        }

        // Delete the old global company-admin and user roles (keep super-admin global)
        if ($adminRole) {
            DB::table('role_has_permissions')->where('role_id', $adminRole->id)->delete();
            DB::table('roles')->where('id', $adminRole->id)->delete();
        }
        if ($userRole) {
            DB::table('role_has_permissions')->where('role_id', $userRole->id)->delete();
            DB::table('roles')->where('id', $userRole->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate global company-admin and user roles
        $adminId = DB::table('roles')->insertGetId([
            'name' => 'company-admin',
            'guard_name' => 'web',
            'company_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('roles')->insertGetId([
            'name' => 'user',
            'guard_name' => 'web',
            'company_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Move all company-specific role assignments back to global roles
        $companyAdminRoles = DB::table('roles')->where('name', 'company-admin')->whereNotNull('company_id')->pluck('id');
        $companyUserRoles = DB::table('roles')->where('name', 'user')->whereNotNull('company_id')->pluck('id');

        DB::table('model_has_roles')->whereIn('role_id', $companyAdminRoles)->update(['role_id' => $adminId]);
        DB::table('model_has_roles')->whereIn('role_id', $companyUserRoles)->update(['role_id' => $userId]);

        // Copy permissions from the first company-specific role
        if ($companyAdminRoles->isNotEmpty()) {
            $perms = DB::table('role_has_permissions')->where('role_id', $companyAdminRoles->first())->pluck('permission_id');
            foreach ($perms as $p) {
                DB::table('role_has_permissions')->insert(['permission_id' => $p, 'role_id' => $adminId]);
            }
        }
        if ($companyUserRoles->isNotEmpty()) {
            $perms = DB::table('role_has_permissions')->where('role_id', $companyUserRoles->first())->pluck('permission_id');
            foreach ($perms as $p) {
                DB::table('role_has_permissions')->insert(['permission_id' => $p, 'role_id' => $userId]);
            }
        }

        // Delete all company-specific roles
        DB::table('role_has_permissions')->whereIn('role_id', $companyAdminRoles)->delete();
        DB::table('role_has_permissions')->whereIn('role_id', $companyUserRoles)->delete();
        DB::table('roles')->whereIn('id', $companyAdminRoles)->delete();
        DB::table('roles')->whereIn('id', $companyUserRoles)->delete();

        // Also delete any custom company-specific roles
        $customRoles = DB::table('roles')->whereNotNull('company_id')->pluck('id');
        DB::table('role_has_permissions')->whereIn('role_id', $customRoles)->delete();
        DB::table('model_has_roles')->whereIn('role_id', $customRoles)->delete();
        DB::table('roles')->whereIn('id', $customRoles)->delete();

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_name_company_unique');
            $table->unique(['name', 'guard_name']);
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
