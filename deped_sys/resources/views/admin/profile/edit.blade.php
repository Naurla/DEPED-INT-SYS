@extends('layouts.admin')
@section('page_title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-[#a52a2a] px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white">
                {{ $isVerified ? 'Update Profile Details' : 'Verify Identity' }}
            </h2>
            <span class="text-xs text-red-200">{{ auth()->user()->email }}</span>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(!$isVerified)
                <form action="{{ route('admin.profile.verify') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-[#a52a2a] mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-gray-600">To protect your account, please verify your password before making any changes.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Confirm Current Password</label>
                        <input type="password" name="current_password" required autofocus
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-3" 
                               placeholder="••••••••">
                        @error('current_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#a52a2a] text-white font-bold rounded-lg hover:bg-red-800 transition shadow-md">
                        Verify Password
                    </button>
                </form>

            @else
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col items-center bg-gray-50 p-6 rounded-xl border border-dashed border-gray-300">
                        <div class="relative group cursor-pointer">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-lg">
                            @else
                                <div class="h-32 w-32 rounded-full bg-red-700 flex items-center justify-center text-white text-4xl font-black shadow-lg">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 w-full max-w-xs">
                            <input type="file" name="photo" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200">
                            @error('photo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required 
                                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">New Password</label>
                                <input type="password" name="password" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Optional">
                                @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.profile.edit') }}" class="text-sm text-gray-400 hover:text-gray-600 font-medium">Cancel Changes</a>
                        <button type="submit" class="px-8 py-2.5 bg-[#a52a2a] text-white font-bold rounded-lg hover:bg-red-800 transition shadow-lg">
                            Save Profile
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection