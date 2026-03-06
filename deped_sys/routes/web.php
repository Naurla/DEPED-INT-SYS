<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\AdvisoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\Admin\UserController; // Added User Controller for role management
use App\Models\Banner;
use App\Models\Advisory; 

// ==========================================
// PUBLIC ROUTES
// ==========================================

// Added ->name('login') to the end of this route to fix the "Route [login] not defined" error
Route::get('/', function () {
    // This gets all advisories from PostgreSQL, newest first
    $latestAdvisory = Advisory::latest()->first(); 
    
    // Fetch banners from database
    $dbBanners = \App\Models\Banner::all();
    
    if($dbBanners->isEmpty()) {
        $banners = collect([
            asset('images/r9.png'), 
            asset('images/foi.png'), 
            asset('images/deped.png')
        ]);
    } else {
        $banners = $dbBanners->map(fn($banner) => asset('storage/' . $banner->image_path));
    }

    return view('index', compact('latestAdvisory', 'banners'));
})->name('login'); 

// Public Issuances Views
Route::get('/issuances/advisories', [IssuanceController::class, 'advisories'])->name('issuances.advisories');
Route::get('/issuances/memoranda', [IssuanceController::class, 'memoranda'])->name('issuances.memoranda');
Route::get('/issuances/hrmpsb', [IssuanceController::class, 'hrmpsb'])->name('issuances.hrmpsb');
Route::get('/issuances/view/{issuance}', [IssuanceController::class, 'show'])->name('issuances.show');

// ==========================================
// ADMIN LOGIN (Unprotected)
// ==========================================
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// ==========================================
// PROTECTED ADMIN ROUTES
// ==========================================
Route::prefix('admin')->middleware(['auth'])->group(function () {
    
    // Dashboard accessible to any logged-in admin user
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // 1. Super Admin Only: User Management
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // 2. Info Office (and Super Admin): Public Advisories & Banners
    Route::middleware(['role:info-office'])->group(function () {
        // Advisories
        Route::get('/advisories', [AdvisoryController::class, 'index'])->name('admin.advisory.index');
        Route::post('/advisories/store', [AdvisoryController::class, 'store'])->name('advisories.store');
        Route::put('/advisories/{advisory}', [AdvisoryController::class, 'update'])->name('advisories.update');
        Route::delete('/advisories/{advisory}', [AdvisoryController::class, 'destroy'])->name('advisories.destroy');
        
        // Banners
        Route::get('/banners', [BannerController::class, 'adminIndex'])->name('admin.banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
    });

    // 3. Issuance Manager (and Super Admin): Memos, Division Advisories, HRMPSB
    Route::middleware(['role:issuance-manager'])->prefix('issuances')->name('admin.issuances.')->group(function () {
        Route::get('/', [IssuanceController::class, 'adminIndex'])->name('index');
        Route::post('/', [IssuanceController::class, 'store'])->name('store');
        Route::put('/{issuance}', [IssuanceController::class, 'update'])->name('update');
        Route::delete('/{issuance}', [IssuanceController::class, 'destroy'])->name('destroy');
    });

});