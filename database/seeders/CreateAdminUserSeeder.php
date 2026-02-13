<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\CompanyAccount;
use App\Models\CompanyUser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [
            [
                'email' => 'superadmin@example.com',
                'first_name' => 'SuperAdmin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ],
            [
                'email' => 'admin@example.com',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ],
            [
                'email' => 'user@example.com',
                'first_name' => 'Regular',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ],
        ];

        $adminUserId = null;
        $regularUserId = null;

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign roles based on email
            if ($userData['email'] === 'superadmin@example.com') {
                // Super-admin is a global role (no company_id)
                $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
                $user->assignRole($role);
            } elseif ($userData['email'] === 'admin@example.com') {
                $adminUserId = $user->id;
            } else if ($userData['email'] === 'user@example.com') {
                $regularUserId = $user->id;
            }
        }
        
        // Now create the company with the actual admin user ID
        if ($adminUserId) {
            // Create company — this triggers auto-creation of company-specific roles
            $company = CompanyAccount::firstOrCreate(
                ['id' => 1],
                [
                    'user_id' => $adminUserId,
                    'company_name' => 'Demo Company',
                    'registered_name' => 'Demo Company Ltd',
                    'company_email' => 'info@democompany.com',
                    'company_phone' => '123-456-7890',
                    'industry' => 'Technology',
                    'company_size' => 'Medium'
                ]
            );
            
            // Assign company-specific company-admin role
            $companyAdminRole = Role::where('name', 'company-admin')
                ->where('company_id', $company->id)
                ->first();
            if ($companyAdminRole) {
                $adminUser = User::find($adminUserId);
                $adminUser->assignRole($companyAdminRole);
            }

            // Attach admin to company
            $this->attachUserToCompany($adminUserId, $company->id);
            
            // Attach regular user to company and assign company-specific user role
            if ($regularUserId) {
                $this->attachUserToCompany($regularUserId, $company->id);
                
                $userRoleModel = Role::where('name', 'user')
                    ->where('company_id', $company->id)
                    ->first();
                if ($userRoleModel) {
                    $regularUser = User::find($regularUserId);
                    $regularUser->assignRole($userRoleModel);
                }
            }
        }
    }
    
    /**
     * Attach a user to a company
     */
    private function attachUserToCompany($userId, $companyId)
    {
        // Check if relationship already exists
        $exists = CompanyUser::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->exists();
            
        if (!$exists) {
            CompanyUser::create([
                'user_id' => $userId,
                'company_id' => $companyId
            ]);
        }
    }
}