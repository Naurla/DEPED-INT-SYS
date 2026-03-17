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
           // Change this line in your store() and update() validation rules:
            'position' => 'required|in:left,right,footer_left,footer_right',
            'order' => 'integer'
        ]);

        $path = $request->file('image')->store('logos', 'public');

        SiteLogo::create([
            'name' => $request->name,
            'image_path' => $path,
            'position' => $request->position,
            'order' => $request->order ?? 0,
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
            // Change this line in your store() and update() validation rules:
            'position' => 'required|in:left,right,footer_left,footer_right',
            'order' => 'integer'
        ]);

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($logo->image_path)) {
                Storage::disk('public')->delete($logo->image_path);
            }
            $logo->image_path = $request->file('image')->store('logos', 'public');
        }

        $logo->name = $request->name;
        $logo->position = $request->position;
        $logo->order = $request->order ?? 0;
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
