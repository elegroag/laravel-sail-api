<?php

use App\Http\Controllers\Cajas\Mercurio02Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio02')->group(function () {
        Route::get('/index', [Mercurio02Controller::class, 'index']);
        Route::post('/buscar', [Mercurio02Controller::class, 'buscar']);
        Route::post('/editar', [Mercurio02Controller::class, 'editar']);
        Route::post('/guardar', [Mercurio02Controller::class, 'guardar']);
        Route::get('/configuracion', [Mercurio02Controller::class, 'getConfiguracion']);
        Route::post('/configuracion', [Mercurio02Controller::class, 'updateConfiguracion']);
        Route::get('/datos', [Mercurio02Controller::class, 'getDatos']);
        Route::post('/datos', [Mercurio02Controller::class, 'updateDatos']);
        Route::post('/aplicar_filtro', [Mercurio02Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio02Controller::class, 'changeCantidadPagina']);
    });
});
