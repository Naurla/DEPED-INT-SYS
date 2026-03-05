<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index() {
        $banners = Banner::all();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request) {
        // 1. Validate the input
       $request->validate(['image' => 'requ ired|image|mimes:jpeg,png,jpg|max:2048']);
    
    // 2. Store file in storage/app/public/banners
    $path = $request->file('image')->store('banners', 'public');
    
    // 3. Save record to 'banners' table
    \App\Models\Banner::create(['image_path' => $path]);
    
    // 4. Redirect to the dashboard route (which now has the data)
    return redirect()->route('admin.dashboard')->with('success', 'Banner added successfully!');
    }

    public function destroy(Banner $banner) {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return back()->with('success', 'Banner deleted!');
    }
}