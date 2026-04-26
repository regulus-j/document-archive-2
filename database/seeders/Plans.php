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

        $userLimits = Feature::where('key', 'user-limits')->first();
        $teamLimits = Feature::where('key', 'team-limits')->first();
        $customRoles = Feature::where('key', 'custom-roles')->first();
        $storageLimits = Feature::where('key', 'storage-limits')->first();

        $this->attachPlanFeature($basicPlan, $userLimits, ['enabled' => true, 'amount' => 10]);
        $this->attachPlanFeature($basicPlan, $teamLimits, ['enabled' => true, 'amount' => 3]);
        $this->attachPlanFeature($basicPlan, $customRoles, ['enabled' => false, 'value' => null]);
        $this->attachPlanFeature($basicPlan, $storageLimits, ['enabled' => true, 'amount' => 2]);

        $this->attachPlanFeature($standardPlan, $userLimits, ['enabled' => true, 'amount' => 30]);
        $this->attachPlanFeature($standardPlan, $teamLimits, ['enabled' => true, 'amount' => 10]);
        $this->attachPlanFeature($standardPlan, $customRoles, ['enabled' => true, 'amount' => 5]);
        $this->attachPlanFeature($standardPlan, $storageLimits, ['enabled' => true, 'amount' => 10]);

        $this->attachPlanFeature($premiumPlan, $userLimits, ['enabled' => true, 'amount' => 100]);
        $this->attachPlanFeature($premiumPlan, $teamLimits, ['enabled' => true, 'amount' => 20]);
        $this->attachPlanFeature($premiumPlan, $customRoles, ['enabled' => true, 'amount' => 20]);
        $this->attachPlanFeature($premiumPlan, $storageLimits, ['enabled' => true, 'amount' => 50]);
    }

    private function attachPlanFeature(Plan $plan, ?Feature $feature, array $pivotData): void
    {
        if ($feature) {
            $plan->features()->syncWithoutDetaching([$feature->id => $pivotData]);
        }
    }
}
