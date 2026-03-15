<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * Middleware to properly handle and log signed URL validation errors.
 * 
 * This middleware catches InvalidSignatureException from the 'signed' middleware
 * and provides detailed logging to help debug APP_URL mismatches or other issues.
 * 
 * Common causes of signature validation failures:
 * - APP_URL in .env doesn't match the actual request domain
 * - HTTPS vs HTTP mismatch
 * - Using different APP_KEY between deployments
 * - Proxy/load balancer not properly forwarding headers
 */
class ValidateSignedUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (InvalidSignatureException $e) {
            // Log detailed information about the signature validation failure
            Log::warning('Signed URL validation failed', [
                'path' => $request->path(),
                'url' => $request->url(),
                'app_url' => config('app.url'),
                'app_key_length' => strlen(config('app.key')),
                'secure' => $request->secure(),
                'scheme' => $request->getScheme(),
                'host' => $request->getHost(),
                'error_message' => $e->getMessage(),
            ]);

            // Re-throw to allow default error handling
            throw $e;
        }
    }
}
