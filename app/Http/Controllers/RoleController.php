<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class RoleController extends Controller
{
    function __construct()
    {
        // Check if user has any of these permissions
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

        public function index(Request $request): View
    {
        $user = Auth::user();

        // Super-admin sees all roles; others see only their own company's roles.
        if ($user->hasRole('super-admin')) {
            $query = Role::withCount('permissions')->orderBy('id', 'DESC');
        } else {
            $company = $user->companies()->first();
            $companyId = $company ? $company->id : null;

            if ($companyId) {
                $query = Role::withCount('permissions')
                    ->companyOnly($companyId)
                    ->where('name', '!=', 'super-admin')
                    ->orderBy('id', 'DESC');
            } else {
                $query = Role::withCount('permissions')
                    ->whereRaw('1 = 0');
            }
        }

        if ($request->filled('role_search')) {
            $query->where('name', 'like', '%' . $request->input('role_search') . '%');
        }

        if ($request->filled('permission_filter')) {
            $filter = $request->input('permission_filter');
            switch ($filter) {
                case '0-5':
                    $query->having('permissions_count', '>=', 0)
                          ->having('permissions_count', '<=', 5);
                    break;
                case '6-10':
                    $query->having('permissions_count', '>=', 6)
                          ->having('permissions_count', '<=', 10);
                    break;
                case '10+':
                    $query->having('permissions_count', '>', 10);
                    break;
            }
        }

        // Get total roles count (company-scoped) before pagination
        $totalRolesQuery = clone $query;
        $totalRoles = $totalRolesQuery->count();

        $roles = $query->paginate(5)->withQueryString();

        // For "found" count, use total() from paginator (total matching records across all pages)
        $foundRoles = $roles->total();

        return view('roles.index', compact('roles', 'totalRoles', 'foundRoles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    public function create(): View
    {
        $permission = Permission::get();
        return view('roles.create', compact('permission'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->companies()->first();
        $companyId = $company ? $company->id : null;

        // Validate name is unique within the company scope
        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        // Check uniqueness within the company
        $exists = Role::where('name', $request->input('name'))
            ->where('company_id', $companyId)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A role with this name already exists in your company.'])->withInput();
        }

        $permissionsID = array_map(
            function($value) { return (int)$value; },
            $request->input('permission')
        );

        $role = Role::create([
            'name' => $request->input('name'),
            'company_id' => $companyId,
        ]);
        $role->syncPermissions($permissionsID);

        return redirect()->route('roles.index')
                        ->with('success', 'Role created successfully');
    }

    public function show($id): View
    {
        $role = Role::findOrFail($id);

        // Ensure user can only view roles from their company (or global)
        $user = Auth::user();
        if (!$user->hasRole('super-admin')) {
            $company = $user->companies()->first();
            if (!$company || $role->company_id !== $company->id) {
                abort(403, 'You cannot view roles from another company.');
            }
        }

        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    public function edit($id): View
    {
        $role = Role::findOrFail($id);

        // Ensure user can only edit roles from their company
        $user = Auth::user();
        if (!$user->hasRole('super-admin')) {
            $company = $user->companies()->first();
            if (!$company || $role->company_id !== $company->id) {
                abort(403, 'You cannot edit roles from another company.');
            }
            // Cannot edit global roles unless super-admin
            if ($role->isGlobal()) {
                abort(403, 'You cannot edit global roles.');
            }
        }

        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::findOrFail($id);

        // Ensure user can only update roles from their company
        $user = Auth::user();
        if (!$user->hasRole('super-admin')) {
            $company = $user->companies()->first();
            if (!$company || $role->company_id !== $company->id) {
                abort(403, 'You cannot update roles from another company.');
            }
            if ($role->isGlobal()) {
                abort(403, 'You cannot update global roles.');
            }
        }

        // Check uniqueness within the company if name changed
        if ($role->name !== $request->input('name')) {
            $exists = Role::where('name', $request->input('name'))
                ->where('company_id', $role->company_id)
                ->where('id', '!=', $role->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['name' => 'A role with this name already exists in your company.'])->withInput();
            }
        }

        $role->name = $request->input('name');
        $role->save();

        $permissionsID = array_map(
            function($value) { return (int)$value; },
            $request->input('permission')
        );

        $role->syncPermissions($permissionsID);

        return redirect()->route('roles.index')
                        ->with('success', 'Role updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        // Ensure user can only delete roles from their company
        $user = Auth::user();
        if (!$user->hasRole('super-admin')) {
            $company = $user->companies()->first();
            if (!$company || $role->company_id !== $company->id) {
                abort(403, 'You cannot delete roles from another company.');
            }
            if ($role->isGlobal()) {
                abort(403, 'You cannot delete global roles.');
            }
        }

        // Prevent deleting default company roles
        if (in_array($role->name, ['company-admin', 'user']) && $role->company_id) {
            return redirect()->route('roles.index')
                ->with('error', 'Default company roles (company-admin, user) cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('roles.index')
                        ->with('success', 'Role deleted successfully');
    }
}
