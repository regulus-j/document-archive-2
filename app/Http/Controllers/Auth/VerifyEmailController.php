<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * 
     * Handles both authenticated and unauthenticated users:
     * - Authenticated: Marks their email as verified
     * - Unauthenticated: Logs them in after verification
     * 
     * Important: This route is NOT protected by 'auth' middleware because users
     * typically click the verification link from their email while not logged in.
     * The 'signed' middleware validates the URL signature instead.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $userId = $request->route('id');
        $isAuthenticated = auth()->check();
        $authenticatedId = $isAuthenticated ? auth()->id() : null;

        // Log verification attempt for debugging
        Log::info('Email verification attempt', [
            'user_id' => $userId,
            'authenticated' => $isAuthenticated,
            'authenticated_user_id' => $authenticatedId,
            'app_url' => config('app.url'),
        ]);

        $user = \App\Models\User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified', ['user_id' => $user->id, 'email' => $user->email]);
            return redirect()->intended(route('dashboard', [], false).'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('User email verified successfully', ['user_id' => $user->id, 'email' => $user->email]);
        }

        // If user was not authenticated before, log them in
        if (!auth()->check()) {
            auth()->login($user, remember: true);
            Log::info('User auto-logged in after email verification', ['user_id' => $user->id]);
        }

        return redirect()->intended(route('dashboard', [], false).'?verified=1');
    }
}
