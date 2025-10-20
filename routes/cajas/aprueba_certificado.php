<?php

use App\Http\Controllers\Cajas\ApruebaCertificadoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/aprobacioncer')->group(function () {
        Route::get('/index', [ApruebaCertificadoController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaCertificadoController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaCertificadoController::class, 'buscar']);
        Route::post('/infor', [ApruebaCertificadoController::class, 'infor']);
        Route::post('/aprobar', [ApruebaCertificadoController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaCertificadoController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaCertificadoController::class, 'rechazar']);
        Route::post('/borrar_filtro', [ApruebaCertificadoController::class, 'borrarFiltro']);
    });
});
