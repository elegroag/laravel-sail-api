<?php

use App\Http\Controllers\Api\AuthMercurioController;
use App\Http\Controllers\Api\ApiEndpointController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\NucleoFamiliarController;
use App\Http\Controllers\Api\TrabajadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate', [AuthMercurioController::class, 'authenticateAction'])->name('api.authenticate');
Route::post('register', [AuthMercurioController::class, 'registerAction'])->name('api.register');
Route::post('verify_store', [AuthMercurioController::class, 'verifyStore'])->name('api.verify_store');
Route::post('recovery_send', [AuthMercurioController::class, 'recoverySend'])->name('api.recovery_send');

// Rutas para Empresas
Route::apiResource('empresas', EmpresaController::class);

// Rutas para Trabajadores
Route::apiResource('trabajadores', TrabajadorController::class);

// Rutas para Núcleos Familiares
Route::apiResource('nucleos-familiares', NucleoFamiliarController::class);

// Rutas para API Endpoints
Route::apiResource('endpoints', ApiEndpointController::class);
Route::post('endpoints/sync-defaults', [ApiEndpointController::class, 'syncDefaults'])->name('api.endpoints.sync-defaults');

Route::fallback(function (Request $request) {
    $ruta = $request->url();

    return response()->json([
        'status' => false,
        'message' => "Ruta {$ruta} no está disponible para acceder.",
        'code' => 404,
    ], 404);
});
