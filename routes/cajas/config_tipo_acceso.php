<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio06Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio06')->group(function () {
        Route::get('/index', [Mercurio06Controller::class, 'index']);
        Route::post('/buscar', [Mercurio06Controller::class, 'buscar']);
        Route::post('/editar/{tipo?}', [Mercurio06Controller::class, 'editar']);

        Route::post('/borrar', [Mercurio06Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio06Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio06Controller::class, 'validePk']);
        Route::post('/valide-pk-campo', [Mercurio06Controller::class, 'validePkCampo']);
        Route::post('/campo_view', [Mercurio06Controller::class, 'campoView']);
        Route::post('/guardar_campo', [Mercurio06Controller::class, 'guardarCampo']);
        Route::post('/editar_campo', [Mercurio06Controller::class, 'editarCampo']);
        Route::post('/borrar_campo', [Mercurio06Controller::class, 'borrarCampo']);
        Route::post('/reporte/{format?}', [Mercurio06Controller::class, 'reporte']);

        Route::post('/aplicar_filtro', [Mercurio06Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio06Controller::class, 'changeCantidadPagina']);
    });
});
