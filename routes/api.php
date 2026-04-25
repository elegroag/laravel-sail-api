<?php

use App\Http\Controllers\Api\AuthMercurioController;
use App\Http\Controllers\Api\ApiEndpointController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\NucleoFamiliarController;
use App\Http\Controllers\Api\TrabajadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('solicitar-token', [AuthMercurioController::class, 'solicitarToken'])->name('api.solicitar-token');
Route::post('validate-token', [AuthMercurioController::class, 'validateToken'])->name('api.validate-token');

Route::middleware(['api.auth'])->group(function () {
    Route::post('authenticate-movile', [AuthMercurioController::class, 'authenticateMovile'])->name('api.authenticate-movile');
    Route::post('change-password-movile', [AuthMercurioController::class, 'changePasswordMovile'])->name('api.change-password-movile');

    Route::apiResource('empresas', EmpresaController::class);
    Route::apiResource('trabajadores', TrabajadorController::class);
    Route::apiResource('nucleos-familiares', NucleoFamiliarController::class);
    Route::apiResource('endpoints', ApiEndpointController::class);
    Route::post('endpoints/sync-defaults', [ApiEndpointController::class, 'syncDefaults'])->name('api.endpoints.sync-defaults');
});

Route::fallback(function (Request $request) {
    $ruta = $request->url();

    return response()->json([
        'status' => false,
        'message' => "Ruta {$ruta} no está disponible para acceder.",
        'code' => 404,
    ], 404);
});
