<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AtraccionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AuthController;


Route::apiResource('atracciones', AtraccionController::class);
Route::apiResource('reservas', ReservaController::class);

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    Route::apiResource('atracciones', AtraccionController::class);
    Route::apiResource('reservas', ReservaController::class);
});





Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
