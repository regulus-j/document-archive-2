<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the plans
        $basicPlan = Plan::firstOrCreate(
            ['plan_name' => 'Basic Plan'],
            [
                'description' => 'A basic plan suitable for small businesses.',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'is_active' => true,
            ]
        );

        $standardPlan = Plan::firstOrCreate(
            ['plan_name' => 'Standard Plan'],
            [
                'description' => 'A standard plan for growing businesses.',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'is_active' => true,
            ]
        );

        $premiumPlan = Plan::firstOrCreate(
            ['plan_name' => 'Premium Plan'],
            [
                'description' => 'A premium plan with all features included.',
                'price' => 59.99,
                'billing_cycle' => 'monthly',
                'is_active' => true,
            ]
        );

        // Get user limit features
        $users3 = Feature::where('key', 'users-3')->first();
        $users10 = Feature::where('key', 'users-10')->first();
        $users30 = Feature::where('key', 'users-30')->first();
        $users100 = Feature::where('key', 'users-100')->first();

        // Get team limit features
        $teams1 = Feature::where('key', 'teams-1')->first();
        $teams3 = Feature::where('key', 'teams-3')->first();
        $teams10 = Feature::where('key', 'teams-10')->first();
        $teams20 = Feature::where('key', 'teams-20')->first();

        // Get storage limit features
        $storage500mb = Feature::where('key', 'storage-500mb')->first();
        $storage2gb = Feature::where('key', 'storage-2gb')->first();
        $storage10gb = Feature::where('key', 'storage-10gb')->first();
        $storage50gb = Feature::where('key', 'storage-50gb')->first();

        // Assign features to Basic plan (10 users, 3 teams, 2GB)
        if ($users10) {
            $basicPlan->features()->syncWithoutDetaching([$users10->id => ['enabled' => true]]);
        }
        if ($teams3) {
            $basicPlan->features()->syncWithoutDetaching([$teams3->id => ['enabled' => true]]);
        }
        if ($storage2gb) {
            $basicPlan->features()->syncWithoutDetaching([$storage2gb->id => ['enabled' => true]]);
        }

        // Assign features to Standard plan (30 users, 10 teams, 10GB)
        if ($users30) {
            $standardPlan->features()->syncWithoutDetaching([$users30->id => ['enabled' => true]]);
        }
        if ($teams10) {
            $standardPlan->features()->syncWithoutDetaching([$teams10->id => ['enabled' => true]]);
        }
        if ($storage10gb) {
            $standardPlan->features()->syncWithoutDetaching([$storage10gb->id => ['enabled' => true]]);
        }

        // Assign features to Premium plan (100 users, 20 teams, 50GB)
        if ($users100) {
            $premiumPlan->features()->syncWithoutDetaching([$users100->id => ['enabled' => true]]);
        }
        if ($teams20) {
            $premiumPlan->features()->syncWithoutDetaching([$teams20->id => ['enabled' => true]]);
        }
        if ($storage50gb) {
            $premiumPlan->features()->syncWithoutDetaching([$storage50gb->id => ['enabled' => true]]);
        }

        // Get additional features
        // $documentStorage = Feature::where('key', 'document-storage')->first();
        // $advancedSharing = Feature::where('key', 'advanced-sharing')->first();
        // $analytics = Feature::where('key', 'analytics')->first();

        // Assign additional features to plans
        // if ($documentStorage) {
        //     $basicPlan->features()->syncWithoutDetaching([$documentStorage->id => ['enabled' => true]]);
        //     $standardPlan->features()->syncWithoutDetaching([$documentStorage->id => ['enabled' => true]]);
        //     $premiumPlan->features()->syncWithoutDetaching([$documentStorage->id => ['enabled' => true]]);
        // }

        // if ($advancedSharing) {
        //     $basicPlan->features()->syncWithoutDetaching([$advancedSharing->id => ['enabled' => false]]);
        //     $standardPlan->features()->syncWithoutDetaching([$advancedSharing->id => ['enabled' => true]]);
        //     $premiumPlan->features()->syncWithoutDetaching([$advancedSharing->id => ['enabled' => true]]);
        // }

        // if ($analytics) {
        //     $basicPlan->features()->syncWithoutDetaching([$analytics->id => ['enabled' => false]]);
        //     $standardPlan->features()->syncWithoutDetaching([$analytics->id => ['enabled' => false]]);
        //     $premiumPlan->features()->syncWithoutDetaching([$analytics->id => ['enabled' => true]]);
        // }
    }
}
