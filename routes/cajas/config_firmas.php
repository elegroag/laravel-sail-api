<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio03Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio03')->group(function () {
        Route::get('/index', [Mercurio03Controller::class, 'index']);
        Route::post('/buscar', [Mercurio03Controller::class, 'buscar']);
        Route::post('/editar/{codfir?}', [Mercurio03Controller::class, 'editar']);
        Route::post('/borrar', [Mercurio03Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio03Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio03Controller::class, 'validePk']);
        Route::get('/reporte/{format?}', [Mercurio03Controller::class, 'reporte']);

        Route::post('/aplicar_filtro', [Mercurio03Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio03Controller::class, 'changeCantidadPagina']);
    });
});
