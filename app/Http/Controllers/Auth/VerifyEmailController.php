<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

class VerifyEmailController extends Controller
{
    /**
     * Verify user email via signed URL link from email.
     * 
     * This handler does NOT use EmailVerificationRequest because that FormRequest
     * requires authentication, which defeats the purpose of email verification links.
     * Instead, we:
     * 1. Manually validate the URL signature (via 'signed' middleware - handles HTTPS/domain issues)
     * 2. Validate the hash matches the user's email SHA1
     * 3. Support both authenticated and unauthenticated users
     * 
     * Important: The 'signed' middleware in routes/auth.php validates the URL signature.
     * If signature validation fails there, it will be caught by the exception handler
     * and return a 419 error instead of 500.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $userId = $request->route('id');
        $hash = $request->route('hash');
        $isAuthenticated = auth()->check();

        // Log verification attempt for debugging
        Log::info('Email verification link clicked', [
            'user_id' => $userId,
            'authenticated' => $isAuthenticated,
            'app_url' => config('app.url'),
            'request_url' => $request->url(),
            'request_scheme' => $request->getScheme(),
            'request_host' => $request->getHost(),
        ]);

        try {
            $user = User::findOrFail($userId);
        } catch (\Exception $e) {
            Log::warning('User not found during email verification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        // Validate the hash matches the user's email (SHA1)
        if (!hash_equals(sha1($user->email), $hash)) {
            Log::warning('Email verification hash mismatch', [
                'user_id' => $user->id,
                'expected_hash' => sha1($user->email),
                'provided_hash' => $hash,
            ]);
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        // If already verified, just redirect
        if ($user->hasVerifiedEmail()) {
            Log::info('User email already verified', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return redirect()->intended(route('dashboard', [], false).'?verified=1');
        }

        // Mark email as verified and fire event
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('User email verified successfully via link', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        // If user was not authenticated before, log them in
        if (!auth()->check()) {
            auth()->login($user, remember: true);
            Log::info('User auto-logged in after email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return redirect()->intended(route('dashboard', [], false).'?verified=1');
    }
}
