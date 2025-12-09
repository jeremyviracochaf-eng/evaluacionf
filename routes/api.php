<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtraccionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AuthController;


// --- Autenticación ---
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // --- Reservas (solo autenticados) ---
    Route::apiResource('reservas', ReservaController::class);

    // --- Atracciones (solo admin puede crear/editar/eliminar) ---
    Route::middleware('is_admin')->group(function () {
        Route::post('atracciones', [AtraccionController::class, 'store']);
        Route::put('atracciones/{id}', [AtraccionController::class, 'update']);
        Route::delete('atracciones/{id}', [AtraccionController::class, 'destroy']);
    });
});

// --- Atracciones públicas (todos pueden ver) ---
Route::get('atracciones', [AtraccionController::class, 'index']);
Route::get('atracciones/{id}', [AtraccionController::class, 'show']);

// --- Endpoint extra para probar usuario autenticado ---
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
