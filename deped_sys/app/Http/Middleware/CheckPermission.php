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

        // Check if user has a role assigned
        if (!$user->role) {
            abort(403, 'Unauthorized Access. You do not have a role assigned.');
        }

        // Super admins automatically bypass all restrictions
        if ($user->role->slug === 'super-admin') {
            return $next($request);
        }

        // Fetch the permissions array directly from the User's assigned Role
        $rolePermissions = $user->role->permissions ?? [];

        // Safety fallback: if it's returning as a JSON string instead of an array, decode it
        if (is_string($rolePermissions)) {
            $rolePermissions = json_decode($rolePermissions, true) ?? [];
        }

        // Check if the Role's checklist includes ANY of the required route permissions
        foreach ($permissions as $permission) {
            if (in_array($permission, $rolePermissions)) {
                return $next($request);
            }
        }

        // If they don't have access, block them and throw a 403 Error
        abort(403, 'Unauthorized Access. You do not have the required permissions to view this module.');
    }
}