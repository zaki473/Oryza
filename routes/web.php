<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

// Home
Route::get('/', function () {
    return view('user.home'); 
})->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API SENSOR
Route::prefix('api')->group(function () {

    Route::get('/sensor', [SensorController::class, 'getData']);

    Route::post('/sensor', [SensorController::class, 'store'])
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/history', [SensorController::class, 'history']); // 🔥 tambahan
});

// Auth
require __DIR__.'/auth.php';