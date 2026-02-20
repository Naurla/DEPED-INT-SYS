<?php

use Illuminate\Support\Facades\Route;
// ADD THIS LINE BELOW:
use App\Http\Controllers\AdminController; 

Route::get('/', function () {
    return view('index');
});

// Now Laravel knows exactly where AdminController is
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');