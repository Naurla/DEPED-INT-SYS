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
        // Corrected the 'required' validation typo
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        // Stores the file in storage/app/public/banners
        $path = $request->file('image')->store('banners', 'public');
        
        Banner::create(['image_path' => $path]);
        
        // Ensures you are redirected back to the dashboard after upload
        return redirect()->route('admin.dashboard')->with('success', 'Banner added successfully!');
    }

    public function destroy(Banner $banner) {
        // Deletes the file from the public disk before removing the database record
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return back()->with('success', 'Banner deleted!');
    }

    public function adminIndex() {
    $banners = Banner::all(); // Get the raw banner models for management
    return view('admin.banners', compact('banners'));
}
}