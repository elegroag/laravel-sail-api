<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Controllers\Cajas\ApruebaConyugeController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    // Agregar rutas para ApruebaConyugeController
    Route::get('/aprobacion-conyuge', [ApruebaConyugeController::class, 'indexAction'])->name('aprobacion.conyuge.index');
    Route::get('/aprobacion-conyuge/buscar/{estado?}', [ApruebaConyugeController::class, 'buscarAction'])->name('aprobacion.conyuge.buscar');
    Route::post('/aprobacion-conyuge/filtrar', [ApruebaConyugeController::class, 'aplicarFiltroAction'])->name('aprobacion.conyuge.filtrar');
    Route::post('/aprobacion-conyuge/aprobar', [ApruebaConyugeController::class, 'apruebaAction'])->name('aprobacion.conyuge.aprobar');
    Route::post('/aprobacion-conyuge/devolver', [ApruebaConyugeController::class, 'devolverAction'])->name('aprobacion.conyuge.devolver');
    Route::post('/aprobacion-conyuge/rechazar', [ApruebaConyugeController::class, 'rechazarAction'])->name('aprobacion.conyuge.rechazar');
    Route::resource('/aprobacion-conyuge', ApruebaConyugeController::class, [
        'only' => ['index', 'store', 'update', 'destroy'],
        'names' => [
            'index' => 'aprobacion.conyuge.index',
            'store' => 'aprobacion.conyuge.store',
            'update' => 'aprobacion.conyuge.update',
            'destroy' => 'aprobacion.conyuge.destroy'
        ]
    ]);
});
