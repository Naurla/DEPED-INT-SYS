<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // Add Mail facade
use App\Mail\UserCreatedMail; // Add our new mail class

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Generate temporary password
        $tempPassword = Str::random(10);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role_id' => $request->role_id,
            'requires_password_change' => true,
        ]);

        // Send the email with the temporary password
        Mail::to($user->email)->send(new UserCreatedMail($user, $tempPassword));

        // Return a generic success message WITHOUT the password
        return back()->with('success', 'User created successfully! An email with their temporary password has been sent.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}