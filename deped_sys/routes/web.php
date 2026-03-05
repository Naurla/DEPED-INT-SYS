<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\AdvisoryController;
// Add this line to access the Advisory model
use App\Models\Advisory; 

// UPDATE THIS ROUTE:
Route::get('/', function () {
    // This gets all advisories from PostgreSQL, newest first
    $advisories = Advisory::latest()->get(); 
    
    // This passes the $advisories variable to your index.blade.php
    return view('index', compact('advisories'));
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