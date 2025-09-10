<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    // Add routes for ApruebaBeneficiarioController
    Route::get('/cajas/aprobacion-beneficiario', [ApruebaBeneficiarioController::class, 'indexAction'])->name('aprobacion.beneficiario.index');
    Route::get('/cajas/aprobacion-beneficiario/buscar/{estado?}', [ApruebaBeneficiarioController::class, 'buscarAction'])->name('aprobacion.beneficiario.buscar');
    Route::post('/cajas/aprobacion-beneficiario/filtrar', [ApruebaBeneficiarioController::class, 'aplicarFiltroAction'])->name('aprobacion.beneficiario.filtrar');
    Route::post('/cajas/aprobacion-beneficiario/aprobar', [ApruebaBeneficiarioController::class, 'apruebaAction'])->name('aprobacion.beneficiario.aprobar');
    Route::post('/cajas/aprobacion-beneficiario/devolver', [ApruebaBeneficiarioController::class, 'devolverAction'])->name('aprobacion.beneficiario.devolver');
    Route::post('/cajas/aprobacion-beneficiario/rechazar', [ApruebaBeneficiarioController::class, 'rechazarAction'])->name('aprobacion.beneficiario.rechazar');
    Route::post('/cajas/aprobacion-beneficiario/borrar-filtro', [ApruebaBeneficiarioController::class, 'borrarFiltroAction'])->name('aprobacion.beneficiario.borrar-filtro');
    Route::get('/cajas/aprobacion-beneficiario/info/{id}', [ApruebaBeneficiarioController::class, 'infoAprobadoViewAction'])->name('aprobacion.beneficiario.info');
    Route::post('/cajas/aprobacion-beneficiario/deshacer', [ApruebaBeneficiarioController::class, 'deshacerAction'])->name('aprobacion.beneficiario.deshacer');
});
