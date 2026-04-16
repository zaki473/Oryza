<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Default endpoint (Bisa digunakan untuk welcome page API)
Route::get('/', [UserController::class, 'index']);

// === Routes untuk SPA Frontend (Next.js/React) ===

// Route Authentication (Login & Register)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected Route untuk Dashboard 
// (Middleware auth:sanctum memastikan hanya user yang login yang bisa akses API dashboard ini)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
    
    // Tambahkan API endpoint lain untuk dashboard di sini
    // Route::get('/dashboard/data', [DashboardController::class, 'data']);
});
