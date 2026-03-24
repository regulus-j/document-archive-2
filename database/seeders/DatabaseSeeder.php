<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\CompanyAccount;
use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in proper order
        $this->call([
            FeatureSeeder::class,          // Create features first
            PlanSeeder::class,             // Create plans with features
            PermissionTableSeeder::class,   // Set up permissions
            RolesSeeder::class,            // Create roles with permissions
            CreateAdminUserSeeder::class,   // Create admin and initial users
            OfficeCompanyUser::class,       // Set up offices and company relationships
            DocumentCategories::class,      // Create document categories
        ]);

        // Ensure any remaining users without roles get the basic user role for their company
        User::whereDoesntHave('roles')->get()->each(function ($user) {
            $company = $user->companies()->first();
            if ($company) {
                $userRole = \App\Models\Role::where('name', 'user')
                    ->where('company_id', $company->id)
                    ->first();
                if ($userRole) {
                    $user->assignRole($userRole);
                }
            }
        });

        // Only handle edge case where a company-admin somehow doesn't have a company
        $companyAdmins = User::role('company-admin')
            ->whereDoesntHave('companies')
            ->where('email', '!=', 'superadmin@example.com')
            ->get();

        foreach ($companyAdmins as $admin) {
            // Create a company for the admin if they don't have one
            // This triggers auto-creation of company-specific default roles
            $company = CompanyAccount::create([
                'user_id' => $admin->id,
                'company_name' => 'Company of ' . $admin->first_name . ' ' . $admin->last_name,
                'registered_name' => 'Company of ' . $admin->first_name . ' ' . $admin->last_name . ' Ltd',
                'company_email' => $admin->email,
                'company_phone' => '000-000-0000',
                'industry' => 'Other',
                'company_size' => 'Small'
            ]);

            // Assign the company-specific company-admin role
            $companyAdminRole = \App\Models\Role::where('name', 'company-admin')
                ->where('company_id', $company->id)
                ->first();
            if ($companyAdminRole) {
                $admin->syncRoles([$companyAdminRole]);
            }

            // Create company user relationship
            DB::table('company_users')->insert([
                'user_id' => $admin->id,
                'company_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
