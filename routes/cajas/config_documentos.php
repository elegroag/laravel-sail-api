<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio12Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio12')->group(function () {
        Route::get('/index', [Mercurio12Controller::class, 'index']);
        Route::post('/buscar', [Mercurio12Controller::class, 'buscar']);
        Route::post('/editar/{coddoc?}', [Mercurio12Controller::class, 'editar']);
        Route::post('/borrar', [Mercurio12Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio12Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio12Controller::class, 'validePk']);
        Route::post('/reporte/{format?}', [Mercurio12Controller::class, 'reporte']);

        Route::post('/aplicar_filtro', [Mercurio12Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio12Controller::class, 'changeCantidadPagina']);
    });
});
