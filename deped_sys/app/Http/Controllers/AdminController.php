<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Advisory;
use App\Models\Issuance; 
use App\Models\User;     
use App\Models\Page;     
use App\Models\LearningMaterial; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Str;          

class AdminController extends Controller
{
    public function index()
{
    // 1. Get accurate records from all major sidebar categories
    $counts = [
        'users'       => \App\Models\User::count(),
        'advisories'  => \App\Models\Advisory::count(),
        'memos'       => \App\Models\Issuance::where('type', 'memorandum')->count(),
        'issuances'   => \App\Models\Issuance::count(),
        'pages'       => \App\Models\Page::count(),
        'materials'   => \App\Models\LearningMaterial::count(),
        'procurement' => \App\Models\BidOpportunity::count(),
        'enrollment'  => \App\Models\EnrollmentStatistic::count(),
        'banners'     => \App\Models\Banner::count(),
    ];

    // 2. Fetch Recent Activity
    $recentAdvisories = \App\Models\Advisory::latest()->take(5)->get();
    $recentIssuances = \App\Models\Issuance::latest()->take(5)->get();

    // 3. Prepare Chart Data (Example: Last 6 Months Activity)
    // In a real scenario, you can group by created_at. Here we use basic arrays for the UI structure.
    $chartData = [
        'months' => ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        'advisories' => [5, 8, 3, 10, 15, 7], // Replace with real DB queries grouping by month
        'issuances' => [12, 19, 10, 14, 22, 18],
    ];

    return view('admin.dashboard.index', compact(
        'counts', 
        'recentAdvisories', 
        'recentIssuances', 
        'chartData'
    ));
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

        return back()->withErrors(['email' => 'Invalid Credentials'])->withInput();
    }

    public function logout(Request $request) 
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ==========================================
    // PASSWORD RESET LOGIC
    // ==========================================

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'No account found with this email address.'
        ]);

        // Generate a random 6-character alphanumeric code
        $code = Str::upper(Str::random(6));
        
        // Bypass mass assignment protection by saving directly
        // We use the updated_at timestamp to track the 10-minute expiration
        $user = User::where('email', $request->email)->first();
        $user->remember_token = $code; 
        $user->updated_at = now(); 
        $user->save();

        // Send the beautiful HTML email view with the code
        Mail::send('emails.password_reset', ['code' => $code], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Admin Password Reset Code');
        });

        // Return back with a status message to automatically switch the modal to the 'reset' view
        return back()->with('status', 'A reset code has been sent to your email address.')->withInput();
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        // 1. Verify the code matches
        if (!$user->remember_token || $user->remember_token !== $request->code) {
            return back()->withErrors(['code' => 'The reset code is invalid.'])->withInput();
        }

        // 2. Enforce the 10-minute expiration rule
        if ($user->updated_at->addMinutes(10)->isPast()) {
            $user->remember_token = null; // Clear the expired code
            $user->save();
            return back()->withErrors(['code' => 'The reset code has expired. Please request a new one.'])->withInput();
        }

        // Bypass mass assignment protection to reset the password
        $user->password = Hash::make($request->password);
        $user->remember_token = null; // Clear the code after successful reset
        $user->save();

       return redirect('/')->with('reset_success', 'Your password has been successfully reset. You can now log in.');
    }
}