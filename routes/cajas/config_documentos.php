<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio12Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio12')->group(function () {
        Route::get('/index', [Mercurio12Controller::class, 'indexAction']);
        Route::post('/buscar', [Mercurio12Controller::class, 'buscarAction']);
        Route::post('/editar/{coddoc?}', [Mercurio12Controller::class, 'editarAction']);
        Route::post('/borrar', [Mercurio12Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio12Controller::class, 'guardarAction']);
        Route::post('/valide-pk', [Mercurio12Controller::class, 'validePkAction']);
        Route::post('/reporte/{format?}', [Mercurio12Controller::class, 'reporteAction']);

        Route::post('/aplicar_filtro', [Mercurio12Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio12Controller::class, 'changeCantidadPaginaAction']);
    });
});
