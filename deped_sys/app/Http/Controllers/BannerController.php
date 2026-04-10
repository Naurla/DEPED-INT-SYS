<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Advisory; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index() {
        // Only get ACTIVE banners and order them by sort_order
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->is_active == 1) {
                        if (Banner::where('is_active', 1)->where('sort_order', $value)->exists()) {
                            $fail("Position {$value} is already occupied by an active banner.");
                        }
                    }
                },
            ],
            'is_active' => 'required|boolean'
        ]);
    
        // 1. Get the original file name
        $file = $request->file('image');
        // 2. Prepend a timestamp so multiple files with the same name don't overwrite each other
        $filename = time() . '_' . $file->getClientOriginalName();
        // 3. Save it to storage using storeAs instead of store
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sort_order' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request, $banner) {
                    if ($request->is_active == 1) {
                        $conflict = Banner::where('is_active', 1)
                                          ->where('sort_order', $value)
                                          ->where('id', '!=', $banner->id)
                                          ->exists();
                        if ($conflict) {
                            $fail("Position {$value} is already occupied by another active banner.");
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

        // Only process the image if a new one was uploaded
        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            
            // Save the newly updated file with its original name as well
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('banners', $filename, 'public');
        }
        
        $banner->update($data);
        
        return back()->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner) {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        
        return back()->with('success', 'Banner deleted!');
    }

    public function adminIndex() {
        // Order the table by sort_order in the admin panel too
        $banners = Banner::orderBy('sort_order', 'asc')->get(); 

        return view('admin.banners.index', compact('banners'));
    }
}