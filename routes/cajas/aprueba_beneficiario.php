<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacion-beneficiario')->group(function () {
        Route::get('/', [ApruebaBeneficiarioController::class, 'indexAction'])->name('aprobacion.beneficiario.index');
        Route::get('/buscar/{estado?}', [ApruebaBeneficiarioController::class, 'buscarAction'])->name('aprobacion.beneficiario.buscar');
        Route::post('/filtrar', [ApruebaBeneficiarioController::class, 'aplicarFiltroAction'])->name('aprobacion.beneficiario.filtrar');
        Route::post('/aprobar', [ApruebaBeneficiarioController::class, 'apruebaAction'])->name('aprobacion.beneficiario.aprobar');
        Route::post('/devolver', [ApruebaBeneficiarioController::class, 'devolverAction'])->name('aprobacion.beneficiario.devolver');
        Route::post('/rechazar', [ApruebaBeneficiarioController::class, 'rechazarAction'])->name('aprobacion.beneficiario.rechazar');
        Route::post('/borrar-filtro', [ApruebaBeneficiarioController::class, 'borrarFiltroAction'])->name('aprobacion.beneficiario.borrar-filtro');
        Route::get('/info/{id}', [ApruebaBeneficiarioController::class, 'infoAprobadoViewAction'])->name('aprobacion.beneficiario.info');
        Route::post('/deshacer', [ApruebaBeneficiarioController::class, 'deshacerAction'])->name('aprobacion.beneficiario.deshacer');
    });
});
