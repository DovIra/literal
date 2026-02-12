<?php

use Illuminate\Support\Facades\Route; // must be here
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;

//use Illuminate\Support\Facades\Artisan;



Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profile', [AuthController::class, 'profile'])->name('profile')->middleware('auth');
Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('events', EventController::class);
    Route::get('/events/{event}/participants', [EventController::class, 'participants'])->name('events.participants');
    Route::delete('/events/{event}/participants/{user}', [EventController::class, 'removeParticipant'])->name('events.participants.remove');
});

// Optional: make / go to login
Route::get('/', fn() => redirect()->route('login'));
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar')->middleware('auth');

Route::middleware(['auth','can:admin'])
->prefix('admin')
->name('admin.')
->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::post('/events/{event}/join', [EventController::class, 'join'])->name('events.join');
Route::delete('/events/{event}/leave', [EventController::class, 'leave'])->name('events.leave');
