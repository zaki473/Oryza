<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

// Gunakan Group agar lebih teratur
Route::prefix('control')->group(function () {
    Route::post('/servo', [SensorController::class, 'toggleServo'])->name('api.servo.toggle');
});

Route::get('/sensor', [SensorController::class, 'getData']);
Route::post('/sensor', [SensorController::class, 'store']);
Route::get('/history', [SensorController::class, 'history']);
