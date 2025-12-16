<?php

use App\Http\Controllers\Mercurio\ConsultasEmpresaController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Consultas de empresas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/subsidioemp')->group(function () {
        Route::get('/', function () {
            return redirect()->route('empresa.historial');
        });
        Route::get('/historial', [ConsultasEmpresaController::class, 'historial'])->name('empresa.historial');
        Route::get('/consulta_trabajadores_view', [ConsultasEmpresaController::class, 'consultaTrabajadoresView']);
        Route::get('/consulta_giro_view', [ConsultasEmpresaController::class, 'consultaGiroView']);
        Route::get('/consulta_aportes_view', [ConsultasEmpresaController::class, 'consultaAportesView']);
        Route::get('/consulta_nomina_view', [ConsultasEmpresaController::class, 'consultaNominaView']);
        Route::get('/consulta_mora_presunta', [ConsultasEmpresaController::class, 'consultaMoraPresunta']);
        Route::get('/certificado_afiliacion', [ConsultasEmpresaController::class, 'certificadoAfiliacionView']);
        Route::get('/certificado_para_trabajador', [ConsultasEmpresaController::class, 'certificadoParaTrabajadorView']);

        Route::post('/consulta_nomina', [ConsultasEmpresaController::class, 'consultaNomina']);
        Route::post('/consulta_aportes', [ConsultasEmpresaController::class, 'consultaAportes']);
        Route::post('/consulta_giro', [ConsultasEmpresaController::class, 'consultaGiro']);
        Route::post('/consulta_trabajadores', [ConsultasEmpresaController::class, 'consultaTrabajadores']);
        Route::post('/mora_presunta', [ConsultasEmpresaController::class, 'moraPresunta']);
        Route::post('/certificado_afiliacion', [ConsultasEmpresaController::class, 'certificadoAfiliacion']);
        Route::post('/certificado_para_trabajador', [ConsultasEmpresaController::class, 'certificadoParaTrabajador']);

        Route::post('/cambio_email', [MovimientosController::class, 'cambioEmail'])->name('empresa.cambio_email');
        Route::post('/cambio_clave', [MovimientosController::class, 'cambioClave'])->name('empresa.cambio_clave');
    });
});
