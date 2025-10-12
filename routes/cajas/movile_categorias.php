<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio51Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio51')->group(function () {
        Route::get('/index', [Mercurio51Controller::class, 'indexAction']);
        Route::post('/aplicar_filtro', [Mercurio51Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio51Controller::class, 'changeCantidadPaginaAction']);

        Route::get('/buscar', [Mercurio51Controller::class, 'buscarAction']);
        Route::get('/editar', [Mercurio51Controller::class, 'editarAction']);
        Route::delete('/borrar', [Mercurio51Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio51Controller::class, 'guardarAction']);
        Route::post('/valide-pk', [Mercurio51Controller::class, 'validePkAction']);
        Route::get('/reporte/{format?}', [Mercurio51Controller::class, 'reporteAction']);
    });
});
