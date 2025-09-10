<?php

use App\Http\Controllers\Cajas\ApruebaCertificadoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('aprobacioncer')->group(function () {
        Route::get('/', [ApruebaCertificadoController::class, 'indexAction']);
        Route::post('aplicar-filtro', [ApruebaCertificadoController::class, 'aplicarFiltroAction']);
        Route::post('buscar', [ApruebaCertificadoController::class, 'buscarAction']);
        Route::post('info', [ApruebaCertificadoController::class, 'inforAction']);
        Route::post('aprobar', [ApruebaCertificadoController::class, 'apruebaAction']);
        Route::post('devolver', [ApruebaCertificadoController::class, 'devolverAction']);
        Route::post('rechazar', [ApruebaCertificadoController::class, 'rechazarAction']);
        Route::post('borrar-filtro', [ApruebaCertificadoController::class, 'borrarFiltroAction']);
    });
});
