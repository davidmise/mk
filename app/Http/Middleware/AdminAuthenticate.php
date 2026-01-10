<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Please sign in to continue.');
        }

        $user = auth()->user();

        // Check if user has admin access (has at least one role)
        if (!$user->roles()->exists()) {
            auth()->logout();
            return redirect()->route('admin.login')->with('error', 'You do not have permission to access the admin area.');
        }

        // Check if account is active
        if (!$user->isActive()) {
            $message = match($user->status) {
                'locked' => 'Your account is temporarily locked. Please try again later.',
                'suspended' => 'Your account has been suspended. Please contact the administrator.',
                default => 'Your account is not active.',
            };

            auth()->logout();
            return redirect()->route('admin.login')->with('error', $message);
        }

        return $next($request);
    }
}
