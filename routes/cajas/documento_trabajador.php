<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio13Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio13')->group(function () {

        Route::get('/index', [Mercurio13Controller::class, 'indexAction']);
        Route::post('/infor/{tipopc?}/{coddoc?}', [Mercurio13Controller::class, 'inforAction']);
        Route::post('/guardar', [Mercurio13Controller::class, 'guardarAction']);
        Route::post('/borrar', [Mercurio13Controller::class, 'borrarAction']);

        Route::post('/aplicar_filtro', [Mercurio13Controller::class, 'aplicarFiltroAction']);
        Route::post('/valide-pk', [Mercurio13Controller::class, 'validePkAction']);
        Route::post('/buscar', [Mercurio13Controller::class, 'buscarAction']);
        Route::post('/editar/{tipo?}', [Mercurio13Controller::class, 'editarAction']);
        Route::post('/change_cantidad_pagina', [Mercurio13Controller::class, 'changeCantidadPaginaAction']);
    });
});
