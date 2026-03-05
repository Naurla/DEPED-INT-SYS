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

        return view('admin.dashboard', compact('banners', 'advisories'));
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