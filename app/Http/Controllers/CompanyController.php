<?php

namespace App\Http\Controllers;

use App\Models\CompanySubscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\CompanyAccount;
use App\Models\CompanyAddress;
use App\Models\CompanyUser;


class CompanyController extends Controller
{
    public function index()
    {
        // Only super-admins can view the list of all companies
        $this->authorize('viewAny', CompanyAccount::class);

        if (auth()->user()->isSuperAdmin()) {
            $query = CompanyAccount::with([
                'user',
                'users',
                'subscriptions' => function ($subscriptionQuery) {
                    $subscriptionQuery->withoutGlobalScope('unexpired')
                        ->orderByDesc('start_date')
                        ->with('plan');
                },
            ]);

            $search = trim((string) request('search', ''));
            if ($search !== '') {
                $query->where(function ($companyQuery) use ($search) {
                    $companyQuery->where('company_name', 'like', "%{$search}%")
                        ->orWhere('registered_name', 'like', "%{$search}%")
                        ->orWhere('company_email', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($ownerQuery) use ($search) {
                            $ownerQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            $status = request('status');
            if ($status === 'no_subscription') {
                $query->whereDoesntHave('subscriptions');
            } elseif (in_array($status, ['active', 'pending', 'canceled', 'expired'], true)) {
                $query->whereHas('subscriptions', function ($subscriptionQuery) use ($status) {
                    $subscriptionQuery->withoutGlobalScope('unexpired')->where('status', $status);
                });
            }

            $companies = $query->orderBy('company_name')->paginate(15)->withQueryString();
            $plans = Plan::where('is_active', true)->orderBy('plan_name')->get();
            $availableUsers = User::orderBy('first_name')->orderBy('last_name')->get();

            return view('admin.companies-index', compact('companies', 'plans', 'availableUsers'));
        }

        // Regular users should only see their own company
        $company = auth()->user()->companies()->first();
        
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('info', 'You need to create a company first.');
        }
        
        return redirect()->route('companies.show', $company->id);
    }

    public function create()
    {
        // Check if the current user already owns a company (unless they're a super admin)
        if (!auth()->user()->isSuperAdmin()) {
            $existingCompany = CompanyAccount::where('user_id', auth()->id())->first();
            if ($existingCompany) {
                return redirect()->route('dashboard')
                    ->with('error', 'You already own a company. Each user can only own one company.');
            }
        }

        // Show the form for creating a new company.
        return view('companies.create');
    }

    public function store(Request $request)
    {
        // Use custom validation rules from the model to enforce one company per user
        $request->validate(CompanyAccount::rules());

        $validated = $request->only([
            'user_id',
            'company_name',
            'registered_name',
            'company_email',

        ]);

        if($request->part == '2') {
            $addressValidated = $request->validate([
            'address'  => 'required|string|max:255',
            'city'     => 'required|string|max:255',
            'state'    => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country'  => 'required|string|max:255',
            ]);

            // Update the company with address details
            CompanyAccount::create($validated);
            $company = CompanyAccount::latest()->first();
            $company->addresses()->create($addressValidated);
            
            // Assign the company-specific company-admin role to the company owner
            $companyOwner = \App\Models\User::find($validated['user_id'] ?? auth()->id());
            $companyAdminRole = \App\Models\Role::where('name', 'company-admin')
                ->where('company_id', $company->id)
                ->first();
            if ($companyAdminRole && $companyOwner) {
                $companyOwner->assignRole($companyAdminRole);
            }

            // Create the company-user relationship in the pivot table
            CompanyUser::create([
                'company_id' => $company->id,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('companies.show', $company->id)
                ->with('success', 'Company created successfully!');
        }
        return redirect()->route('companies.create')->with('success', 'Company created successfully.')->withInput();
    }

    public function show(CompanyAccount $company)
{
    $this->authorize('view', $company);

    // Load users if not already eager-loaded
    $company->load('users');

    return view('companies.show', compact('company'));
}


    public function edit(CompanyAccount $company)
    {
        // Authorization check - only super-admin or company owner can edit
        $this->authorize('update', $company);

        // Get the authenticated user
        $authUser = auth()->guard('web')->user();

        // Initialize $users based on role
        // Super-admin can see all users, company-admin only sees their company's employees
        if($authUser && $authUser->isSuperAdmin()) {
            $users = User::paginate(10);
        } else {
            $users = $company->employees()->paginate(10);
        }

        // Show the form for editing the specified company.
        return view('companies.edit', compact('company', 'users'));
    }

    public function update(Request $request, CompanyAccount $company)
    {
        // Use custom validation rules from the model to enforce one company per user
        // Passing the company ID to exclude the current company from validation
        $rules = CompanyAccount::rules($company->id);
        $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        $rules['color_theme'] = 'nullable|string|in:' . implode(',', array_keys(CompanyAccount::colorPalette()));
        $request->validate($rules);

        $validated = $request->only([
            'user_id',
            'company_name',
            'registered_name',
            'company_email',
            'company_phone',
            'color_theme',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if present
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company_logos', 'public');
        }

        $company->update($validated);

        // Check if the current user is an admin or the company owner
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
        } else {
            // For company owners, redirect to dashboard or company show page
            return redirect()->route('dashboard')->with('success', 'Company information updated successfully.');
        }
    }

    public function destroy(CompanyAccount $company)
    {
        // Only super-admin can delete companies
        $this->authorize('delete', $company);

        // Remove the specified company from storage.
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }

    public function userCompanies($userId)
    {
        $companies = CompanyAccount::where('user_id', $userId)->with('address')->get();
        return view('companies.userManaged', compact('companies'));
    }

    public function adminCreateCompany(Request $request)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'registered_name' => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'owner_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($validated) {
            $company = CompanyAccount::create([
                'user_id' => $validated['owner_id'],
                'company_name' => $validated['company_name'],
                'registered_name' => $validated['registered_name'],
                'company_email' => $validated['company_email'] ?? null,
                'company_phone' => $validated['company_phone'] ?? null,
            ]);

            CompanyUser::firstOrCreate([
                'company_id' => $company->id,
                'user_id' => $validated['owner_id'],
            ]);
        });

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function adminUpdateDetails(Request $request, CompanyAccount $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'registered_name' => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'owner_id' => 'required|exists:users,id',
        ]);

        $company->update([
            'company_name' => $validated['company_name'],
            'registered_name' => $validated['registered_name'],
            'company_email' => $validated['company_email'] ?? null,
            'company_phone' => $validated['company_phone'] ?? null,
            'user_id' => $validated['owner_id'],
        ]);

        CompanyUser::firstOrCreate([
            'company_id' => $company->id,
            'user_id' => $validated['owner_id'],
        ]);

        return redirect()->route('companies.index')->with('success', 'Company details updated successfully.');
    }

    public function adminAddMember(Request $request, CompanyAccount $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        CompanyUser::firstOrCreate([
            'company_id' => $company->id,
            'user_id' => $validated['user_id'],
        ]);

        return redirect()->route('companies.index')->with('success', 'Company member added successfully.');
    }

    public function adminCreateMember(Request $request, CompanyAccount $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($validated, $company) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            CompanyUser::firstOrCreate([
                'company_id' => $company->id,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('companies.index')->with('success', 'New user created and added to company.');
    }

    public function adminRemoveMember(CompanyAccount $company, User $user)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        if ((int) $company->user_id === (int) $user->id) {
            return redirect()->route('companies.index')->with('error', 'Cannot remove the current company owner.');
        }

        CompanyUser::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->route('companies.index')->with('success', 'Company member removed successfully.');
    }

    public function adminUpdateSubscriptionPlan(Request $request, CompanyAccount $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $latestSubscription = $company->subscriptions()
            ->withoutGlobalScope('unexpired')
            ->orderByDesc('start_date')
            ->first();

        if ($latestSubscription) {
            $latestSubscription->update(['plan_id' => $validated['plan_id']]);
        } else {
            CompanySubscription::create([
                'company_id' => $company->id,
                'plan_id' => $validated['plan_id'],
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'status' => 'active',
                'auto_renew' => false,
            ]);
        }

        return redirect()->route('companies.index')->with('success', 'Subscription plan updated successfully.');
    }

    public function adminManualRenew(Request $request, CompanyAccount $company)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($company, $validated) {
            CompanySubscription::where('company_id', $company->id)
                ->withoutGlobalScope('unexpired')
                ->where('status', 'active')
                ->update(['status' => 'canceled', 'auto_renew' => false]);

            CompanySubscription::create([
                'company_id' => $company->id,
                'plan_id' => $validated['plan_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'active',
                'auto_renew' => (bool) ($validated['auto_renew'] ?? false),
            ]);
        });

        return redirect()->route('companies.index')->with('success', 'Manual renewal completed successfully.');
    }

}
