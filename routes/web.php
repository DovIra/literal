<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventImportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');

    Route::get('/events/import', [EventImportController::class, 'showImportForm'])->name('events.import.form');
    Route::post('/events/import', [EventImportController::class, 'import'])->name('events.import');

    Route::get('/events/download-template', [EventImportController::class, 'downloadTemplate'])->name('events.downloadTemplate');

    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::post('/events/{id}/join', [EventController::class, 'join'])->name('events.join');
    Route::post('/events/{id}/cancel', [EventController::class, 'cancelParticipation'])->name('events.cancel');
    Route::delete('/events/{id}/image/{index}', [EventController::class, 'removeImage'])->name('events.image.remove');

    Route::get('/events/{id}/review', [EventController::class, 'reviewForm'])->name('events.review.form');
    Route::post('/events/{id}/review', [EventController::class, 'reviewStore'])->name('events.review.store');

    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::get('/review/{reviewId}/edit', [EventController::class, 'reviewEdit'])->name('review.edit');
    Route::put('/review/{reviewId}', [EventController::class, 'reviewUpdate'])->name('review.update');
    Route::delete('/review/{reviewId}', [EventController::class, 'reviewDestroy'])->name('review.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});


require __DIR__.'/auth.php';
