<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuardCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Todas las rutas acá usan el middleware "api"
| y están pensadas para autenticación con tokens (Sanctum).
*/

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware(['auth:sanctum', 'role:GUARDIA'])->group(function () {
    Route::get('/guard/check/{qrToken}', [GuardCheckController::class, 'check']);
});

