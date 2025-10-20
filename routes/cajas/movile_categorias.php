<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio51Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio51')->group(function () {
        Route::get('/index', [Mercurio51Controller::class, 'index']);
        Route::post('/aplicar_filtro', [Mercurio51Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio51Controller::class, 'changeCantidadPagina']);

        Route::get('/buscar', [Mercurio51Controller::class, 'buscar']);
        Route::get('/editar', [Mercurio51Controller::class, 'editar']);
        Route::delete('/borrar', [Mercurio51Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio51Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio51Controller::class, 'validePk']);
        Route::get('/reporte/{format?}', [Mercurio51Controller::class, 'reporte']);
    });
});
