<?php

use App\Http\Controllers\Cajas\ApruebaComunitariaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacioncom')->group(function () {
        Route::get('/index', [ApruebaComunitariaController::class, 'index']);
        Route::post('/infor', [ApruebaComunitariaController::class, 'infor']);
        Route::post('/aprueba', [ApruebaComunitariaController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaComunitariaController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaComunitariaController::class, 'rechazar']);
        Route::post('/borrar_filtro', [ApruebaComunitariaController::class, 'borrarFiltro']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaComunitariaController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaComunitariaController::class, 'buscar']);
    });
});
