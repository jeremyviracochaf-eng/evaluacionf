<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtraccionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AtraccionImportController;
use App\Services\FirebaseStorageService;

// --- Autenticación ---
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // --- Reservas (solo autenticados) ---
    Route::apiResource('reservas', ReservaController::class);

    // --- Atracciones y Administración (solo admin) ---
    // Este grupo ya hereda 'auth:sanctum' por estar dentro del grupo principal
    Route::middleware('is_admin')->group(function () {
        
        // CRUD de Atracciones
        Route::post('atracciones', [AtraccionController::class, 'store']);
        Route::put('atracciones/{id}', [AtraccionController::class, 'update']);
        Route::delete('atracciones/{id}', [AtraccionController::class, 'destroy']);
        
        // AQUÍ AGREGAMOS LA NUEVA RUTA DE IMPORTACIÓN
        Route::post('atracciones/import-google', [AtraccionImportController::class, 'importFromGoogle']);

        // AQUÍ AGREGAMOS LA RUTA DE CAMBIAR ESTADO 
        // Al estar aquí dentro, automáticamente requiere estar logueado Y ser admin
        Route::put('reservas/{id}/estado', [ReservaController::class, 'cambiarEstado']);

        // AQUÍ AGREGAMOS LA RUTA DE SUBIR IMAGEN
        Route::post('atracciones/{id}/imagen', [AtraccionController::class, 'uploadImage']);
    });
});


// --- Atracciones públicas (todos pueden ver) ---
Route::get('atracciones', [AtraccionController::class, 'index']);
Route::get('atracciones/{id}', [AtraccionController::class, 'show']);

// --- Endpoint extra para probar usuario autenticado ---
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');