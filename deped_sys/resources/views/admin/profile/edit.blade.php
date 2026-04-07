@extends('layouts.admin')
@section('page_title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto pb-10">
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your profile information and security credentials.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="border-b border-gray-100 px-8 py-5 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $isVerified ? 'Profile Information' : 'Security Verification' }}
            </h2>
            @if($isVerified)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Identity Verified
                </span>
            @endif
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(!$isVerified)
                <form action="{{ route('admin.profile.verify') }}" method="POST" class="max-w-md mx-auto py-6">
                    @csrf
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-[#a52a2a] mb-5 ring-4 ring-red-50/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Verify your password</h3>
                        <p class="text-sm text-gray-500 mt-2">To protect your account, please confirm your current password to continue making changes.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" required autofocus
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-colors py-2.5" 
                               placeholder="••••••••">
                        @error('current_password') <span class="text-sm text-red-600 mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-[#a52a2a] text-white font-medium rounded-lg hover:bg-red-800 transition-colors focus:ring-4 focus:ring-red-200 shadow-sm">
                        Confirm Password
                    </button>
                </form>

            @else
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col md:flex-row gap-10">
                        <div class="flex flex-col items-center space-y-4 md:w-1/3">
                            <div class="relative group">
                                <div class="h-40 w-40 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100 ring-1 ring-gray-200 relative">
                                    
                                    <img id="profile-image-preview" 
                                         src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : '#' }}" 
                                         class="absolute inset-0 h-full w-full object-cover z-10 {{ auth()->user()->profile_photo_path ? '' : 'hidden' }}" 
                                         alt="Profile Photo">
                                    
                                    @if(!auth()->user()->profile_photo_path)
                                        <div id="profile-initials" class="absolute inset-0 h-full w-full bg-red-50 flex items-center justify-center text-[#a52a2a] text-5xl font-bold z-0">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif

                                </div>
                                
                                <label for="photo-upload" class="absolute bottom-1 right-1 bg-white rounded-full p-2.5 shadow-md border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 z-20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </label>
                            </div>
                            
                            <div class="text-center w-full">
                                <input id="photo-upload" type="file" name="photo" accept="image/*" class="hidden">
                                <label for="photo-upload" class="text-sm font-medium text-[#a52a2a] hover:text-red-800 cursor-pointer transition-colors">
                                    Upload new photo
                                </label>
                                <p class="text-xs text-gray-400 mt-1">JPG, GIF or PNG. Max 1MB.</p>
                                @error('photo') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex-1 space-y-6">
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required 
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-colors py-2.5">
                                    @error('name') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                        </div>
                                        <input type="email" value="{{ auth()->user()->email }}" disabled 
                                               class="w-full pl-10 py-2.5 rounded-lg border-gray-200 bg-gray-50 text-gray-500 shadow-sm cursor-not-allowed">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1.5">Email address is used for login and cannot be changed here.</p>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-100">
                                <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Change Password</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                        <input type="password" name="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-colors py-2.5" placeholder="Leave blank to keep current">
                                        @error('password') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-colors py-2.5" placeholder="Repeat new password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 mt-8 border-t border-gray-100 space-x-3">
                        <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#a52a2a] rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-200 transition-colors shadow-sm inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoUpload = document.getElementById('photo-upload');
        const previewImage = document.getElementById('profile-image-preview');
        const initialsDiv = document.getElementById('profile-initials');

        if(photoUpload) {
            photoUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        // Update the src of the image tag to the newly uploaded file
                        previewImage.src = e.target.result;
                        
                        // Reveal the image tag if it was hidden
                        previewImage.classList.remove('hidden');
                        
                        // Hide the fallback initials if they exist
                        if (initialsDiv) {
                            initialsDiv.classList.add('hidden');
                        }
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endsection