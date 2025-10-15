<?php

use App\Http\Controllers\Cajas\ApruebaUpTrabajadorController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/actualizatra')->group(function () {
        Route::get('/index', [ApruebaUpTrabajadorController::class, 'indexAction']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaUpTrabajadorController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaUpTrabajadorController::class, 'buscarAction']);
        Route::post('/infor', [ApruebaUpTrabajadorController::class, 'inforAction']);
        Route::post('/aprueba', [ApruebaUpTrabajadorController::class, 'apruebaAction']);
        Route::post('/rechazar', [ApruebaUpTrabajadorController::class, 'rechazarAction']);
    });
});
