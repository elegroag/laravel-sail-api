<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio14Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio14')->group(function () {

        Route::get('/index', [Mercurio14Controller::class, 'index']);
        Route::post('/guardar', [Mercurio14Controller::class, 'guardar']);
        Route::post('/borrar', [Mercurio14Controller::class, 'borrar']);
        Route::post('/aplicar_filtro', [Mercurio14Controller::class, 'aplicarFiltro']);
        Route::post('/valide-pk', [Mercurio14Controller::class, 'validePk']);
        Route::post('/buscar', [Mercurio14Controller::class, 'buscar']);
        Route::post('/editar/{tipo?}', [Mercurio14Controller::class, 'editar']);
        Route::post('/change_cantidad_pagina', [Mercurio14Controller::class, 'changeCantidadPagina']);
    });
});
