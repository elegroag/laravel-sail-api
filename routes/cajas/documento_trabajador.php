<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio13Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio13')->group(function () {

        Route::get('/index', [Mercurio13Controller::class, 'index']);
        Route::post('/infor/{tipopc?}/{coddoc?}', [Mercurio13Controller::class, 'infor']);
        Route::post('/guardar', [Mercurio13Controller::class, 'guardar']);
        Route::post('/borrar', [Mercurio13Controller::class, 'borrar']);

        Route::post('/aplicar_filtro', [Mercurio13Controller::class, 'aplicarFiltro']);
        Route::post('/valide-pk', [Mercurio13Controller::class, 'validePk']);
        Route::post('/buscar', [Mercurio13Controller::class, 'buscar']);
        Route::post('/editar/{tipo?}', [Mercurio13Controller::class, 'editar']);
        Route::post('/change_cantidad_pagina', [Mercurio13Controller::class, 'changeCantidadPagina']);
    });
});
