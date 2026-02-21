<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * B-02 NOTE: This class is dead scaffolding that was never hooked into the
 * middleware pipeline. Role-based access control is handled by Spatie's
 * RoleMiddleware (aliased as 'role' in bootstrap/app.php).
 *
 * This file is intentionally left as a clear no-op to avoid accidental use.
 * If you ever need a custom role check beyond what Spatie provides, implement
 * it here and register it in bootstrap/app.php.
 *
 * @deprecated  Not used.  Spatie RoleMiddleware covers all current use cases.
 */
class Roles
{
    /**
     * Handle an incoming request.
     *
     * This middleware performs NO checks. Do not wire it into any route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
