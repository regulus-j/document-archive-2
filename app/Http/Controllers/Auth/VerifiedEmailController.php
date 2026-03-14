<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\verificationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerifiedEmailController extends Controller
{
    public $user;
    public $verification_code;
    public $verification_code_expires_at;

    public function __construct()
    {
        // We'll set the user in each method when needed
    }

    /*
    // Create verification code
    */
    public function create()
    {
        // Logic to create a verification code
        // This could involve generating a random code and saving it to the database
    }

    public function userVerifiesMail()
    {
        $this->user = auth()->user();
        
        if(!$this->user) {
            return redirect()->route('login');
        }
        
        // Logic to check if the user has verified their email
        if($this->user->hasVerifiedEmail()) {
            // If verified, redirect to the intended route
            return redirect()->intended(route('dashboard', [], false).'?verified=1');
        }
    
        // If not verified, show the verification notice view directly instead of redirecting
        return view('auth.verify-email');  // Adjust to your actual view name
    }

    /*
    // Send verification code to user's email
    */
    public function send(Request $request)
    {
        $this->user = auth()->user();
        
        if(!$this->user) {
            return redirect()->route('login');
        }

        // Generate a verification code
        $code = $this->user->generateVerificationCode();

        try {
            Mail::to($this->user->email)
                ->send(new verificationMail(
                    $this->user->first_name,
                    $this->user->last_name,
                    $code,
                    route('login')
                ));
            
            // Return a redirect instead of a JSON response
            return redirect()->back()->with('status', 'verification-link-sent');
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            
            // Return a redirect with error
            return redirect()->back()->withErrors([
                'email' => 'Failed to send verification email. Please try again later.'
            ]);
        }
    }

    /*
    // Verify the code entered by the user
    */
    public function verify(Request $request, $id = null)
    {
        // First try to get user by ID if provided
        if ($id) {
            $this->user = User::find($id);
            if (!$this->user) {
                return back()->withErrors([
                    'verification_code' => 'User not found. Please request a new verification code.'
                ]);
            }
            if (auth()->check() && auth()->id() !== $this->user->id) {
                return back()->withErrors([
                    'verification_code' => 'Invalid verification request. Please use your own verification code.'
                ]);
            }
        } else {
            $this->user = auth()->user();
            
            if(!$this->user) {
                return redirect()->route('login');
            }
        }

        // Validate the request
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $code = $request->input('verification_code');
        
        if (!$this->user->verification_code || !$this->user->verification_code_expires_at) {
            return back()->withErrors([
                'verification_code' => 'No active verification code found. Please request a new code.'
            ]);
        }

        // Check if the code is valid
        if ($this->user->isValidVerificationCode($code)) {
            // Mark email as verified
            $this->user->email_verified_at = now();
            $this->user->clearVerificationCode();
            $this->user->save();
            
            return redirect()->route('dashboard')->with('status', 'Your email has been verified successfully!');
        }
        
        // If code is invalid or expired
        return back()->withErrors([
            'verification_code' => 'The verification code is invalid or has expired.',
        ]);
    }

    /*
    // Resend verification code
    */
    public function resend(Request $request)
    {
        $this->user = auth()->user();
        
        if(!$this->user) {
            return redirect()->route('login');
        }
        
        // Generate and send a new verification code
        $code = $this->user->generateVerificationCode();
        
        try {
            Mail::to($this->user->email)
                ->send(new verificationMail(
                    $this->user->first_name,
                    $this->user->last_name,
                    $code,
                    route('login')
                ));
            
            return back()->with('status', 'verification-link-sent');
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Failed to send verification email. Please try again later.'
            ]);
        }
    }

    /*
    // Handle the case when the user clicks the verification link in the email
    */
    public function handleVerificationLink(Request $request)
    {
        // Logic to handle the verification link
        // This could involve checking the token in the URL and marking the email as verified
    }
}
