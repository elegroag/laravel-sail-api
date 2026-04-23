<?php

use App\Http\Controllers\Api\AuthMercurioController;
use App\Http\Controllers\Api\ApiEndpointController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\NucleoFamiliarController;
use App\Http\Controllers\Api\TrabajadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate', [AuthMercurioController::class, 'authenticateAction'])->name('api.authenticate');
Route::post('register/empresa', [AuthMercurioController::class, 'registerEmpresaAction'])->name('api.register.empresa');
Route::post('register/trabajador', [AuthMercurioController::class, 'registerTrabajadorAction'])->name('api.register.trabajador');
Route::post('register/particular', [AuthMercurioController::class, 'registerParticularAction'])->name('api.register.particular');
Route::post('register/independiente', [AuthMercurioController::class, 'registerIndependienteAction'])->name('api.register.independiente');
Route::post('register/pensionado', [AuthMercurioController::class, 'registerPensionadoAction'])->name('api.register.pensionado');
Route::post('register/facultativo', [AuthMercurioController::class, 'registerFacultativoAction'])->name('api.register.facultativo');
Route::post('register/domestico', [AuthMercurioController::class, 'registerDomesticoAction'])->name('api.register.domestico');

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
