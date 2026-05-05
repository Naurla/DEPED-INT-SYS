<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - DepEd Zamboanga City</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 antialiased min-h-screen flex flex-col justify-center items-center relative selection:bg-[#a52a2a] selection:text-white">

    {{-- Back to Main Page Button --}}
    <a href="/" class="absolute top-6 left-6 md:top-8 md:left-8 flex items-center gap-2 text-gray-500 hover:text-[#a52a2a] font-bold text-xs uppercase tracking-widest transition-colors group">
        <div class="bg-white p-2 rounded-full shadow-sm border border-gray-200 group-hover:border-[#a52a2a] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </div>
        <span class="mt-0.5">Back to Main Page</span>
    </a>

    <div class="w-full max-w-md px-4 mt-12 md:mt-0">
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 overflow-hidden" 
             x-data="{ 
                view: '{{ session('status') ? 'verify' : (old('code') ? 'new_password' : (session('reset_success') ? 'login' : (old('email') && !$errors->has('password') ? 'forgot' : 'login'))) }}',
                email: '{{ old('email') }}',
                resetCode: '{{ old('code') }}'
             }">
             
            {{-- Branding Header --}}
            <div class="pt-10 pb-6 px-8 text-center border-b border-gray-50 bg-gray-50/30">
                <img src="{{ asset('images/r9.png') }}" alt="DepEd Logo" class="h-20 w-auto mx-auto mb-5 drop-shadow-sm">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase" 
                    x-text="view === 'login' ? 'Admin Portal' : (view === 'forgot' ? 'Reset Password' : (view === 'verify' ? 'Verify Code' : 'New Password'))">
                </h2>
                <p class="text-sm text-gray-500 mt-2 font-medium" 
                   x-text="view === 'login' ? 'Sign in to manage division records' : 'Follow the instructions below to regain access'">
                </p>
            </div>
            
            <div class="p-8 md:p-10">
                {{-- Success Messages --}}
                @if (session('status') || session('reset_success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('status') ?? session('reset_success') }}</span>
                    </div>
                @endif
                
                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- 1. LOGIN FORM --}}
                <form action="{{ route('admin.login') }}" method="POST" x-show="view === 'login'" x-transition>
                    @csrf
                    <div class="mb-5">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" name="email" x-model="email" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:bg-white outline-none transition-colors text-sm">
                    </div>
                    <div class="mb-2">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:bg-white outline-none transition-colors text-sm">
                    </div>
                    <div class="flex justify-end mb-8 mt-3">
                        <button type="button" @click="view = 'forgot'" class="text-sm font-semibold text-blue-600 hover:text-blue-800 focus:outline-none">Forgot Password?</button>
                    </div>
                    <button type="submit" class="w-full bg-[#a52a2a] text-white font-bold py-3.5 rounded-lg hover:bg-red-800 transition-all shadow-md hover:shadow-lg uppercase tracking-widest text-sm">
                        Sign In
                    </button>
                </form>

                {{-- 2. FORGOT PASSWORD (EMAIL SUBMISSION) --}}
                <form action="/admin/password/email" method="POST" x-show="view === 'forgot'" x-cloak x-transition>
                    @csrf
                    <p class="text-sm text-gray-600 mb-6 leading-relaxed">Enter your registered email address to receive a 6-digit verification code.</p>
                    <div class="mb-8">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" name="email" x-model="email" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none text-sm">
                    </div>
                    <button type="submit" class="w-full bg-[#a52a2a] text-white font-bold py-3.5 rounded-lg hover:bg-red-800 transition-all shadow-md uppercase tracking-widest text-sm mb-6">
                        Send Reset Code
                    </button>
                    <div class="text-center border-t border-gray-100 pt-6">
                        <button type="button" @click="view = 'login'" class="text-sm font-semibold text-gray-500 hover:text-gray-900 focus:outline-none">Back to Login</button>
                    </div>
                </form>

                {{-- 3. VERIFY CODE STEP --}}
                <div x-show="view === 'verify'" x-cloak x-transition>
                    <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                        We sent a code to <strong class="text-gray-900" x-text="email"></strong>. Please enter it below to proceed.
                    </p>
                    <div class="mb-8">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">6-Digit Reset Code</label>
                        <input type="text" 
                               x-model="resetCode"
                               x-on:input="if(resetCode.length === 6) view = 'new_password'" 
                               class="w-full bg-gray-50 border border-gray-300 px-4 py-4 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none text-center text-2xl font-bold tracking-[0.5em] uppercase" 
                               placeholder="······" 
                               maxlength="6">
                    </div>
                    <button type="button" 
                            @click="if(resetCode.length === 6) view = 'new_password'" 
                            class="w-full bg-[#a52a2a] text-white font-bold py-3.5 rounded-lg hover:bg-red-800 transition-all shadow-md uppercase tracking-widest text-sm mb-6">
                        Verify Code
                    </button>
                    <div class="text-center border-t border-gray-100 pt-6">
                        <button type="button" @click="view = 'forgot'" class="text-sm font-semibold text-gray-500 hover:text-gray-900 focus:outline-none">Didn't get a code?</button>
                    </div>
                </div>

                {{-- 4. NEW PASSWORD FORM (Code is now hidden) --}}
                <form action="/admin/password/reset" method="POST" x-show="view === 'new_password'" x-cloak x-transition>
                    @csrf
                    {{-- Hidden Fields --}}
                    <input type="hidden" name="email" :value="email">
                    <input type="hidden" name="code" :value="resetCode">
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1">Resetting for:</label>
                        <span class="text-sm font-bold text-gray-800 break-all" x-text="email"></span>
                    </div>

                    <div class="mb-5">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">New Password</label>
                        <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none transition-colors text-sm">
                    </div>
                    <div class="mb-8">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none transition-colors text-sm">
                    </div>
                    
                    <button type="submit" class="w-full bg-[#a52a2a] text-white font-bold py-3.5 rounded-lg hover:bg-red-800 transition-all shadow-md uppercase tracking-widest text-sm mb-6">
                        Update Password
                    </button>
                    
                    <div class="text-center border-t border-gray-100 pt-6">
                        <button type="button" @click="view = 'verify'" class="text-sm font-semibold text-gray-500 hover:text-gray-900 focus:outline-none">Back to verification</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>