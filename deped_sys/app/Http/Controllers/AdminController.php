<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Advisory;
use App\Models\Issuance; // Added
use App\Models\User;     // Added
use App\Models\Page;     // Added
use App\Models\LearningMaterial; // Added
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // Calculate counts for the dashboard summary
        $counts = [
            'banners'    => Banner::count(),
            'advisories' => Advisory::count(),
            'memos'      => Issuance::where('type', 'memorandum')->count(),
            'users'      => User::count(),
            'pages'      => Page::count(),
            'materials'  => LearningMaterial::count(),
        ];

        // Fetch recent data for quick overview tables
        $recentAdvisories = Advisory::latest()->take(5)->get();
        $recentIssuances = Issuance::latest()->take(5)->get();

        return view('admin.dashboard.index', compact('counts', 'recentAdvisories', 'recentIssuances'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'], 
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid Credentials');
    }

    public function logout(Request $request) 
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}