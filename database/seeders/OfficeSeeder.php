<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\User;
use App\Models\CompanyAccount;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get companies to assign offices to
        $companies = CompanyAccount::all();
        
        if ($companies->isEmpty()) {
            // Create a company if none exist
            $companies = [CompanyAccount::factory()->create()];
        }
        
        foreach ($companies as $company) {
            // Get company users
            $companyUsers = $company->employees;
            
            if ($companyUsers->isEmpty()) {
                // Skip if no users in company
                continue;
            }
            
            // Create 2-5 offices per company
            $officeCount = rand(2, 5);
            
            for ($i = 0; $i < $officeCount; $i++) {
                // Create head office
                $headOffice = Office::factory()->create([
                    'company_id' => $company->id,
                    'name' => 'Head Office - ' . $company->company_name,
                    'office_lead' => $companyUsers->isNotEmpty() ? $companyUsers->random()->id : null,
                ]);
                
                // Attach users to this office
                $usersToAttach = $companyUsers->random(min(3, $companyUsers->count()))->pluck('id');
                $headOffice->users()->attach($usersToAttach);
                
                // Create 1-3 sub-offices
                $subOfficeCount = rand(1, 3);
                
                for ($j = 0; $j < $subOfficeCount; $j++) {
                    $subOffice = Office::factory()->create([
                        'company_id' => $company->id,
                        'name' => 'Branch Office ' . ($j + 1) . ' - ' . $company->company_name,
                        'parent_office_id' => $headOffice->id,
                        'office_lead' => $companyUsers->isNotEmpty() ? $companyUsers->random()->id : null,
                    ]);
                    
                    // Attach users to this sub-office
                    $usersToAttach = $companyUsers->random(min(2, $companyUsers->count()))->pluck('id');
                    $subOffice->users()->attach($usersToAttach);
                }
            }
        }
    }
}