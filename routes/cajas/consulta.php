<?php

// Importar facades y controlador necesarios
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\ConsultaController;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/consulta')->group(function () {
        Route::get('/masivas', [ConsultaController::class, 'index']);
        Route::get('/carga_laboral', [ConsultaController::class, 'cargaLaboral'])->name('consulta.cargaLaboral');
        Route::post('/reporte_excel_carga_laboral', [ConsultaController::class, 'reporteExcelCargaLaboral']);
        Route::post('/reporte_excel_indicadores', [ConsultaController::class, 'reporteExcelIndicadores']);
        Route::get('/indicadores', [ConsultaController::class, 'indicadores']);
        Route::post('/consulta_indicadores', [ConsultaController::class, 'consultaIndicadores']);
        Route::get('/activacion_masiva', [ConsultaController::class, 'consultaActivacionMasivaView']);
        Route::post('/activacion_masiva', [ConsultaController::class, 'consultaActivacionMasiva'])->name('consulta.activacion_masiva');
    });
});
