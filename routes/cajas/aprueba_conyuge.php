<?php

use App\Http\Controllers\Cajas\ApruebaConyugeController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {

    Route::prefix('/cajas/aprobacioncon')->group(function () {
        Route::get('/index', [ApruebaConyugeController::class, 'indexAction'])->name('aprobacion.conyuge.index');
        Route::post('/aplicar_filtro/{estado?}', [ApruebaConyugeController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaConyugeController::class, 'buscarAction']);
        Route::post('/aprobar', [ApruebaConyugeController::class, 'apruebaAction'])->name('aprobacion.conyuge.aprobar');
        Route::post('/devolver', [ApruebaConyugeController::class, 'devolverAction'])->name('aprobacion.conyuge.devolver');
        Route::post('/rechazar', [ApruebaConyugeController::class, 'rechazarAction'])->name('aprobacion.conyuge.rechazar');
    });
});
