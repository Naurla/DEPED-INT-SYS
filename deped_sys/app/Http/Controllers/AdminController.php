<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // This will look for resources/views/admin/dashboard.blade.php
        return view('admin.dashboard');
    }

    public function login(Request $request)
    {
        // Simple manual check (In production, use Laravel Auth/Breeze)
        if ($request->username === 'admin' && $request->password === 'password123') {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid Credentials');
    }
}