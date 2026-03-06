<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check() || !Auth::user()->role) {
            abort(403, 'Unauthorized Access.');
        }

        // Check if the user's role slug matches any of the allowed roles
        if (!in_array(Auth::user()->role->slug, $roles) && Auth::user()->role->slug !== 'super-admin') {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
