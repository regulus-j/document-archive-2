<?php

namespace App\Http\Controllers;

use App\Mail\userInvite;
use App\Models\Office;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Models\CompanySubscription;
use App\Models\Plan;
use App\Models\CompanyUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class UserController extends Controller
{
    /**
     * Constructor to set up middleware
     */
    public function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the users.
     */

    public function index(Request $request): View
    {
        $authUser = Auth::user();

        // Only super-admins can see the super-admin role in filters
        if ($authUser->hasRole('super-admin')) {
            $roles = Role::all();
        } elseif ($authUser->hasRole('company-admin')) {
            $company = $authUser->companies()->first();
            $roles = $company ? Role::companyOnly($company->id)->where('name', '!=', 'super-admin')->get() : collect();
        } else {
            $company = $authUser->companies()->first();
            $roles = $company ? Role::where('name', 'user')->where('company_id', $company->id)->get() : collect();
        }

        // Super-admin: see all users
        if ($authUser->hasRole('super-admin')) {
            return $this->showRegistered();
        }

        // Company admin: see users in their company
        if ($authUser->isCompanyAdmin()) {
            $company = $authUser->companies()->first();
            if ($company) {
                $users = $company->employees()->paginate(5);
            } else {
                $users = collect();
            }
        } else {
            // Regular user: see only themselves
            $users = User::where('id', $authUser->id)->paginate(5);
        }


        return view('users.index', compact('users', 'roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    public function showRegistered(): View
    {
        $users = User::with(['companies.subscriptions.plan'])
            ->paginate(10);

        $roles = Role::all();

        return view('admin.users-index', compact('users', 'roles'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Search for users based on name, email, and role.
     */
    public function search(Request $request): View
    {
        $authUser = Auth::user();

        // Only super-admins can see the super-admin role in filters
        if ($authUser->hasRole('super-admin')) {
            $roles = Role::all();
        } elseif ($authUser->hasRole('company-admin')) {
            $company = $authUser->companies()->first();
            $roles = $company ? Role::companyOnly($company->id)->where('name', '!=', 'super-admin')->get() : collect();
        } else {
            $company = $authUser->companies()->first();
            $roles = $company ? Role::where('name', 'user')->where('company_id', $company->id)->get() : collect();
        }

        // Fetch teams for filter
        if ($authUser->hasRole('super-admin')) {
            $teams = \App\Models\Office::all();
        } elseif ($authUser->isCompanyAdmin()) {
            $company = $authUser->companies()->first();
            $teams = $company ? $company->offices()->get() : collect();
        } else {
            $teams = $authUser->offices()->get();
        }

        // Super-admin: can search all users
        if ($authUser->hasRole('super-admin')) {
            $query = User::query();
        } elseif ($authUser->isCompanyAdmin()) {
            // Company admin: can search only users in their company
            $company = $authUser->companies()->first();
            if ($company) {
                $query = $company->employees();
            } else {
                $query = User::whereRaw('1 = 0'); // No company, no results
            }
        } else {
            // Regular user: can search only themselves
            $query = User::where('id', $authUser->id);
        }

        if ($request->filled('name')) {
            $query->where('first_name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('role_search')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role_search . '%');
            });
        }

        // Filter by team name (text search)
        if ($request->filled('team_search')) {
            $query->whereHas('offices', function ($q) use ($request) {
                $q->where('offices.name', 'like', '%' . $request->team_search . '%');
            });
        }

        $users = $query->paginate(5);

        return view('users.index', compact('users', 'roles', 'teams'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $userCompany = auth()->user()->companies()->first();

        if (!$userCompany->canAddUser()) {
            return redirect()->route('users.index')
                ->with('error', 'Max users reached, upgrade your plan to add more.');
        }

        // Filter roles based on user permissions
        if (auth()->user()->hasRole('super-admin')) {
            // Super admins can see all roles
            $roles = Role::pluck('name', 'id')->all();
        } else {
            // Others see only their company's roles
            $company = auth()->user()->companies()->first();
            $roles = $company
                ? Role::companyOnly($company->id)->pluck('name', 'id')->all()
                : [];
        }

        $company = auth()->user()->companies()->first();
        // Get offices and format them as id => name pairs
        $offices = Office::where('company_id', $company->id)
            ->get()
            ->pluck('name', 'id')
            ->all();

        // Fetch users that belong to the same company & offices
        $users = User::whereHas('offices', function ($query) use ($offices) {
            $query->whereIn('offices.id', array_keys($offices));
        })->get();

        return view('users.create', compact('roles', 'offices', 'userCompany', 'users'));
    }


    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $userCompany = auth()->user()->companies()->first();

        if (!$userCompany->canAddUser()) {
            return redirect()->route('users.index')
                ->with('error', 'Max users reached, upgrade your plan to add more.');
        }

        $temp_pass = Str::random(12);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255', // Changed from string to nullable|string
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'offices' => 'required|array',
            'offices.*' => 'exists:offices,id',
            'roles' => 'required',
            'companies' => 'required|exists:company_accounts,id'
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($temp_pass), // B-10 FIX: use Hash facade for consistency.
        ]);

        // Attach multiple offices
        $user->offices()->attach($request->offices);

        // Assign roles by ID to ensure company-specific roles are used
        $roleIds = $request->input('roles');
        $roleModels = Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roleModels);

        $roleNames = $user->roles->pluck('name')->implode(', ');

        // Queue the email instead of sending it synchronously
        try {
            Mail::to($user->email)
                ->send(new UserInvite(
                    $user->first_name,
                    $user->email,
                    $temp_pass,
                    $roleNames,
                    route('login')
                ));
        } catch (\Exception $e) {
            \Log::error('Failed to queue invitation email: ' . $e->getMessage());
            // Continue execution even if email queueing fails
        }

        $temp_pass = null;

        // Fix company association by properly handling array or single value
        $companyId = auth()->user()->companies()->first()->id;

        // If user is not a super admin, use their company
        if (!auth()->user()->hasRole('super-admin')) {
            $userCompany = auth()->user()->companies()->first();
            if ($userCompany) {
                $companyId = $userCompany->id;
            }
        }

        CompanyUser::create([
            'company_id' => $companyId,
            'user_id'     => $user->id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified user.
     */
    public function show($id): View
    {
        $user = User::findOrFail($id);
        $authUser = User::findOrFail(Auth::id());

        // Super admin can see all users
        if ($authUser->hasRole('super-admin')) {
            return view('users.show', compact('user'));
        }

        // Company admin can only see users in their company
        if ($authUser->isCompanyAdmin()) {
            $company = $authUser->companies()->first();

            if (!$company) {
                abort(403, 'You must be associated with a company to view user details.');
            }

            // Check if user belongs to the company
            if (!$user->companies->contains($company->id)) {
                abort(403, 'You can only view users from your company.');
            }
        } else {
            // Regular users can only see themselves
            if ($user->id !== $authUser->id) {
                abort(403, 'Unauthorized access.');
            }
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id): View
    {
        $user = User::findOrFail($id);
        $authUser = User::findOrFail(Auth::id());

        // Super admin can edit all users
        if ($authUser->hasRole('super-admin')) {
            $roles = Role::pluck('name', 'id')->all();
        } elseif ($authUser->isCompanyAdmin()) {
            // Company admin checks
            $company = $authUser->companies()->first();
            if (!$company) {
                abort(403, 'You must be associated with a company to edit users.');
            }

            // Check if target user belongs to admin's company
            if (!$user->companies->contains($company->id)) {
                abort(403, 'You can only edit users from your company.');
            }

            // Company admins see only their company's roles (excluding super-admin)
            $roles = Role::companyOnly($company->id)->where('name', '!=', 'super-admin')->pluck('name', 'id')->all();
        } else {
            // Regular users can only edit themselves
            if ($user->id !== $authUser->id) {
                abort(403, 'You can only edit your own profile.');
            }
            $company = $authUser->companies()->first();
            $roles = $company
                ? Role::where('name', 'user')->where('company_id', $company->id)->pluck('name', 'id')->all()
                : [];
        }

        $userRoles = $user->roles->pluck('name', 'id')->all();

        // Get offices from user's company only
        $company = $authUser->companies()->first();
        $offices = $company ? Office::where('company_id', $company->id)->pluck('name', 'id')->all() : [];

        $userOffices = $user->offices->pluck('id')->all();
        $userCompany = CompanyAccount::all();

        return view('users.edit', compact('user', 'roles', 'userRoles', 'offices', 'userOffices', 'userCompany'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prepare validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id},id,deleted_at,NULL",
            'roles' => 'required|array',
            // B-06 FIX: require a minimum length and confirmation when a new password is supplied.
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Conditionally require offices field
        if (!$user->hasRole('company-admin')) {
            $rules['offices'] = 'required|array';
        }

        // Validate the request with the prepared rules
        $request->validate($rules);

        // Update user details
        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Sync roles by ID to ensure company-specific roles are used
        $roleIds = $request->input('roles');
        $roleModels = Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roleModels);

        // Ensure company association is maintained
        $companyId = $request->input('companies');
        if ($companyId) {
            // Check if user already has this company
            $companyExists = CompanyUser::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->exists();

            if (!$companyExists) {
                // Remove old company associations
                CompanyUser::where('user_id', $user->id)->delete();

                // Add new company association
                CompanyUser::create([
                    'user_id' => $user->id,
                    'company_id' => $companyId
                ]);
            }
        }

        // Sync offices if user is not company admin
        if (!$user->hasRole('company-admin') && $request->has('offices')) {
            $user->offices()->sync($request->input('offices'));
        } else if ($user->hasRole('company-admin')) {
            // For company admins, ensure they have at least one office from their company
            $authUser = User::findOrFail(Auth::id());
            $company = $authUser->companies()->first();
            if ($company) {
                $office = Office::where('company_id', $company->id)->first();
                if ($office && $user->offices()->count() == 0) {
                    $user->offices()->sync([$office->id]);
                }
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function getUsersByOffice(Request $request)
    {
        $request->validate(['office_id' => 'required|integer|exists:offices,id']);

        $officeId = $request->query('office_id');

        // Ensure the requested office belongs to the authenticated user's company.
        // This prevents cross-company user enumeration via this AJAX endpoint.
        $userCompany = auth()->user()->companies()->first();

        if (!$userCompany) {
            return response()->json([]);
        }

        $officeExists = \App\Models\Office::where('id', $officeId)
            ->where('company_id', $userCompany->id)
            ->exists();

        if (!$officeExists) {
            return response()->json([], 403);
        }

        // B-07 FIX: select only display-safe columns; never serialise password hash,
        // remember_token, verification_code, etc. to a JSON response.
        $users = User::whereHas('offices', function ($query) use ($officeId) {
            $query->where('offices.id', $officeId);
        })->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json($users);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $authUser = User::findOrFail(Auth::id());

        // Prevent self-deletion
        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Super admin specific checks
        if ($authUser->hasRole('super-admin')) {
            if ($user->hasRole('super-admin')) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete another super admin account.');
            }
        } elseif ($authUser->isCompanyAdmin()) {
            // Company admin checks
            $company = $authUser->companies()->first();

            if (!$company) {
                return redirect()->route('users.index')
                    ->with('error', 'You must be associated with a company to delete users.');
            }

            // Check if target user belongs to admin's company
            if (!$user->companies->contains($company->id)) {
                return redirect()->route('users.index')
                    ->with('error', 'You can only delete users from your company.');
            }

            // Prevent company admins from deleting other company admins
            if ($user->hasRole('company-admin')) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete another company admin account.');
            }
        } else {
            // Regular users cannot delete anyone
            return redirect()->route('users.index')
                ->with('error', 'Access Denied: You are not authorized to delete user accounts. This action requires administrative privileges.');
        }

        // Proceed with deletion
        CompanyUser::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
