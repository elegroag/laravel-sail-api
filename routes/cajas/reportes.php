<?php

use App\Http\Controllers\Cajas\ConsultaDocumentoSolicitudController;
use App\Http\Controllers\Cajas\InformeSolicitudController;
use App\Http\Controllers\Cajas\ReporteOportunidadAfiliacionController;
use App\Http\Controllers\Cajas\ReportesolController;
use App\Http\Controllers\Cajas\ReporteSolicitudesEmpresaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/reporte-oportunidad')->group(function () {
        Route::get('/index', [ReporteOportunidadAfiliacionController::class, 'index'])
            ->name('cajas.reporte-oportunidad.index');

        Route::get('/previsualizar', [ReporteOportunidadAfiliacionController::class, 'previsualizar'])
            ->name('cajas.reporte-oportunidad.previsualizar');

        Route::post('/exportar', [ReporteOportunidadAfiliacionController::class, 'exportar'])
            ->name('cajas.reporte-oportunidad.exportar');
    });

    Route::prefix('/cajas/informe-solicitud')->group(function () {
        Route::get('/', [InformeSolicitudController::class, 'index'])
            ->name('cajas.informe-solicitud.index');

        Route::match(['get', 'post'], '/pdf', [InformeSolicitudController::class, 'pdf'])
            ->name('cajas.informe-solicitud.pdf');
    });

    Route::prefix('/cajas/reporte-solicitudes-empresa')->group(function () {
        Route::get('/index', [ReporteSolicitudesEmpresaController::class, 'index'])
            ->name('cajas.reporte-solicitudes-empresa.index');

        Route::post('/consultar', [ReporteSolicitudesEmpresaController::class, 'consultar'])
            ->name('cajas.reporte-solicitudes-empresa.consultar');
    });

    Route::prefix('/cajas/consulta-documento-solicitud')->group(function () {
        Route::get('/index', [ConsultaDocumentoSolicitudController::class, 'index'])
            ->name('cajas.consulta-documento-solicitud.index');

        Route::post('/consultar', [ConsultaDocumentoSolicitudController::class, 'consultar'])
            ->name('cajas.consulta-documento-solicitud.consultar');
    });

    Route::get('/cajas/reportesol/index', [ReportesolController::class, 'index'])
        ->name('cajas.reportesol.index');

    Route::post('/cajas/reportesol/procesar', [ReportesolController::class, 'procesar'])
        ->name('cajas.reportesol.procesar');
});
