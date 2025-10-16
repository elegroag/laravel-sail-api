<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio04Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio04')->group(function () {
        Route::get('/index', [Mercurio04Controller::class, 'index']);
        Route::post('/buscar', [Mercurio04Controller::class, 'buscar']);
        Route::post('/editar/{codofi?}', [Mercurio04Controller::class, 'editar']);
        Route::post('/borrar', [Mercurio04Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio04Controller::class, 'guardar']);
        Route::post('/valide_pk', [Mercurio04Controller::class, 'validePk']);
        Route::post('/reporte/{format?}', [Mercurio04Controller::class, 'reporte']);
        Route::post('/valide_pk_ciudad', [Mercurio04Controller::class, 'validePkCiudad']);
        Route::post('/ciudad_view', [Mercurio04Controller::class, 'ciudadView']);
        Route::post('/guardar_ciudad', [Mercurio04Controller::class, 'guardarCiudad']);
        Route::post('/editar_ciudad', [Mercurio04Controller::class, 'editarCiudad']);
        Route::post('/borrar_ciudad', [Mercurio04Controller::class, 'borrarCiudad']);
        Route::post('/valide_pk_opcion', [Mercurio04Controller::class, 'validePkOpcion']);
        Route::post('/opcion_view', [Mercurio04Controller::class, 'opcionView']);
        Route::post('/guardar_opcion', [Mercurio04Controller::class, 'guardarOpcion']);
        Route::post('/borrar_opcion', [Mercurio04Controller::class, 'borrarOpcion']);

        Route::post('/aplicar_filtro', [Mercurio04Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio04Controller::class, 'changeCantidadPagina']);
    });
});
