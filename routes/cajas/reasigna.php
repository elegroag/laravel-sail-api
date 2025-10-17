<?php

// Importar facades y controlador necesarios
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\ReasignaController;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/reasigna')->group(function () {
        Route::get('/index', [ReasignaController::class, 'index']);
        Route::post('/traer_datos', [ReasignaController::class, 'traerDatos']);
        Route::post('/proceso_reasignar_masivo', [ReasignaController::class, 'procesoReasignarMasivo']);
        Route::post('/infor', [ReasignaController::class, 'infor']);
        Route::post('/cambiar_usuario', [ReasignaController::class, 'cambiarUsuario']);
    });
});
