<?php

// Importar facades y controlador necesarios
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\ConsultaController;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/consulta')->group(function () {
        Route::get('/masivas', [ConsultaController::class, 'indexAction']);
        Route::get('/carga_laboral', [ConsultaController::class, 'cargaLaboralAction'])->name('consulta.cargaLaboral');
        Route::post('/reporte_excel_carga_laboral', [ConsultaController::class, 'reporteExcelCargaLaboralAction']);
        Route::post('/reporte_excel_indicadores', [ConsultaController::class, 'reporteExcelIndicadoresAction']);
        Route::get('/indicadores', [ConsultaController::class, 'indicadoresAction']);
        Route::post('/indicadores', [ConsultaController::class, 'consultaIndicadoresAction']);
        Route::get('/activacion_masiva', [ConsultaController::class, 'consultaActivacionMasivaViewAction']);
        Route::post('/activacion_masiva', [ConsultaController::class, 'consultaActivacionMasivaAction'])->name('consulta.activacion_masiva');
    });
});
