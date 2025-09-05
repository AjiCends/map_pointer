<?php

use App\Http\Controllers\ActivityController;
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
    Route::get('/programs/{program}/activities', [ActivityController::class, 'index'])
        ->name('activities.index');
    Route::get('/programs/{program}/activities', [ActivityController::class, 'create'])
        ->name('activities.create');
    Route::post('/programs/{program}/activities', [ActivityController::class, 'store'])
        ->name('activities.store');
    Route::get('/programs/{program}/activities/{id}', [ActivityController::class, 'edit'])
        ->name('activities.edit');
    Route::put('/programs/{program}/activities/{id}', [ActivityController::class, 'update'])
        ->name('activities.update');
    Route::delete('/programs/{program}/activities/{id}', [ActivityController::class, 'destroy'])
        ->name('activities.destroy');
});

require __DIR__ . '/auth.php';
