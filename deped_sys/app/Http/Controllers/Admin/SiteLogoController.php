<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SiteLogoController extends Controller
{
    public function index()
    {
        $logos = SiteLogo::orderBy('position', 'asc')->orderBy('order', 'asc')->get();
        return view('admin.logos.index', compact('logos'));
    }

    public function store(Request $request)
    {
        // Custom user-friendly messages
        $messages = [
            'name.required' => 'The Logo Name is now required.',
            'name.unique' => 'A logo with the name ":input" already exists. Please provide a unique name.',
        ];

        $request->validate([
            'name' => 'required|string|max:255|unique:site_logos,name', // Now Required and Unique
            'image' => 'required|image|max:2048',
            'position' => 'required|in:left,right,footer_left,footer_right',
            'order' => 'integer'
        ], $messages);

        $order = $request->order ?? 1;

        // Duplication Check for Position + Order
        $existingLogo = SiteLogo::where('position', $request->position)
                                ->where('order', $order)
                                ->first();

        if ($existingLogo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Section Conflict: A logo already exists in the ' . str_replace('_', ' ', $request->position) . ' with Sort Order ' . $order . '.');
        }

        $path = $request->file('image')->store('logos', 'public');

        SiteLogo::create([
            'name' => $request->name,
            'image_path' => $path,
            'position' => $request->position,
            'order' => $order,
            'is_active' => $request->has('is_active'),
        ]);

        Cache::forget('site_logos');
        return redirect()->back()->with('success', 'Logo added successfully!');
    }

    public function update(Request $request, SiteLogo $logo)
    {
        $messages = [
            'name.required' => 'The Logo Name is now required.',
            'name.unique' => 'A logo with the name ":input" already exists.',
        ];

        $request->validate([
            'name' => 'required|string|max:255|unique:site_logos,name,' . $logo->id, // Required + Unique ignore self
            'image' => 'nullable|image|max:2048',
            'position' => 'required|in:left,right,footer_left,footer_right',
            'order' => 'integer'
        ], $messages);

        $order = $request->order ?? 1;

        // Check for conflicts, ignoring the current logo being edited
        $conflictLogo = SiteLogo::where('position', $request->position)
                                ->where('order', $order)
                                ->where('id', '!=', $logo->id)
                                ->first();
        
        if ($conflictLogo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sort Order Conflict: Another logo is already using order ' . $order . ' in this section.');
        }

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($logo->image_path)) {
                Storage::disk('public')->delete($logo->image_path);
            }
            $logo->image_path = $request->file('image')->store('logos', 'public');
        }

        $logo->name = $request->name;
        $logo->position = $request->position;
        $logo->order = $order;
        $logo->is_active = $request->has('is_active');
        $logo->save();

        Cache::forget('site_logos');
        return redirect()->back()->with('success', 'Logo updated successfully!');
    }

    public function destroy(SiteLogo $logo)
    {
        if (Storage::disk('public')->exists($logo->image_path)) {
            Storage::disk('public')->delete($logo->image_path);
        }
        $logo->delete();

        Cache::forget('site_logos');
        return redirect()->back()->with('success', 'Logo deleted successfully!');
    }
}