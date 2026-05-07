<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Advisory; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index() {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function($banner) {
                return asset('storage/' . $banner->image_path);
            });

        $advisories = Advisory::all();
        return view('index', compact('banners', 'advisories'));
    }

    public function store(Request $request) {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->hasFile('image')) {
                        $originalName = $request->file('image')->getClientOriginalName();
                        // Search for the original filename suffix in the database
                        $exists = Banner::where('image_path', 'LIKE', '%_' . $originalName)->exists();
                        if ($exists) {
                            $fail("An image named '{$originalName}' has already been uploaded. Please rename the file.");
                        }
                    }
                }
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    // Only block duplicates if the banner is being set to Active
                    if ($request->is_active == 1) {
                        $exists = Banner::where('is_active', 1)->where('sort_order', $value)->exists();
                        if ($exists) {
                            $fail("Position {$value} is already occupied by an active banner.");
                        }
                    }
                },
            ],
            'is_active' => 'required|boolean'
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('banners', $filename, 'public');
        
        Banner::create([
            'image_path' => $path,
            'sort_order' => $request->sort_order,
            'is_active' => $request->is_active,
        ]);
        
        return back()->with('success', 'Banner added successfully!');
    }

    public function update(Request $request, Banner $banner) {
        $request->validate([
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                function ($attribute, $value, $fail) use ($request, $banner) {
                    if ($request->hasFile('image')) {
                        $originalName = $request->file('image')->getClientOriginalName();
                        // Check if name exists in OTHER banners
                        $exists = Banner::where('id', '!=', $banner->id)
                                        ->where('image_path', 'LIKE', '%_' . $originalName)
                                        ->exists();
                        if ($exists) {
                            $fail("The filename '{$originalName}' is already used by another banner.");
                        }
                    }
                }
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request, $banner) {
                    if ($request->is_active == 1) {
                        $conflict = Banner::where('is_active', 1)
                                          ->where('sort_order', $value)
                                          ->where('id', '!=', $banner->id)
                                          ->exists();
                        if ($conflict) {
                            $fail("Sort order {$value} is already assigned to another active banner.");
                        }
                    }
                },
            ],
            'is_active' => 'required|boolean'
        ]);

        $data = [
            'sort_order' => $request->sort_order,
            'is_active' => $request->is_active,
        ];

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('banners', $filename, 'public');
        }
        
        $banner->update($data);
        return back()->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner) {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();
        return back()->with('success', 'Banner deleted successfully!');
    }

    public function adminIndex() {
        // Changed to paginate by 5
        $banners = Banner::orderBy('sort_order', 'asc')->paginate(10); 
        return view('admin.banners.index', compact('banners'));
    }
}