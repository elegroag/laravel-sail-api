<?php

// Importar facades y controlador necesarios
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\AuditoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/auditoria')->group(function () {
        Route::get('/index', [AuditoriaController::class, 'index']);
        Route::post('/consulta', [AuditoriaController::class, 'consultaAuditoria']);
        Route::post('/reporte', [AuditoriaController::class, 'reporteAuditoria']);
        Route::post('/infor', [AuditoriaController::class, 'infor']);
    });
});
