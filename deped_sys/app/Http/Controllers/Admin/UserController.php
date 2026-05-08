<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedMail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search Filter (by name or email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Year Filter
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Month Filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Sort Filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('created_at')->oldest('id');
                    break;
                case 'a_z':
                    $query->orderBy('name', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('created_at')->latest('id');
                    break;
            }
        } else {
            $query->latest('created_at')->latest('id');
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();
        
        // Get unique years for the dropdown filter
        $years = User::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.users.index', compact('users', 'roles', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
        ], [
            'email.unique' => 'A user with this email address already exists in the system.'
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

        return back()->with('success', 'User created successfully! An email with their temporary password has been sent.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, 
            'role_id' => 'required|exists:roles,id',
        ], [
            'email.unique' => 'This email address is already taken by another user.'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}