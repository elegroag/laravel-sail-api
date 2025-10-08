<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\UsuarioController;

Route::prefix('/cajas/usuario')->group(function () {
    Route::get('/index', [UsuarioController::class, 'indexAction']);
    Route::post('/aplicar_filtro/{tipo?}', [UsuarioController::class, 'aplicarFiltroAction']);
    Route::post('/buscar/{tipo?}', [UsuarioController::class, 'buscarAction']);
    Route::post('/change_cantidad_pagina/{tipo?}', [UsuarioController::class, 'changeCantidadPagina']);
    Route::post('/show_user', [UsuarioController::class, 'showUserAction']);
    Route::post('/params', [UsuarioController::class, 'paramsAction']);
    Route::post('/borrar_usuario', [UsuarioController::class, 'borrarUsuarioAction']);
});
