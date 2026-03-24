<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\CompanySubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
    
        $request->session()->regenerate();

        // If user hasn't verified email, redirect to verification page
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
    
        // Check if the user's password_set column is false
        if (auth()->user()->password_set == 0) {
            if(auth()->user()->isSuperAdmin()){
                return redirect()->route('admin.dashboard')->with('status', 201);
            }elseif(auth()->user()->isCompanyAdmin()){
                // Check if user has an active subscription or trial
                $userCompany = auth()->user()->companies()->first();
                $hasActiveSubscription = false;
                
                // Check for trial period
                $trialEndDate = DB::table('company_users')
                    ->where('user_id', auth()->id())
                    ->value('trial_ends_at');
                    
                // Check for active subscription or trial
                if (($trialEndDate && now()->lessThan($trialEndDate)) || 
                    ($userCompany && CompanySubscription::active()->where('company_id', $userCompany->id)->exists())) {
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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
