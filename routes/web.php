<?php

use Illuminate\Support\Facades\Route; // must be here
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
//use Illuminate\Support\Facades\Artisan;



// Реєстрація                                                                                                
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profile', [AuthController::class, 'profile'])->name('profile')->middleware('auth');
Route::post('/profile/update', [AuthController::class, 'updateProfile'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('events', EventController::class);
});

// Optional: make / go to login
Route::get('/', fn() => redirect()->route('login'));
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');
