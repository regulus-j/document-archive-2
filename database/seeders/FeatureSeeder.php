<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'name' => 'User Limits',
                'key' => 'user-limits',
                'description' => 'Set the maximum number of users included in the plan',
                'unit_label' => 'users',
            ],
            [
                'name' => 'Team Limits',
                'key' => 'team-limits',
                'description' => 'Set the maximum number of teams or offices included in the plan',
                'unit_label' => 'teams',
            ],
            [
                'name' => 'Custom Roles',
                'key' => 'custom-roles',
                'description' => 'Control how many custom roles or role templates are available',
                'unit_label' => 'roles',
            ],
            [
                'name' => 'Storage Limits',
                'key' => 'storage-limits',
                'description' => 'Set the document storage capacity included in the plan',
                'unit_label' => 'GB',
            ],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(['key' => $feature['key']], $feature);
        }
    }
}
