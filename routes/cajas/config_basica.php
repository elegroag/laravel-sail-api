<?php

use App\Http\Controllers\Cajas\Mercurio01Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio01')->group(function () {
        Route::get('/index', [Mercurio01Controller::class, 'indexAction']);
        Route::post('/editar', [Mercurio01Controller::class, 'editarAction']);
        Route::post('/buscar', [Mercurio01Controller::class, 'buscarAction']);
        Route::post('/guardar', [Mercurio01Controller::class, 'guardarAction']);
        Route::post('/aplicar_filtro', [Mercurio01Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio01Controller::class, 'changeCantidadPagina']);
        Route::post('/borrar_filtro', [Mercurio01Controller::class, 'borrarFiltroAction']);
    });
});
