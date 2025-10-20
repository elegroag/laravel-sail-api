<?php

use App\Http\Controllers\Cajas\ApruebaUpEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/actualizaemp')->group(function () {
        Route::get('/index', [ApruebaUpEmpresaController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaUpEmpresaController::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [ApruebaUpEmpresaController::class, 'changeCantidadPagina']);
        Route::get('/opcional/{estado?}', [ApruebaUpEmpresaController::class, 'opcional']);
        Route::post('/buscar/{estado?}', [ApruebaUpEmpresaController::class, 'buscar']);
        Route::post('/devolver', [ApruebaUpEmpresaController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaUpEmpresaController::class, 'rechazar']);
        Route::post('/aprueba', [ApruebaUpEmpresaController::class, 'aprueba']);
        Route::post('/borrar_filtro', [ApruebaUpEmpresaController::class, 'borrarFiltro']);
        Route::post('/infor', [ApruebaUpEmpresaController::class, 'infor']);
    });
});
