<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio01Controller;

Route::prefix('/cajas')->group(function () {
    Route::get('/mercurio01/index', [Mercurio01Controller::class, 'indexAction']);

    Route::post('/mercurio01/editar', [Mercurio01Controller::class, 'editarAction']);
    Route::post('/mercurio01/buscar', [Mercurio01Controller::class, 'buscarAction']);
    Route::post('/mercurio01/guardar', [Mercurio01Controller::class, 'guardarAction']);
    Route::post('/mercurio01/aplicar_filtro', [Mercurio01Controller::class, 'aplicarFiltroAction']);
    Route::post('/mercurio01/change_cantidad_pagina', [Mercurio01Controller::class, 'changeCantidadPagina']);
});
