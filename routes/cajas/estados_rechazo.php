<?php

use App\Http\Controllers\Cajas\Mercurio11Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio11')->group(function () {
        Route::get('/index', [Mercurio11Controller::class, 'index']);
        Route::post('/buscar', [Mercurio11Controller::class, 'buscar']);
        Route::post('/editar', [Mercurio11Controller::class, 'editar']);
        Route::post('/borrar', [Mercurio11Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio11Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio11Controller::class, 'validePk']);
        Route::get('/reporte/{format?}', [Mercurio11Controller::class, 'reporte']);

        Route::post('/aplicar_filtro', [Mercurio11Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio11Controller::class, 'changeCantidadPagina']);
        Route::post('/borrar_filtro', [Mercurio11Controller::class, 'borrarFiltro']);
    });
});
