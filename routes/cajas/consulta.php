<?php

use App\Http\Controllers\Cajas\ConsultaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/consulta')->group(function () {
        Route::get('/masivas', [ConsultaController::class, 'index']);
        Route::get('/carga_laboral', [ConsultaController::class, 'cargaLaboral'])->name('consulta.cargaLaboral');
        Route::post('/solicitudes_carga_laboral', [ConsultaController::class, 'solicitudesCargaLaboral'])->name('consulta.solicitudesCargaLaboral');
        Route::post('/reporte_excel_carga_laboral', [ConsultaController::class, 'reporteExcelCargaLaboral'])->name('consulta.reporteExcelCargaLaboral');
        Route::get('/indicadores', [ConsultaController::class, 'indicadores'])->name('consulta.indicadores');
        Route::post('/consulta_indicadores', [ConsultaController::class, 'consultaIndicadores'])->name('consulta.consultaIndicadores');
        Route::post('/reporte_excel_indicadores', [ConsultaController::class, 'reporteExcelIndicadores'])->name('consulta.reporteExcelIndicadores');
        Route::get('/certificado_trabajador', [ConsultaController::class, 'certificadoTrabajadorView'])->name('consulta.certificadoTrabajador');
        Route::post('/trabajadores_por_nit', [ConsultaController::class, 'trabajadoresPorNit'])->name('consulta.trabajadoresPorNit');
        Route::post('/certificado_trabajador', [ConsultaController::class, 'certificadoTrabajador'])->name('consulta.generarCertificadoTrabajador');
    });
});
