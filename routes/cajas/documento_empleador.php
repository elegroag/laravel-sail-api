<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio14Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio14')->group(function () {

        Route::get('/index', [Mercurio14Controller::class, 'indexAction']);
        Route::post('/guardar', [Mercurio14Controller::class, 'guardarAction']);
        Route::post('/borrar', [Mercurio14Controller::class, 'borrarAction']);
        Route::post('/aplicar_filtro', [Mercurio14Controller::class, 'aplicarFiltroAction']);
        Route::post('/valide-pk', [Mercurio14Controller::class, 'validePkAction']);
        Route::post('/buscar', [Mercurio14Controller::class, 'buscarAction']);
        Route::post('/editar/{tipo?}', [Mercurio14Controller::class, 'editarAction']);
        Route::post('/change_cantidad_pagina', [Mercurio14Controller::class, 'changeCantidadPaginaAction']);
    });
});
