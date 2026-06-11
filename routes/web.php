<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

// Home
// Home
Route::get('/', function () {
    return view('user.home'); 
})->name('home');

// Login - pakai middleware guest supaya yg sudah login ga bisa balik ke sini

// Dashboard - pakai middleware auth supaya harus login dulu
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/api/logout-firebase', function () {
    return response()->json(['message' => 'ok']);
});

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

Route::post('/api/login-firebase', function (\Illuminate\Http\Request $request) {
    $token = $request->token;

    return response()->json([
        'message' => 'Token diterima',
        'token' => $token
    ]);
});

// Auth
require __DIR__.'/auth.php';