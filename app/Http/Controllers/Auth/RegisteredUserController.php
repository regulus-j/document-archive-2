<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Models\CompanyAddress;
use App\Models\CompanyUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validationRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                // Temporarily disable reCAPTCHA verification in local development
                if (env('APP_ENV') === 'local') {
                    return; // Skip verification in local development
                }

                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

                if (!$response->json('success')) {
                    $fail('The reCAPTCHA verification failed. Please try again.');
                }
            }],
        ];

        // Only require company contact and address fields when address details are included
        if ($request->include_address === '1') {
            $validationRules['company_email'] = ['required', 'string', 'email', 'max:255'];
            $validationRules['company_phone'] = ['required', 'string', 'max:255'];
            $validationRules['address'] = ['required', 'string', 'max:255'];
            $validationRules['city'] = ['nullable', 'string', 'max:255'];
            $validationRules['state'] = ['nullable', 'string', 'max:255'];
            $validationRules['zip_code'] = ['nullable', 'string', 'max:20'];
            $validationRules['country'] = ['nullable', 'string', 'max:255'];
        }

        $request->validate($validationRules);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Generate verification code before redirecting to verification notice
        $user->generateVerificationCode();

        // Get registered_name or default to company_name if not provided
        $registeredName = $request->registered_name ?: $request->company_name;

        // Get company_email or default to user's email if not provided
        $companyEmail = $request->include_address === '1' && $request->company_email
            ? $request->company_email
            : $request->email;

        // Get company_phone or default to null if not provided
        $companyPhone = $request->include_address === '1' && $request->company_phone
            ? $request->company_phone
            : null;

        // Company registration with required fields
        // This triggers auto-creation of default roles (company-admin, user) for the company
        $company = CompanyAccount::create([
            'user_id' => auth()->id(),
            'company_name' => $request->company_name,
            'registered_name' => $registeredName,
            'company_email' => $companyEmail,
            'company_phone' => $companyPhone,
        ]);

        // Assign the company-specific company-admin role
        $companyAdminRole = \App\Models\Role::where('name', 'company-admin')
            ->where('company_id', $company->id)
            ->first();
        if ($companyAdminRole) {
            $user->assignRole($companyAdminRole);
        }

        // Only create company address if the toggle is enabled and address fields are provided
        if ($request->include_address === '1' && $request->filled('address')) {
            CompanyAddress::create([
                'company_id' => $company->id,
                'address' => $request->address,
                'city' => $request->city ?: '',
                'state' => $request->state ?: '',
                'zip_code' => $request->zip_code ?: '',
                'country' => $request->country ?: '',
            ]);
        }

        CompanyUser::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        return redirect()->intended(route('verification.notice'))
            ->with('status', 'verification-link-sent')
            ->with('success', 'Account created successfully! Please verify your email.');

        // return redirect(route('dashboard', absolute: false));
    }
}

