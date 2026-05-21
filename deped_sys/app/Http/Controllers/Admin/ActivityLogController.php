<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // STRICT CHECK: Only Super Admin (Role ID 1) can access this
        if (auth()->user()->role_id !== 1) {
            abort(403, 'STRICTLY PROHIBITED: Only the Super Admin can view Activity Logs.');
        }

        // Map the friendly URL names to your actual Model paths
        $filterableModules = [
            'users'       => 'App\Models\User',
            'roles'       => 'App\Models\Role',
            'settings'    => 'App\Models\SiteSetting',
            'issuances'   => 'App\Models\Issuance',
            'procurement' => 'App\Models\BidOpportunity',
        ];

        // Start building the query
        $query = Activity::with('causer')->latest();

        // Apply the filter if one is selected and it exists in our map
        if ($request->filled('module') && array_key_exists($request->module, $filterableModules)) {
            $query->where('subject_type', $filterableModules[$request->module]);
        }

        // Fetch the logs and append the current query string (so pagination remembers the filter)
        $logs = $query->paginate(20)->appends($request->query());

        // Get the current active module to highlight the correct button in the view
        $activeModule = $request->query('module', 'all');

        return view('admin.activity_logs.index', compact('logs', 'activeModule'));
    }
}