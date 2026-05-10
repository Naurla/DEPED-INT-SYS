<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Advisory;
use App\Models\Issuance; 
use App\Models\User;     
use App\Models\Page;     
use App\Models\LearningMaterial; 
use App\Models\BidOpportunity;
use App\Models\Modules; // <-- Added Modules Model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Get accurate records
        $counts = [
            'users'       => \App\Models\User::count(),
            'issuances'   => \App\Models\Issuance::count(),
            'pages'       => \App\Models\Page::count(),
            'materials'   => \App\Models\LearningMaterial::count(),
            'procurement' => \App\Models\BidOpportunity::count(),
            'enrollment'  => \App\Models\EnrollmentStatistic::count(),
            'banners'     => \App\Models\Banner::count(),
            'modules'     => \App\Models\Modules::count(), // <-- ADDED THIS SO THE DASHBOARD CAN SEE IT
        ];

        // 2. Fetch Recent Activity 
        $recentProcurements = \App\Models\BidOpportunity::latest()->take(5)->get();
        $recentIssuances = \App\Models\Issuance::latest()->take(5)->get();

        // 3. Prepare Chart Data (DYNAMIC REAL DATA FOR THE LAST 6 MONTHS)
        $months = [];
        $procurementData = [];
        $issuancesData = [];

        // Loop backwards from 5 months ago to the current month
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M'); // e.g., 'Oct', 'Nov', 'Dec'

            // Count real Procurement records for that specific month & year
            $procurementData[] = \App\Models\BidOpportunity::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            // Count real Issuance records for that specific month & year
            $issuancesData[] = \App\Models\Issuance::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Pack the real data into the array
        $chartData = [
            'months' => $months,
            'procurement' => $procurementData,
            'issuances' => $issuancesData,
        ];

        return view('admin.dashboard.index', compact(
            'counts', 
            'recentProcurements', 
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

        $code = Str::upper(Str::random(6));
        
        $user = User::where('email', $request->email)->first();
        $user->remember_token = $code; 
        $user->updated_at = now(); 
        $user->save();

        Mail::send('emails.password_reset', ['code' => $code], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Admin Password Reset Code');
        });

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

        if (!$user->remember_token || $user->remember_token !== $request->code) {
            return back()->withErrors(['code' => 'The reset code is invalid.'])->withInput();
        }

        if ($user->updated_at->addMinutes(10)->isPast()) {
            $user->remember_token = null; 
            $user->save();
            return back()->withErrors(['code' => 'The reset code has expired. Please request a new one.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->remember_token = null; 
        $user->save();

       return redirect('/')->with('reset_success', 'Your password has been successfully reset. You can now log in.');
    }
}