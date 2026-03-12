<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\AdvisoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\ProcurementController;
use App\Http\Controllers\Frontend\BidOpportunityController;
use App\Http\Controllers\Frontend\FileAccessController;
use App\Models\Banner;
use App\Models\Advisory; 

// ==========================================
// PUBLIC ROUTES
// ==========================================

Route::get('/', function () {
    $latestAdvisory = Advisory::latest()->first(); 
    $dbBanners = Banner::all();
    
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

// Admin Login
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// Public Issuances
Route::get('/issuances/advisories', [IssuanceController::class, 'advisories'])->name('issuances.advisories');
Route::get('/issuances/memoranda', [IssuanceController::class, 'memoranda'])->name('issuances.memoranda');
Route::get('/issuances/hrmpsb', [IssuanceController::class, 'hrmpsb'])->name('issuances.hrmpsb');
Route::get('/issuances/view/{issuance}', [IssuanceController::class, 'show'])->name('issuances.show');

// Public Procurement
Route::prefix('procurement/{category}')->name('procurement.')->group(function () {
    Route::get('/', [App\Http\Controllers\Frontend\BidOpportunityController::class, 'index'])->name('index');
    Route::get('/{id}', [App\Http\Controllers\Frontend\BidOpportunityController::class, 'show'])->name('show');
});

// ==========================================
// SECURE ROUTES
// ==========================================

Route::middleware(['auth'])->group(function () {

    // LOGOUT ROUTE (Placed here so it is accessible as route('logout'))
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Secure File Access
    Route::get('/procurement/bid-opportunities/file/{id}/{type}', [FileAccessController::class, 'show'])
        ->name('procurement.file.access');

    // Protected Admin Management
    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Super Admin: User Management
        Route::middleware(['role:super-admin'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Info Office: Advisories & Banners
        Route::middleware(['role:info-office'])->group(function () {
            Route::get('/advisories', [AdvisoryController::class, 'index'])->name('advisory.index');
            Route::post('/advisories/store', [AdvisoryController::class, 'store'])->name('advisories.store');
            Route::put('/advisories/{advisory}', [AdvisoryController::class, 'update'])->name('advisories.update');
            Route::delete('/advisories/{advisory}', [AdvisoryController::class, 'destroy'])->name('advisories.destroy');
            
            Route::get('/banners', [BannerController::class, 'adminIndex'])->name('banners.index');
            Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
            Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
        });

        // Issuance Manager
        Route::middleware(['role:issuance-manager'])->prefix('issuances')->name('issuances.')->group(function () {
            Route::get('/', [IssuanceController::class, 'adminIndex'])->name('index');
            Route::post('/', [IssuanceController::class, 'store'])->name('store');
            Route::put('/{issuance}', [IssuanceController::class, 'update'])->name('update');
            Route::delete('/{issuance}', [IssuanceController::class, 'destroy'])->name('destroy');
        });

      // Procurement Management
        Route::prefix('procurement/{category}')->name('procurement.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ProcurementController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\ProcurementController::class, 'store'])->name('store');
            Route::put('/{id}', [App\Http\Controllers\Admin\ProcurementController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\ProcurementController::class, 'destroy'])->name('destroy');
        });
    }); // End of admin prefix group
}); // End of auth