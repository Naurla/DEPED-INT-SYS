<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name), // Auto-generates a slug like "content-editor"
            'permissions' => $request->permissions ?? []
        ]);

        return back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'permissions' => $request->permissions ?? []
        ]);

        return back()->with('success', 'Role configuration updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Prevent deleting roles that still have users assigned to them
        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete this role because users are currently assigned to it.']);
        }
        
        $role->delete();
        return back()->with('success', 'Role deleted successfully.');
    }
}