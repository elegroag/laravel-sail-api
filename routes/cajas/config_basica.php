<?php

use App\Http\Controllers\Cajas\Mercurio01Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio01')->group(function () {
        Route::get('/index', [Mercurio01Controller::class, 'index']);
        Route::post('/editar', [Mercurio01Controller::class, 'editar']);
        Route::post('/buscar', [Mercurio01Controller::class, 'buscar']);
        Route::post('/guardar', [Mercurio01Controller::class, 'guardar']);
        Route::post('/aplicar_filtro', [Mercurio01Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio01Controller::class, 'changeCantidadPagina']);
        Route::post('/borrar_filtro', [Mercurio01Controller::class, 'borrarFiltro']);
    });
});
