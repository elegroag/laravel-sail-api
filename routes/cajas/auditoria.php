<?php

// Importar facades y controlador necesarios
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\AuditoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/auditoria')->group(function () {
        Route::get('/index', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::post('/consulta', [AuditoriaController::class, 'consultaAuditoria'])->name('auditoria.consulta');
        Route::post('/reporte', [AuditoriaController::class, 'reporteAuditoria'])->name('auditoria.reporte');
        Route::post('/infor', [AuditoriaController::class, 'infor'])->name('auditoria.infor');
    });
});
