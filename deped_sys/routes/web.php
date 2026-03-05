<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\AdvisoryController;
use App\Http\Controllers\BannerController;
use App\Models\Banner;
// Add this line to access the Advisory model
use App\Models\Advisory; 

// UPDATE THIS ROUTE:
Route::get('/', function () {
    // This gets all advisories from PostgreSQL, newest first
    $advisories = Advisory::latest()->get(); 
    
    // Fetch banners from database
    $dbBanners = \App\Models\Banner::all();
    
    if($dbBanners->isEmpty()) {
        // Fallback images if database is empty so the site doesn't crash
        $banners = collect([
            asset('images/r9.png'), 
            asset('images/foi.png'), 
            asset('images/deped.png')
        ]);
    } else {
        // Convert database objects into a simple array of image URLs
        $banners = $dbBanners->map(fn($banner) => asset('storage/' . $banner->image_path));
    }

    return view('index', compact('advisories', 'banners'));
});

Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/advisories/store', [AdvisoryController::class, 'store'])->name('advisories.store');
// Update your existing admin dashboard or add a specific route for management
Route::get('/admin/advisories', [AdvisoryController::class, 'index'])->name('admin.advisory.index');
// Update the update route to match your form's URL structure
Route::put('/admin/advisories/{advisory}', [AdvisoryController::class, 'update'])->name('advisories.update');

// Ensure your delete route is also present
Route::delete('/admin/advisories/{advisory}', [AdvisoryController::class, 'destroy'])->name('advisories.destroy');
Route::get('/admin/banners', [BannerController::class, 'index'])->name('admin.banners.index');
Route::post('/admin/banners', [BannerController::class, 'store'])->name('banners.store');
Route::delete('/admin/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');