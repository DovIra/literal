<?php

use Illuminate\Support\Facades\Route; // must be here
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return '<h1>LOGIN PAGE WORKS 🎉</h1>';
});

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Optional: make / go to login
Route::get('/', fn () => redirect()->route('login'));

