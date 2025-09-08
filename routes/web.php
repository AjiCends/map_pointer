<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Program routes
    Route::resource('programs', ProgramController::class);

    // Activity routes
    Route::get('/programs/{program}/activities/create', [ActivityController::class, 'create'])
        ->name('activities.create');
    Route::post('/programs/{program}/activities', [ActivityController::class, 'store'])
        ->name('activities.store');
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])
        ->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])
        ->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])
        ->name('activities.destroy');

    // Gallery routes
    Route::get('/activities/{activity}/gallery', [GalleryController::class, 'index'])
        ->name('gallery.index');
    Route::get('/activities/{activity}/gallery/create', [GalleryController::class, 'create'])
        ->name('gallery.create');
    Route::post('/activities/{activity}/gallery', [GalleryController::class, 'store'])
        ->name('gallery.store');
    Route::get('/activities/{activity}/gallery/{gallery}', [GalleryController::class, 'show'])
        ->name('gallery.show');
    Route::get('/activities/{activity}/gallery/{gallery}/edit', [GalleryController::class, 'edit'])
        ->name('gallery.edit');
    Route::put('/activities/{activity}/gallery/{gallery}', [GalleryController::class, 'update'])
        ->name('gallery.update');
    Route::delete('/activities/{activity}/gallery/{gallery}', [GalleryController::class, 'destroy'])
        ->name('gallery.destroy');
});

require __DIR__ . '/auth.php';
