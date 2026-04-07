<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        // Check if the user has been verified in this session
        $isVerified = session()->get('profile_verified', false);
        return view('admin.profile.edit', compact('isVerified'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
        ]);

        if (Hash::check($request->current_password, auth()->user()->password)) {
            // Store verification in session. It will persist until logout or session expiry.
            session()->put('profile_verified', true);
            return redirect()->route('admin.profile.edit')->with('success', 'Password verified. You can now edit your profile.');
        }

        return redirect()->back()->withErrors(['current_password' => 'The provided password does not match our records.']);
    }

    public function update(Request $request)
    {
        // Ensure they didn't bypass the UI
        if (!session()->get('profile_verified')) {
            return redirect()->route('admin.profile.edit');
        }

        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        // REMOVED: session()->forget('profile_verified'); 
        // Now the user stays verified until they log out!

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully!');
    }
}