<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cajas/usuario')->group(function () {
    Route::get('/index', [UsuarioController::class, 'indexAction']);
    Route::post('/aplicar_filtro/{tipo?}', [UsuarioController::class, 'aplicarFiltroAction']);
    Route::post('/buscar/{tipo?}', [UsuarioController::class, 'buscarAction']);
    Route::post('/change_cantidad_pagina/{tipo?}', [UsuarioController::class, 'changeCantidadPagina']);
    Route::post('/show_user', [UsuarioController::class, 'showUserAction']);
    Route::post('/params', [UsuarioController::class, 'paramsAction']);
    Route::post('/borrar_usuario', [UsuarioController::class, 'borrarUsuarioAction']);
});
