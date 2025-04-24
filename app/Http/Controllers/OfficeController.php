<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource with parent office name if exists.
     */
    public function index()
    {
        $company = auth()->user()->companies()->first();

        if (!$company) {
            return redirect()->route('companies.create')
                ->with('error', 'Please create a company first.');
        }

        $offices = Office::where('company_id', $company->id)->get();
        return view('offices.index', compact('offices'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get the current user's company
        $company = auth()->user()->companies()->first();
        
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('error', 'Please create a company first.');
        }
        
        // Only show offices from the user's company
        $offices = Office::where('company_id', $company->id)->pluck('name', 'id');
        
        // Get users from the current company for office lead selection
        $users = $company->employees()->get(['id', 'first_name', 'last_name']);
        
        return view('offices.create', compact('offices', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_office_id' => 'nullable|exists:offices,id',
            'office_lead' => 'nullable|exists:users,id',
        ]);

        // Get the current user's company
        $company = auth()->user()->companies()->first();
        
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('error', 'Please create a company first.');
        }

        $office = Office::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'parent_office_id' => $request->parent_office_id,
            'office_lead' => $request->office_lead,
        ]);

        // If an office lead is selected, ensure they're attached to this office
        if ($request->office_lead) {
            $office->users()->syncWithoutDetaching([$request->office_lead]);
        }

        return redirect()->route('office.index')
            ->with('success', 'Office created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office)
    {
        //
        $office = Office::with('parentOffice')->find($office->id);
        return view('offices.show', compact('office'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Office $office)
    {
        $office = Office::with('parentOffice', 'lead')->find($office->id);
        
        // Get the current user's company
        $company = auth()->user()->companies()->first();
        
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('error', 'Please create a company first.');
        }
        
        // Only show offices from the same company and exclude the current office
        $offices = Office::where('company_id', $company->id)
                         ->where('id', '!=', $office->id)
                         ->get();
                         
        // Get users from the current company for office lead selection
        $users = $company->employees()->get(['id', 'first_name', 'last_name']);

        return view('offices.edit', compact('office', 'offices', 'users'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Office $office)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_office_id' => 'nullable|exists:offices,id',
            'office_lead' => 'nullable|exists:users,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        try {
            $office->update([
                'name' => $request->name,
                'parent_office_id' => $request->parent_office_id,
                'office_lead' => $request->office_lead,
            ]);
            
            // If an office lead is selected, ensure they're attached to this office
            if ($request->office_lead) {
                $office->users()->syncWithoutDetaching([$request->office_lead]);
            }
            
            // Sync the users to the office if users field is present
            if ($request->has('users')) {
                // Make sure office lead is included in the users array
                $officeUsers = $request->users;
                if ($request->office_lead && !in_array($request->office_lead, $officeUsers)) {
                    $officeUsers[] = $request->office_lead;
                }
                $office->users()->sync($officeUsers);
            }

            return redirect()->route('office.index')->with('success', 'Office updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating office: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Office $office)
    {
        try {
            if ($office->childOffices()->count() > 0) {
                return back()->with('error', 'Cannot delete office with child offices.');
            }
            if ($office->users()->count() > 0) {
                return back()->with('error', 'Cannot delete office with associated users.');
            }
            if ($office->sentTransactions()->count() > 0 || $office->receivedTransactions()->count() > 0) {
                return back()->with('error', 'Cannot delete office associated with documents.');
            }

            $office->delete();
            return redirect()->route('offices.index')->with('success', 'Office deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting office: ' . $e->getMessage());
        }
    }

    /**
     * Show form for assigning users to an office
     */
    public function assignUsers(Office $office)
    {
        // Get the current user's company
        $company = auth()->user()->companies()->first();
        
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('error', 'Please create a company first.');
        }
        
        // Get all users from the company who are not already assigned to this office
        $availableUsers = $company->employees()
            ->whereDoesntHave('offices', function($query) use ($office) {
                $query->where('offices.id', $office->id);
            })
            ->get(['id', 'first_name', 'last_name', 'email']);
            
        // Get users already assigned to this office
        $assignedUsers = $office->users()->get(['users.id', 'first_name', 'last_name', 'email']);
        
        return view('offices.assign-users', compact('office', 'availableUsers', 'assignedUsers'));
    }
    
    /**
     * Assign users to an office
     */
    public function updateAssignedUsers(Request $request, Office $office)
    {
        $request->validate([
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ]);
        
        // Get selected users
        $selectedUsers = $request->input('users', []);
        
        // Sync the selected users with the office
        $office->users()->sync($selectedUsers);
        
        return redirect()->route('office.assign.users', $office->id)
            ->with('success', 'Users assigned to office successfully.');
    }

    /**
     * Add a single user to office
     */
    public function addUserToOffice(Request $request, Office $office)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        // Attach the user to the office
        $office->users()->attach($request->user_id);
        
        return redirect()->route('office.assign.users', $office->id)
            ->with('success', 'User added to office successfully.');
    }
    
    /**
     * Remove a user from office
     */
    public function removeUserFromOffice(Request $request, Office $office)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        // Check if user is office lead
        if ($office->office_lead == $request->user_id) {
            return redirect()->route('office.assign.users', $office->id)
                ->with('error', 'Cannot remove the office leader. Please change the office leader first.');
        }
        
        // Detach the user from the office
        $office->users()->detach($request->user_id);
        
        return redirect()->route('office.assign.users', $office->id)
            ->with('success', 'User removed from office successfully.');
    }

}
