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
        $features = Feature::orderBy('name')->get();
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
            'features.*.enabled' => 'nullable|boolean',
            'features.*.value' => 'nullable|string|max:255',
        ]);
    
        // Create the plan without features first
        $plan = Plan::create([
            'plan_name' => $validated['plan_name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'is_active' => isset($validated['is_active']),
        ]);
    
        $this->syncPlanFeatures($plan, $validated['features'] ?? []);
    
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
        $features = Feature::orderBy('name')->get();
        $planFeatures = $plan->features
            ->mapWithKeys(function ($feature) {
                return [
                    $feature->id => [
                        'enabled' => (bool) $feature->pivot->enabled,
                        'value' => $feature->pivot->value,
                    ],
                ];
            })
            ->all();

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
            'features.*.enabled' => 'nullable|boolean',
            'features.*.value' => 'nullable|string|max:255',
        ]);

        $plan->update([
            'plan_name' => $validated['plan_name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'is_active' => isset($validated['is_active']),
        ]);

        $this->syncPlanFeatures($plan, $validated['features'] ?? []);

        return redirect()->route('plans.show', $plan)
            ->with('success', 'Plan updated successfully');
    }

    /**
     * Sync all catalog features against a plan using the submitted values.
     */
    protected function syncPlanFeatures(Plan $plan, array $features): void
    {
        $syncData = [];

        foreach (Feature::orderBy('name')->get() as $feature) {
            $featureConfig = $features[$feature->id] ?? [];
            $enabled = filter_var($featureConfig['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $value = isset($featureConfig['value']) ? trim((string) $featureConfig['value']) : null;

            $syncData[$feature->id] = [
                'enabled' => $enabled,
                'value' => $enabled && $value !== '' ? $value : null,
            ];
        }

        $plan->features()->sync($syncData);
    }
}
