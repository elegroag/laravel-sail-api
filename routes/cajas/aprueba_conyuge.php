<?php

use App\Http\Controllers\Cajas\ApruebaConyugeController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacioncon')->group(function () {

        Route::get('/', function () {
            return redirect('cajas/aprobacioncon/index');
        });

        Route::get('/index', [ApruebaConyugeController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaConyugeController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaConyugeController::class, 'buscar']);
        Route::post('/aprueba', [ApruebaConyugeController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaConyugeController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaConyugeController::class, 'rechazar']);
        Route::post('/infor', [ApruebaConyugeController::class, 'infor']);
        Route::post('/valida_conyuge', [ApruebaConyugeController::class, 'validaConyuge']);
    });
});
