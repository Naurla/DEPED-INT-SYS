<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner; // Use only Banner
use App\Models\Advisory;

class AdminController extends Controller
{
    public function index()
    {
        // Fetch data for the dashboard view
        $banners = Banner::all(); 
        $advisories = Advisory::latest()->get();

        // Calculate counts for the dashboard summary
        $counts = [
            'banners'    => Banner::count(),
            'advisories' => Advisory::count(),
            'memos'      => 0, // Replace with Memo::count() when the model exists
        ];

        return view('admin.dashboard', compact('banners', 'advisories', 'counts'));
    }

    public function login(Request $request)
    {
        if ($request->username === 'admin' && $request->password === 'password123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid Credentials');
    }
}