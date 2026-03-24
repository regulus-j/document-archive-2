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
            // User limits
            [
                'name' => '3 Users',
                'key' => 'users-3',
                'description' => 'Maximum of 3 users',
            ],
            [
                'name' => '10 Users',
                'key' => 'users-10',
                'description' => 'Maximum of 10 users',
            ],
            [
                'name' => '30 Users',
                'key' => 'users-30',
                'description' => 'Maximum of 30 users',
            ],
            [
                'name' => '100 Users',
                'key' => 'users-100',
                'description' => 'Maximum of 100 users',
            ],

            // Team limits
            [
                'name' => '1 Team',
                'key' => 'teams-1',
                'description' => 'Maximum of 1 team',
            ],
            [
                'name' => '3 Teams',
                'key' => 'teams-3',
                'description' => 'Maximum of 3 teams',
            ],
            [
                'name' => '10 Teams',
                'key' => 'teams-10',
                'description' => 'Maximum of 10 teams',
            ],
            [
                'name' => '20 Teams',
                'key' => 'teams-20',
                'description' => 'Maximum of 20 teams',
            ],

            // Storage limits
            [
                'name' => '500 MB Storage',
                'key' => 'storage-500mb',
                'description' => '500 MB storage capacity',
            ],
            [
                'name' => '2 GB Storage',
                'key' => 'storage-2gb',
                'description' => '2 GB storage capacity',
            ],
            [
                'name' => '10 GB Storage',
                'key' => 'storage-10gb',
                'description' => '10 GB storage capacity',
            ],
            [
                'name' => '50 GB Storage',
                'key' => 'storage-50gb',
                'description' => '50 GB storage capacity',
            ],
            // [
            //     'name' => 'Document Storage',
            //     'key' => 'document-storage',
            //     'description' => 'Store and manage documents in the system',
            // ],
            // [
            //     'name' => 'Advanced Sharing',
            //     'key' => 'advanced-sharing',
            //     'description' => 'Share documents with advanced permission controls',
            // ],
            // [
            //     'name' => 'Analytics',
            //     'key' => 'analytics',
            //     'description' => 'View detailed analytics on document usage',
            // ],

        ];

        foreach ($features as $feature) {
            Feature::firstOrCreate(['key' => $feature['key']], $feature);
        }
    }
}
