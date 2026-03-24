<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Advisory; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index() {
        // This converts the database paths into full URLs for your Alpine.js slider
        $banners = Banner::all()->map(function($banner) {
            return asset('storage/' . $banner->image_path);
        });

        $advisories = Advisory::all();

        return view('index', compact('banners', 'advisories'));
    }

    public function store(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
    
        // Stores the file in storage/app/public/banners
        $path = $request->file('image')->store('banners', 'public');
        
        Banner::create(['image_path' => $path]);
        
        return back()->with('success', 'Banner added successfully!');
    }

    // ADDED THIS NEW METHOD TO HANDLE REPLACING/UPDATING
    public function update(Request $request, Banner $banner) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        // Delete the old image from storage
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        // Store the new image
        $path = $request->file('image')->store('banners', 'public');
        
        // Update the database record
        $banner->update(['image_path' => $path]);
        
        return back()->with('success', 'Banner replaced successfully!');
    }

    public function destroy(Banner $banner) {
        // Deletes the file from the public disk before removing the database record
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        
        return back()->with('success', 'Banner deleted!');
    }

    public function adminIndex() {
        $banners = Banner::all(); 

        return view('admin.banners.index', compact('banners'));
    }
}