<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CompanyAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class CompanySelectionController extends Controller
{
    /**
     * Display the company selection view.
     */
    public function show(Request $request): View|RedirectResponse
    {
        // Check if email and password are in session
        if (!$request->session()->has('login_email') || !$request->session()->has('login_password_hash')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $email = $request->session()->get('login_email');

        // Get all companies associated with this email
        $user = User::where('email', $email)->whereNull('deleted_at')->first();
        
        if (!$user) {
            $request->session()->forget(['login_email', 'login_password_hash']);
            return redirect()->route('login')->with('error', 'User not found.');
        }

        $companies = $user->companies()->get();

        if ($companies->isEmpty()) {
            $request->session()->forget(['login_email', 'login_password_hash']);
            return redirect()->route('login')->with('error', 'No companies found for this account.');
        }

        // If only one company, skip selection and authenticate directly
        if ($companies->count() === 1) {
            return $this->authenticateWithCompany($request, $companies->first()->id);
        }

        return view('auth.select-company', [
            'email' => $email,
            'companies' => $companies,
        ]);
    }

    /**
     * Authenticate user with selected company.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'company_id' => 'required|exists:company_accounts,id',
        ]);

        return $this->authenticateWithCompany($request, $request->company_id);
    }

    /**
     * Complete authentication with company context.
     */
    protected function authenticateWithCompany(Request $request, int $companyId): RedirectResponse
    {
        $email = $request->session()->get('login_email');
        $passwordHash = $request->session()->get('login_password_hash');

        if (!$email || !$passwordHash) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        // Find user
        $user = User::where('email', $email)->whereNull('deleted_at')->first();

        if (!$user) {
            $request->session()->forget(['login_email', 'login_password_hash']);
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Verify password
        if ($user->password !== $passwordHash) {
            $request->session()->forget(['login_email', 'login_password_hash']);
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Verify user belongs to selected company
        if (!$user->companies->contains($companyId)) {
            return redirect()->route('login.select-company')
                ->with('error', 'You do not have access to this company.');
        }

        // Log the user in
        Auth::login($user, $request->session()->get('login_remember', false));
        
        // Store company context in session
        $request->session()->put('current_company_id', $companyId);
        
        // Clear login session data
        $request->session()->forget(['login_email', 'login_password_hash', 'login_remember']);
        
        // Regenerate session
        $request->session()->regenerate();

        \Log::info('User logged in with company context', [
            'user_id' => $user->id,
            'company_id' => $companyId,
            'email' => $user->email,
        ]);

        // If user hasn't verified email, redirect to verification page
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Check if the user's password_set column is false
        if ($user->password_set == 0) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard')->with('status', 201);
            } elseif ($user->isCompanyAdmin()) {
                // Check if user has an active subscription or trial
                $hasActiveSubscription = false;
                
                // Check for trial period
                $trialEndDate = \DB::table('company_users')
                    ->where('user_id', $user->id)
                    ->where('company_id', $companyId)
                    ->value('trial_ends_at');
                    
                // Check for active subscription or trial
                if (($trialEndDate && now()->lessThan($trialEndDate)) || 
                    \App\Models\CompanySubscription::active()->where('company_id', $companyId)->exists()) {
                    $hasActiveSubscription = true;
                }
                
                // Direct to appropriate dashboard based on subscription status
                if ($hasActiveSubscription) {
                    return redirect()->route('reports.company-dashboard')->with('status', 201);
                } else {
                    return redirect()->route('dashboard')->with('status', 201);
                }
            }
            return redirect()->route('profile.edit')->with('message', 'Please change your password before proceeding.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
