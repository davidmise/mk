<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // Super Admin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required roles
        $userRole = $user->getPrimaryRole()?->slug;

        if ($userRole && in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'You do not have the required role to access this area.');
    }
}
