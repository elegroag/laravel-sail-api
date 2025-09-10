<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\ApruebaComunitariaController;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('aprobacioncom')->group(function () {
        Route::get('/', [ApruebaComunitariaController::class, 'indexAction']);
        Route::post('aplicar-filtro', [ApruebaComunitariaController::class, 'aplicarFiltroAction']);
        Route::post('buscar', [ApruebaComunitariaController::class, 'buscarAction']);
        Route::post('info', [ApruebaComunitariaController::class, 'inforAction']);
        Route::post('aprobar', [ApruebaComunitariaController::class, 'aprobarAction']);
        Route::post('devolver', [ApruebaComunitariaController::class, 'devolverAction']);
        Route::post('rechazar', [ApruebaComunitariaController::class, 'rechazarAction']);
        Route::post('borrar-filtro', [ApruebaComunitariaController::class, 'borrarFiltroAction']);
    });
});
