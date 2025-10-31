<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\UsuarioController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/usuario')->group(function () {
        Route::get('/index', [UsuarioController::class, 'index']);
        Route::post('/aplicar_filtro/{tipo?}', [UsuarioController::class, 'aplicarFiltro']);
        Route::post('/buscar/{tipo?}', [UsuarioController::class, 'buscar']);
        Route::post('/change_cantidad_pagina/{tipo?}', [UsuarioController::class, 'changeCantidadPagina']);
        Route::post('/show_user', [UsuarioController::class, 'showUser']);
        Route::post('/params', [UsuarioController::class, 'params']);
        Route::post('/borrar_usuario', [UsuarioController::class, 'borrarUsuario']);
        Route::post('/guardar', [UsuarioController::class, 'guardar']);
    });
});
