<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Never block admin backend routes or logout action
        if ($request->is('admin*') || $request->is('logout')) {
            return $next($request);
        }

        try {
            $settings = SiteSetting::first();
            $isMaintenance = $settings ? $settings->is_maintenance_mode : false;
            $disabledPages = $settings ? ($settings->disabled_pages ?? []) : [];

            // If the user is NOT an admin
            if (!Auth::check()) {
                
                // 1. Is the ENTIRE site disabled?
                if ($isMaintenance) {
                    return response()->view('maintenance');
                }

                // 2. Is this SPECIFIC route disabled?
                $currentRouteName = $request->route() ? $request->route()->getName() : null;
                
                if ($currentRouteName && in_array($currentRouteName, $disabledPages)) {
                    return response()->view('maintenance');
                }

                // 3. SPECIAL CHECK: Is this a specific Procurement Category?
                if ($currentRouteName === 'procurement.index' || $currentRouteName === 'procurement.show') {
                    $category = $request->route('category'); // gets 'apcpi', 'pmr', etc.
                    
                    // Look for a custom string like 'procurement:apcpi' in our disabled list
                    if ($category && in_array('procurement:' . $category, $disabledPages)) {
                        return response()->view('maintenance');
                    }
                }
            }
        } catch (\Exception $e) {
            // Failsafe
        }

        return $next($request);
    }
}