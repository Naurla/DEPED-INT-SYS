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
<body class="bg-gray-50 antialiased min-h-screen flex flex-col justify-center items-center relative selection:bg-[#a52a2a] selection:text-white"
      x-data="{ 
          view: '{{ session('status') ? 'verify' : (session('reset_success') ? 'login' : (old('form_type') === 'reset' ? 'new_password' : (old('form_type') === 'forgot' ? 'forgot' : 'login'))) }}',
          email: '{{ old('email') }}',
          resetCode: '{{ old('code') }}',
          
          /* Modal States */
          showVerifyModal: false,
          verifyStatus: 'loading', /* loading, success, error */
          showSuccessModal: {{ session('reset_success') ? 'true' : 'false' }},

          /* Verification Function Simulation */
          verifyCodeAction() {
              if (this.resetCode.length !== 6) {
                  this.verifyStatus = 'error';
                  this.showVerifyModal = true;
              } else {
                  this.verifyStatus = 'loading';
                  this.showVerifyModal = true;
                  
                  // Simulate backend code checking (replace with actual fetch/axios call if needed)
                  setTimeout(() => {
                      this.verifyStatus = 'success';
                      setTimeout(() => {
                          this.showVerifyModal = false;
                          this.view = 'new_password';
                      }, 1200);
                  }, 1000);
              }
          }
      }">

    {{-- Back to Main Page Button --}}
    <a href="/" class="absolute top-6 left-6 md:top-8 md:left-8 flex items-center gap-2 text-gray-500 hover:text-[#a52a2a] font-bold text-xs uppercase tracking-widest transition-colors group">
        <div class="bg-white p-2 rounded-full shadow-sm border border-gray-200 group-hover:border-[#a52a2a] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </div>
        <span class="mt-0.5">Back to Main Page</span>
    </a>

    <div class="w-full max-w-md px-4 mt-12 md:mt-0">
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 overflow-hidden">
             
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
                {{-- Informational Messages (Success handled via modal now) --}}
                @if (session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('status') }}</span>
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
                    <input type="hidden" name="form_type" value="login">
                    
                    <div class="mb-5">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" name="email" x-model="email" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:bg-white outline-none transition-colors text-sm">
                    </div>
                    
                    <div class="mb-2" x-data="{ show: false }">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 pr-10 rounded-lg focus:ring-2 focus:ring-[#a52a2a] focus:bg-white outline-none transition-colors text-sm">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#a52a2a]">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
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
                    <input type="hidden" name="form_type" value="forgot">
                    
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
                               @keydown.enter="verifyCodeAction()"
                               class="w-full bg-gray-50 border border-gray-300 px-4 py-4 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none text-center text-2xl font-bold tracking-[0.5em] uppercase" 
                               placeholder="······" 
                               maxlength="6">
                    </div>
                    <button type="button" 
                            @click="verifyCodeAction()" 
                            class="w-full bg-[#a52a2a] text-white font-bold py-3.5 rounded-lg hover:bg-red-800 transition-all shadow-md uppercase tracking-widest text-sm mb-6">
                        Verify Code
                    </button>
                    <div class="text-center border-t border-gray-100 pt-6">
                        <button type="button" @click="view = 'forgot'" class="text-sm font-semibold text-gray-500 hover:text-gray-900 focus:outline-none">Didn't get a code?</button>
                    </div>
                </div>

                {{-- 4. NEW PASSWORD FORM --}}
                <form action="/admin/password/reset" method="POST" x-show="view === 'new_password'" x-cloak x-transition>
                    @csrf
                    <input type="hidden" name="form_type" value="reset">
                    <input type="hidden" name="email" :value="email">
                    <input type="hidden" name="code" :value="resetCode">
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-1">Resetting for:</label>
                        <span class="text-sm font-bold text-gray-800 break-all" x-text="email"></span>
                    </div>

                    <div class="mb-5" x-data="{ show: false }">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">New Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 pr-10 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none transition-colors text-sm">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#a52a2a]">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-8" x-data="{ show: false }">
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Confirm Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation" required class="w-full bg-gray-50 border border-gray-300 px-4 py-3 pr-10 rounded-lg focus:ring-2 focus:ring-[#a52a2a] outline-none transition-colors text-sm">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#a52a2a]">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
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

    {{-- MODALS SECTION --}}

    {{-- Verify Code Modal --}}
    <div x-show="showVerifyModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all text-center" @click.away="verifyStatus === 'error' ? showVerifyModal = false : null">
            
            <div x-show="verifyStatus === 'loading'">
                <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-[#a52a2a] mx-auto mb-4"></div>
                <h3 class="text-lg font-bold text-gray-900">Verifying Code...</h3>
                <p class="text-sm text-gray-500 mt-2">Please wait while we check your code.</p>
            </div>
            
            <div x-show="verifyStatus === 'success'" x-cloak>
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Code Verified!</h3>
                <p class="text-sm text-gray-500 mt-2">Redirecting to create a new password...</p>
            </div>
            
            <div x-show="verifyStatus === 'error'" x-cloak>
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Invalid Code</h3>
                <p class="text-sm text-gray-500 mt-2">The code you entered is incomplete or incorrect. Please try again.</p>
                <button @click="showVerifyModal = false" class="mt-6 w-full bg-gray-100 text-gray-800 font-bold py-3 rounded-lg hover:bg-gray-200 transition-colors">
                    Try Again
                </button>
            </div>
        </div>
    </div>

    {{-- Password Reset Success Modal --}}
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div @click.away="showSuccessModal = false" class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Password Reset!</h3>
            <p class="text-sm text-gray-500 mb-6">Your password has been successfully updated. You can now securely sign in to your account.</p>
            <button @click="showSuccessModal = false; view = 'login'" class="w-full bg-[#a52a2a] text-white font-bold py-3 rounded-lg hover:bg-red-800 transition-colors uppercase tracking-widest text-sm">
                Go to Login
            </button>
        </div>
    </div>

</body>
</html>