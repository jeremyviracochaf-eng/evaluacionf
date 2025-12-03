<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtraccionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AuthController;


Route::apiResource('atracciones', AtraccionController::class);
Route::apiResource('reservas', ReservaController::class);

// Auth
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Protegemos CRUD de atracciones y reservas si quieres que solo usuarios autenticados accedan:
    Route::apiResource('atracciones', AtraccionController::class);
    Route::apiResource('reservas', ReservaController::class);
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
