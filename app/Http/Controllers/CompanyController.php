<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
            $companies = CompanyAccount::with(['subscriptions.plan', 'user'])
                ->get()
                ->map(function ($company) {
                    $subscription = $company->subscriptions->first();
                    return [
                        'id' => $company->id,
                        'name' => $company->company_name,
                        'owner' => $company->user->first_name . ' ' . $company->user->last_name,
                        'status' => $subscription ? $subscription->status : 'No subscription',
                        'plan' => $subscription ? $subscription->plan->plan_name : 'No plan'
                    ];
                });
            return view('admin.companies-index', compact('companies'));
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
        $request->validate(CompanyAccount::rules($company->id));

        $validated = $request->only([
            'user_id',
            'company_name',
            'registered_name',
            'company_email',
            'company_phone',
        ]);

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

}
