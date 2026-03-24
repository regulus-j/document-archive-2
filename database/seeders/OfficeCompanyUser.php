<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Models\Office;

class OfficeCompanyUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // First check if we have users, if not create some
        if (User::count() == 0) {
            User::factory()->count(5)->create();
        }
        
        // Find admin and regular user by email
        $adminUser = User::where('email', 'admin@example.com')->first();
        $regularUser = User::where('email', 'user@example.com')->first();
        
        // Get existing users to use for companies
        $users = User::all();
        
        // Check if company with ID 1 exists
        $companyOne = CompanyAccount::find(1);
        
        // Create company ID 1 if it doesn't exist, and assign admin as owner if possible
        if (!$companyOne && $adminUser) {
            $companyOne = CompanyAccount::factory()->create([
                'id' => 1,
                'user_id' => $adminUser->id
            ]);
        } elseif (!$companyOne) {
            $companyOne = CompanyAccount::factory()->create([
                'id' => 1,
                'user_id' => $users->first()->id
            ]);
        }
        
        // Create additional companies with valid user_id references
        $companies = [$companyOne];
        foreach ($users->take(2) as $index => $user) {
            // Skip if this user is already assigned to company one
            if ($user->id == $companyOne->user_id) {
                continue;
            }
            
            $company = CompanyAccount::factory()->create([
                'user_id' => $user->id
            ]);
            $companies[] = $company;
        }
        
        // Ensure all companies have offices (especially company ID 1)
        $offices = [];
        foreach ($companies as $company) {
            // Check if company already has offices
            $existingOffices = Office::where('company_id', $company->id)->get();
            
            if ($existingOffices->count() == 0) {
                // Create new offices only if none exist
                $companyOffices = Office::factory()
                    ->count(5)
                    ->create(['company_id' => $company->id]);
                $offices = array_merge($offices, $companyOffices->toArray());
            } else {
                // Use existing offices
                $offices = array_merge($offices, $existingOffices->toArray());
            }
        }
        
        // Create more users and assign each to at least one office
        User::factory()->count(11)->create()->each(function ($user) use ($offices) {
            // Determine how many offices to assign to this user (1-3)
            $numOffices = rand(1, 3);
            
            // Get random office IDs
            $officeIds = array_column($offices, 'id');
            shuffle($officeIds);
            $selectedOfficeIds = array_slice($officeIds, 0, $numOffices);
            
            // Assign offices to user
            $user->offices()->attach($selectedOfficeIds);
            
            // Assign user to a company
            $companyId = $offices[array_search($selectedOfficeIds[0], array_column($offices, 'id'))]['company_id'];
            \App\Models\CompanyUser::create([
                'user_id' => $user->id,
                'company_id' => $companyId
            ]);
        });
        
        // Make sure we have company 1 offices
        $companyOneOffices = array_filter($offices, function($office) {
            return $office['company_id'] == 1;
        });
        
        if (empty($companyOneOffices) && isset($companies[0]) && $companies[0]->id == 1) {
            $companyOneOffices = Office::factory()
                ->count(3)
                ->create(['company_id' => 1])
                ->toArray();
            $offices = array_merge($offices, $companyOneOffices);
        }
        
        // Filter only company 1 offices for admin and regular user
        $companyOneOfficeIds = array_column($companyOneOffices, 'id');
        
        // If admin and regular user exist, ensure they're assigned to the same company (company ID 1)
        // and the regular user has an office
        if ($adminUser) {
            // Ensure admin is attached to company 1
            if (!\App\Models\CompanyUser::where('user_id', $adminUser->id)->where('company_id', 1)->exists()) {
                \App\Models\CompanyUser::create([
                    'user_id' => $adminUser->id,
                    'company_id' => 1
                ]);
            }
            
            // Assign admin to an office if not already assigned
            if ($adminUser->offices()->count() == 0 && !empty($companyOneOfficeIds)) {
                $adminUser->offices()->attach($companyOneOfficeIds[0]);
            }
        }
        
        if ($regularUser) {
            // Ensure regular user is attached to company 1
            if (!\App\Models\CompanyUser::where('user_id', $regularUser->id)->where('company_id', 1)->exists()) {
                \App\Models\CompanyUser::create([
                    'user_id' => $regularUser->id,
                    'company_id' => 1
                ]);
            }
            
            // Always assign regular user to an office (even if already assigned before)
            if (!empty($companyOneOfficeIds)) {
                // Pick a random office from company 1
                shuffle($companyOneOfficeIds);
                $selectedOfficeId = $companyOneOfficeIds[0];
                
                // Clear any existing office assignments and add the new one
                $regularUser->offices()->detach();
                $regularUser->offices()->attach($selectedOfficeId);
            }
        }
    }
}