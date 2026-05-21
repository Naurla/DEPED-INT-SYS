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
use App\Models\Modules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $counts = [
            'users'       => \App\Models\User::count(),
            'issuances'   => \App\Models\Issuance::count(),
            'pages'       => \App\Models\Page::count(),
            'materials'   => \App\Models\LearningMaterial::count(),
            'procurement' => \App\Models\BidOpportunity::count(),
            'enrollment'  => \App\Models\EnrollmentStatistic::count(),
            'banners'     => \App\Models\Banner::count(),
            'modules'     => \App\Models\Modules::count(), 
        ];

        $recentProcurements = \App\Models\BidOpportunity::latest()->take(5)->get();
        $recentIssuances = \App\Models\Issuance::latest()->take(5)->get();

        $months = [];
        $procurementData = [];
        $issuancesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M'); 

            $procurementData[] = \App\Models\BidOpportunity::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $issuancesData[] = \App\Models\Issuance::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

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
        $request->validate([
            'email' => ['required', 'email'], 
            'password' => ['required'],
        ]);

        // FIX: Clean the inputs to remove invisible trailing spaces 
        // and force email to lowercase for reliable matching.
        $email = strtolower(trim($request->email));
        $password = trim($request->password);

        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

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

    public function sendResetCode(Request $request)
    {
        // Also clean the email here before validating
        $request->merge(['email' => strtolower(trim($request->email))]);

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
        $request->merge(['email' => strtolower(trim($request->email))]);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->remember_token || $user->remember_token !== trim($request->code)) {
            return back()->withErrors(['code' => 'The reset code is invalid.'])->withInput();
        }

        if ($user->updated_at->addMinutes(10)->isPast()) {
            $user->remember_token = null; 
            $user->save();
            return back()->withErrors(['code' => 'The reset code has expired. Please request a new one.'])->withInput();
        }

        $user->password = Hash::make(trim($request->password));
        $user->remember_token = null; 
        $user->save();

       // FIXED: Redirect directly to the login route. The Blade template 
       // handles the 'reset_success' session variable and triggers the success modal.
       return redirect()->route('login')->with('reset_success', 'Your password has been successfully reset. You can now log in.');
    }
}