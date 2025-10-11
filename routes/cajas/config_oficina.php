<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio04Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio04')->group(function () {
        Route::get('/index', [Mercurio04Controller::class, 'indexAction']);
        Route::post('/buscar', [Mercurio04Controller::class, 'buscarAction']);
        Route::post('/editar/{codofi?}', [Mercurio04Controller::class, 'editarAction']);
        Route::post('/borrar', [Mercurio04Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio04Controller::class, 'guardarAction']);
        Route::post('/valide_pk', [Mercurio04Controller::class, 'validePkAction']);
        Route::post('/reporte/{format?}', [Mercurio04Controller::class, 'reporteAction']);
        Route::post('/valide_pk_ciudad', [Mercurio04Controller::class, 'validePkCiudadAction']);
        Route::post('/ciudad_view', [Mercurio04Controller::class, 'ciudadViewAction']);
        Route::post('/guardar_ciudad', [Mercurio04Controller::class, 'guardarCiudadAction']);
        Route::post('/editar_ciudad', [Mercurio04Controller::class, 'editarCiudadAction']);
        Route::post('/borrar_ciudad', [Mercurio04Controller::class, 'borrarCiudadAction']);
        Route::post('/valide_pk_opcion', [Mercurio04Controller::class, 'validePkOpcionAction']);
        Route::post('/opcion_view', [Mercurio04Controller::class, 'opcionViewAction']);
        Route::post('/guardar_opcion', [Mercurio04Controller::class, 'guardarOpcionAction']);
        Route::post('/borrar_opcion', [Mercurio04Controller::class, 'borrarOpcionAction']);

        Route::post('/aplicar_filtro', [Mercurio04Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio04Controller::class, 'changeCantidadPaginaAction']);
    });
});
