<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Advisory;
use Illuminate\Support\Facades\Auth; // Make sure to add this line

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

        return view('admin.dashboard.index', compact('banners', 'advisories', 'counts'));
    }

    public function login(Request $request)
    {
        // Validate the incoming request
        $credentials = $request->validate([
            'email' => ['required', 'email'], // Assuming your login form uses an email field
            'password' => ['required'],
        ]);

        // Attempt to log the user in using Laravel's Auth system
        if (Auth::attempt($credentials)) {
            // Regenerate the session to prevent fixation attacks
            $request->session()->regenerate();
            
            return redirect()->route('admin.dashboard');
        }

        // If authentication fails, redirect back with an error
        return back()->with('error', 'Invalid Credentials');
    }
    public function logout(Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
    }
}