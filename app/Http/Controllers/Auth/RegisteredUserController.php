<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Models\CompanyAddress;
use App\Models\CompanyUser;
use App\Models\Office;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
                
                if (!$response->json('success')) {
                    $fail('The reCAPTCHA verification failed. Please try again.');
                }
            }],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assigned role as company admin to newly registered company
        $user->assignRole('company-admin');

        event(new Registered($user));

        Auth::login($user);

        // Generate verification code before redirecting to verification notice
        $user->generateVerificationCode();

        // Get registered_name or default to company_name if not provided
        $registeredName = $request->registered_name ?: $request->company_name;
        
        // Get company_email or default to user's email if not provided
        $companyEmail = $request->company_email ?: $request->email;
        
        // Company registration with required fields
        $company = CompanyAccount::create([
            'user_id' => auth()->id(),
            'company_name' => $request->company_name,
            'registered_name' => $registeredName,
            'company_email' => $companyEmail,
            'company_phone' => $request->company_phone ?: '00000000000',
        ]);

        // $companyAddress = CompanyAddress::create([
        //     'company_id' => $company->id,
        //     'address' => $request->address ?: 'Default Address',
        //     'city' => $request->city ?: 'Default City',
        //     'state' => $request->state ?: 'Default State',
        //     'zip_code' => $request->zip_code ?? '00000',
        //     'country' => $request->country ?: 'Default Country',
        // ]);

        CompanyUser::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        // Create default "Main" office for the company
        $mainOffice = \App\Models\Office::create([
            'company_id' => $company->id,
            'name' => 'Main',
            'office_lead' => $user->id,
        ]);

        // Assign user to the Main office
        $mainOffice->users()->attach($user->id);

        return redirect()->intended(route('verification.notice'))
            ->with('status', 'verification-link-sent');

        // return redirect(route('dashboard', absolute: false));
    }
}

