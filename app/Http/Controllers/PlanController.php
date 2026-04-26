<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->check() && auth()->user()->isAdmin();

        $plansQuery = Plan::with(['features' => function ($query) {
            $query->orderBy('name');
        }]);

        if (!$isAdmin) {
            $plansQuery->where('is_active', 1);
        }

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        if ($search !== '') {
            $plansQuery->where(function ($query) use ($search) {
                $query->where('plan_name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('billing_cycle', 'like', '%' . $search . '%')
                    ->orWhereHas('features', function ($featureQuery) use ($search) {
                        $featureQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($isAdmin && in_array($status, ['active', 'inactive'], true)) {
            $plansQuery->where('is_active', $status === 'active');
        }

        $plans = $plansQuery->orderBy('plan_name')->paginate(15)->withQueryString();

        if ($isAdmin) {
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
            'features.*.amount' => 'nullable|integer|min:0',
            'features.*.unit_label' => 'nullable|string|max:50',
        ]);

        $this->ensureNumericFeatureAmounts($validated['features'] ?? []);
    
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
                        'amount' => $feature->pivot->amount,
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
            'features.*.amount' => 'nullable|integer|min:0',
            'features.*.unit_label' => 'nullable|string|max:50',
        ]);

        $this->ensureNumericFeatureAmounts($validated['features'] ?? []);

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
            $amount = isset($featureConfig['amount']) && $featureConfig['amount'] !== ''
                ? (int) $featureConfig['amount']
                : null;
            $unitLabel = isset($featureConfig['unit_label']) ? trim((string) $featureConfig['unit_label']) : null;

            if ($unitLabel !== null && $unitLabel !== '' && $feature->unit_label !== $unitLabel) {
                $feature->forceFill(['unit_label' => $unitLabel])->save();
            }

            $syncData[$feature->id] = [
                'enabled' => $enabled,
                'amount' => $enabled ? $amount : null,
                'value' => null,
            ];
        }

        $plan->features()->sync($syncData);
    }

    /**
     * Ensure enabled features always submit a numeric amount.
     */
    protected function ensureNumericFeatureAmounts(array $features): void
    {
        $errors = [];

        foreach (Feature::orderBy('name')->get() as $feature) {
            $featureConfig = $features[$feature->id] ?? [];
            $enabled = filter_var($featureConfig['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $amount = $featureConfig['amount'] ?? null;

            if ($enabled && ($amount === null || $amount === '')) {
                $errors["features.{$feature->id}.amount"] = "{$feature->name} requires a numeric limit.";
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
