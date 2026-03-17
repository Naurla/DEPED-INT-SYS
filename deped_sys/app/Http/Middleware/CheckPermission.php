<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $user = auth()->user();

        // Super admins automatically bypass all restrictions
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Check if the user's checklist includes ANY of the required route permissions
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        // If they don't have access, block them and throw a 403 Error
        abort(403, 'Unauthorized Access. You do not have the required permissions to view this module.');
    }
}