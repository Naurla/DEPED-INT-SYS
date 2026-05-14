<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Page; 
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

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
        
        $years = User::pluck('created_at')
            ->filter()
            ->map(fn($date) => $date->format('Y'))
            ->unique()
            ->sortDesc()
            ->values();

        $dynamicPages = Page::with('children')->whereNull('parent_id')->get();

        return view('admin.users.index', compact('users', 'roles', 'years', 'dynamicPages'));
    }

    public function store(Request $request)
    {
        // Automatically clean before validating so the unique check works correctly
        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name' => trim($request->name)
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
        ], [
            'email.unique' => 'A user with this email address already exists in the system.'
        ]);

        $tempPassword = Str::random(10);

        $user = new User();
        $user->name = $request->name; // Already trimmed above
        $user->email = $request->email; // Already lowercase and trimmed above
        $user->role_id = $request->role_id;
        $user->password = Hash::make($tempPassword); 
        $user->requires_password_change = true;
        $user->save();

        Mail::to($user->email)->send(new UserCreatedMail($user, $tempPassword));

        return back()->with('success', 'User created successfully! An email with their temporary password has been sent.');
    }

    public function update(Request $request, User $user)
    {
        // Clean inputs here too, to prevent spaces sneaking in during edits
        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name' => trim($request->name)
        ]);

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

    public function resetPassword(Request $request, User $user)
    {
        if (auth()->user()->role && auth()->user()->role->slug !== 'super-admin') {
            return back()->withErrors(['error' => 'Unauthorized action. Only super admins can manually reset user passwords.']);
        }

        $tempPassword = Str::random(10);

        $user->password = Hash::make($tempPassword);
        $user->requires_password_change = true;
        $user->save();

        Mail::to($user->email)->send(new UserCreatedMail($user, $tempPassword));

        return back()->with('success', "Password successfully reset for {$user->name}. An email with the new temporary password has been sent to their inbox.");
    }
}