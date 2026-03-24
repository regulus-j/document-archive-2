<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', 1)->paginate(15);

        if (auth()->check() && auth()->user()->isAdmin()) {
            return view('admin.plans-index', compact('plans'));
        }
        return view('plans.index', compact('plans'));
    }

    public function register(Plan $plan)
    {
        return view('auth.register', compact('plan'));
    }

    public function subscribe(Request $request, Plan $plan)
    {
        // Subscription logic here
        return redirect()->route('plans.index')->with('success', 'Successfully subscribed to plan!');
    }

    public function create()
    {
        $features = Feature::all();
        return view('plans.create', compact('features'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'is_active' => 'nullable|boolean',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
        ]);
    
        // Create the plan without features first
        $plan = Plan::create([
            'plan_name' => $validated['plan_name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'is_active' => isset($validated['is_active']),
        ]);
    
        // Attach features with their enabled status
        if (isset($validated['features']) && is_array($validated['features'])) {
            foreach ($validated['features'] as $featureId) {
                $plan->features()->attach($featureId, [
                    'enabled' => true,
                ]);
            }
        }
    
        // Also ensure all features that weren't selected are still attached but disabled
        $allFeatureIds = Feature::pluck('id')->toArray();
        $selectedFeatureIds = $validated['features'] ?? [];
        $disabledFeatureIds = array_diff($allFeatureIds, $selectedFeatureIds);
        
        foreach ($disabledFeatureIds as $featureId) {
            $plan->features()->attach($featureId, [
                'enabled' => false,
            ]);
        }
    
        return redirect()->route('plans.index')
            ->with('success', 'Plan created successfully');
    }

    public function show(Plan $plan)
    {
        $plan->load('features');
        return view('plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        $features = Feature::all();
        $planFeatures = $plan->features->where('pivot.enabled', true)->pluck('id')->toArray();
        
        return view('plans.edit', compact('plan', 'features', 'planFeatures'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'is_active' => 'nullable|boolean',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
        ]);

        $plan->update([
            'plan_name' => $validated['plan_name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'is_active' => isset($validated['is_active']),
        ]);

        // Get all features
        $allFeatures = Feature::all();
        $enabledFeatures = isset($validated['features']) ? $validated['features'] : [];

        // Build sync array
        $syncData = [];
        foreach ($allFeatures as $feature) {
            $syncData[$feature->id] = [
                'enabled' => in_array($feature->id, $enabledFeatures)
            ];
        }

        // Sync features
        $plan->features()->sync($syncData);

        return redirect()->route('plans.show', $plan)
            ->with('success', 'Plan updated successfully');
    }
}
