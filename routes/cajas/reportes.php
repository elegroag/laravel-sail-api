<?php

use App\Http\Controllers\Cajas\ReporteOportunidadAfiliacionController;
use App\Http\Controllers\Cajas\ReportesolController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/reporte-oportunidad')->group(function () {
        Route::get('/index', [ReporteOportunidadAfiliacionController::class, 'index'])
            ->name('cajas.reporte-oportunidad.index');

        Route::post('/por-aportante', [ReporteOportunidadAfiliacionController::class, 'exportarPorAportante'])
            ->name('cajas.reporte-oportunidad.por-aportante');

        Route::post('/por-trabajador', [ReporteOportunidadAfiliacionController::class, 'exportarPorTrabajador'])
            ->name('cajas.reporte-oportunidad.por-trabajador');
    });

    Route::get('/cajas/reportesol/index', [ReportesolController::class, 'index'])
        ->name('cajas.reportesol.index');

    Route::post('/cajas/reportesol/procesar', [ReportesolController::class, 'procesar'])
        ->name('cajas.reportesol.procesar');
});
