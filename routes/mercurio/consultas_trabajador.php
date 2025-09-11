<?php

use App\Http\Controllers\Mercurio\ConsultasTrabajadorController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

# Subsidio consultas de trabajadores
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('mercurio/subsidio')->group(function () {
        Route::get('consulta_giro_view', [ConsultasTrabajadorController::class, 'consultaGiroViewAction']);
        Route::post('consulta_giro', [ConsultasTrabajadorController::class, 'consultaGiroAction']);
        Route::get('consulta_no_giro_view', [ConsultasTrabajadorController::class, 'consultaNoGiroViewAction']);
        Route::post('consulta_no_giro', [ConsultasTrabajadorController::class, 'consultaNoGiroAction']);
        Route::get('consulta_planilla_trabajador_view', [ConsultasTrabajadorController::class, 'consultaPlanillaTrabajadorViewAction']);
        Route::post('consulta_planilla_trabajador', [ConsultasTrabajadorController::class, 'consultaPlanillaTrabajadorAction']);
        Route::post('consulta_tarjeta', [ConsultasTrabajadorController::class, 'consultaTarjetaAction']);
        Route::get('certificado_afiliacion_view', [ConsultasTrabajadorController::class, 'certificadoAfiliacionViewAction']);
        Route::post('certificado_afiliacion', [ConsultasTrabajadorController::class, 'certificadoAfiliacionAction']);
        Route::get('consulta_nucleo_view', [ConsultasTrabajadorController::class, 'consultaNucleoViewAction']);
        Route::post('consulta_nucleo', [ConsultasTrabajadorController::class, 'consultaNucleoAction']);
        Route::get('historial', [ConsultasTrabajadorController::class, 'historialAction'])->name('subsidio.historial');
    });
});
