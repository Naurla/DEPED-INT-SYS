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
        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'position' => 'required|in:left,right,footer_left,footer_right',
            'order' => 'integer'
        ]);

        $order = $request->order ?? 1;

        // Check if a logo already exists in this position with this exact order
        $existingLogo = SiteLogo::where('position', $request->position)
                                ->where('order', $order)
                                ->first();

        // If it exists, redirect back with a user-friendly error message
        if ($existingLogo) {
            return redirect()->back()
                ->withInput() // Keeps the user's typed inputs
                ->with('error', 'A logo already exists in this section with Sort Order ' . $order . '. Please choose a different order number or edit the existing logo instead.');
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
        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'position' => 'required|in:left,right,footer_left,footer_right',
            'order' => 'integer'
        ]);

        $order = $request->order ?? 1;

        // Check for conflicts, ignoring the current logo being edited
        $conflictLogo = SiteLogo::where('position', $request->position)
                                ->where('order', $order)
                                ->where('id', '!=', $logo->id)
                                ->first();
        
        if ($conflictLogo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Another logo already exists in this section with Sort Order ' . $order . '. Please choose a different order number.');
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