<?php

use App\Http\Controllers\Mercurio\ConsultasTrabajadorController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Subsidio consultas de trabajadores
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('mercurio/subsidio')->group(function () {
        Route::get('/', function () {
            return redirect()->route('trabajador.historial');
        });
        Route::get('historial', [ConsultasTrabajadorController::class, 'historial'])->name('trabajador.historial');
        Route::get('consulta_giro_view', [ConsultasTrabajadorController::class, 'consultaGiroView']);
        Route::post('consulta_giro', [ConsultasTrabajadorController::class, 'consultaGiro']);
        Route::get('consulta_no_giro_view', [ConsultasTrabajadorController::class, 'consultaNoGiroView']);
        Route::post('consulta_no_giro', [ConsultasTrabajadorController::class, 'consultaNoGiro']);
        Route::get('consulta_planilla_trabajador_view', [ConsultasTrabajadorController::class, 'consultaPlanillaTrabajadorView']);
        Route::post('consulta_planilla_trabajador', [ConsultasTrabajadorController::class, 'consultaPlanillaTrabajador']);
        Route::get('certificado_afiliacion', [ConsultasTrabajadorController::class, 'certificadoAfiliacionView']);
        Route::post('certificado_afiliacion', [ConsultasTrabajadorController::class, 'certificadoAfiliacion']);
        Route::get('consulta_nucleo_view', [ConsultasTrabajadorController::class, 'consultaNucleoView']);
        Route::post('consulta_nucleo', [ConsultasTrabajadorController::class, 'consultaNucleo']);
        Route::get('consulta_tarjeta', [ConsultasTrabajadorController::class, 'consultaTarjeta']);

        Route::post('/cambio_email', [MovimientosController::class, 'cambioEmail'])->name('trabajador.cambio_email');
        Route::post('/cambio_clave', [MovimientosController::class, 'cambioClave'])->name('trabajador.cambio_clave');
    });
});
