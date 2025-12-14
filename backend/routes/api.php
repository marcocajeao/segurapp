<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GuardCheckController;

Route::get('/ping', fn () => response()->json(['status' => 'ok']));

// Auth
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// Guardia (requiere rol GUARDIA)
Route::middleware(['auth:sanctum', 'role:GUARDIA'])->group(function () {
    Route::get('/guard/check/{qrToken}', [GuardCheckController::class, 'check']);
});
