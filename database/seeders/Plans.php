<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;

class Plans extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        $users10 = Feature::where('key', 'users-10')->first();
        $users30 = Feature::where('key', 'users-30')->first();
        $users100 = Feature::where('key', 'users-100')->first();
        $teams3 = Feature::where('key', 'teams-3')->first();
        $teams10 = Feature::where('key', 'teams-10')->first();
        $teams20 = Feature::where('key', 'teams-20')->first();
        $storage2gb = Feature::where('key', 'storage-2gb')->first();
        $storage10gb = Feature::where('key', 'storage-10gb')->first();
        $storage50gb = Feature::where('key', 'storage-50gb')->first();

        if ($users10) {
            $basicPlan->features()->syncWithoutDetaching([$users10->id => ['enabled' => true, 'value' => '10 users']]);
        }
        if ($teams3) {
            $basicPlan->features()->syncWithoutDetaching([$teams3->id => ['enabled' => true, 'value' => '3 teams']]);
        }
        if ($storage2gb) {
            $basicPlan->features()->syncWithoutDetaching([$storage2gb->id => ['enabled' => true, 'value' => '2 GB']]);
        }

        if ($users30) {
            $standardPlan->features()->syncWithoutDetaching([$users30->id => ['enabled' => true, 'value' => '30 users']]);
        }
        if ($teams10) {
            $standardPlan->features()->syncWithoutDetaching([$teams10->id => ['enabled' => true, 'value' => '10 teams']]);
        }
        if ($storage10gb) {
            $standardPlan->features()->syncWithoutDetaching([$storage10gb->id => ['enabled' => true, 'value' => '10 GB']]);
        }

        if ($users100) {
            $premiumPlan->features()->syncWithoutDetaching([$users100->id => ['enabled' => true, 'value' => '100 users']]);
        }
        if ($teams20) {
            $premiumPlan->features()->syncWithoutDetaching([$teams20->id => ['enabled' => true, 'value' => '20 teams']]);
        }
        if ($storage50gb) {
            $premiumPlan->features()->syncWithoutDetaching([$storage50gb->id => ['enabled' => true, 'value' => '50 GB']]);
        }
    }
}
