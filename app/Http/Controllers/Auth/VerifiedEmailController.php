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
        try {
            $this->user = auth()->user();
            
            if(!$this->user) {
                return redirect()->route('login');
            }

            // Check if verification_code column exists in database
            $hasVerificationCodeColumn = \Schema::hasColumn('users', 'verification_code');
            $hasVerificationExpiresColumn = \Schema::hasColumn('users', 'verification_code_expires_at');
            
            if (!$hasVerificationCodeColumn || !$hasVerificationExpiresColumn) {
                \Log::error('Verification code columns missing from users table', [
                    'has_verification_code' => $hasVerificationCodeColumn,
                    'has_verification_expires_at' => $hasVerificationExpiresColumn,
                ]);
                return redirect()->back()->withErrors([
                    'email' => 'System error: Database schema incomplete. Please contact support. (ERR: DB_SCHEMA)'
                ]);
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
                
                \Log::info('Verification code sent successfully', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                ]);
                
                // Return a redirect instead of a JSON response
                return redirect()->back()->with('status', 'verification-link-sent');
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                    'error' => $e->getMessage(),
                ]);
                
                // Return a redirect with error
                return redirect()->back()->withErrors([
                    'email' => 'Failed to send verification email. Please try again later.'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in send verification method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->withErrors([
                'email' => 'An unexpected error occurred. Please try again later.'
            ]);
        }
    }

    /*
    // Verify the code entered by the user
    */
    public function verify(Request $request, $id = null)
    {
        try {
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
            
            // Check if verification_code column exists in database
            $hasVerificationCodeColumn = \Schema::hasColumn('users', 'verification_code');
            $hasVerificationExpiresColumn = \Schema::hasColumn('users', 'verification_code_expires_at');
            
            if (!$hasVerificationCodeColumn || !$hasVerificationExpiresColumn) {
                \Log::error('Verification code columns missing from users table', [
                    'has_verification_code' => $hasVerificationCodeColumn,
                    'has_verification_expires_at' => $hasVerificationExpiresColumn,
                ]);
                return back()->withErrors([
                    'verification_code' => 'System error: Database schema incomplete. Please contact support.'
                ]);
            }
            
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
                
                \Log::info('User email verified via code', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                ]);
                
                return redirect()->route('dashboard')->with('status', 'Your email has been verified successfully!');
            }
            
            // If code is invalid or expired
            return back()->withErrors([
                'verification_code' => 'The verification code is invalid or has expired.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error during email verification code verification', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors([
                'verification_code' => 'An error occurred while verifying your code. Please try again later.'
            ]);
        }
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
