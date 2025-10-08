<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionben')->group(function () {
        Route::get('/index', [ApruebaBeneficiarioController::class, 'indexAction'])->name('aprobacion.beneficiario.index');
        Route::post('/filtrar', [ApruebaBeneficiarioController::class, 'aplicarFiltroAction'])->name('aprobacion.beneficiario.filtrar');
        Route::post('/aprobar', [ApruebaBeneficiarioController::class, 'apruebaAction'])->name('aprobacion.beneficiario.aprobar');
        Route::post('/devolver', [ApruebaBeneficiarioController::class, 'devolverAction'])->name('aprobacion.beneficiario.devolver');
        Route::post('/rechazar', [ApruebaBeneficiarioController::class, 'rechazarAction'])->name('aprobacion.beneficiario.rechazar');
        Route::post('/borrar_filtro', [ApruebaBeneficiarioController::class, 'borrarFiltroAction'])->name('aprobacion.beneficiario.borrar-filtro');
        Route::get('/info/{id}', [ApruebaBeneficiarioController::class, 'infoAprobadoViewAction'])->name('aprobacion.beneficiario.info');
        Route::post('/deshacer', [ApruebaBeneficiarioController::class, 'deshacerAction'])->name('aprobacion.beneficiario.deshacer');

        Route::post('/aplicar_filtro/{estado?}', [ApruebaBeneficiarioController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaBeneficiarioController::class, 'buscarAction']);
    });
});
