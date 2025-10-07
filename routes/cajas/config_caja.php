<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio02Controller;

Route::prefix('/cajas')->group(function () {
    Route::get('/mercurio02/index', [Mercurio02Controller::class, 'indexAction']);
    Route::post('/mercurio02/buscar', [Mercurio02Controller::class, 'buscarAction']);

    Route::post('/mercurio02/editar', [Mercurio02Controller::class, 'editarAction']);
    Route::post('/mercurio02/guardar', [Mercurio02Controller::class, 'guardarAction']);
    Route::get('/mercurio02/configuracion', [Mercurio02Controller::class, 'getConfiguracionAction']);
    Route::post('/mercurio02/configuracion', [Mercurio02Controller::class, 'updateConfiguracionAction']);
    Route::get('/mercurio02/datos', [Mercurio02Controller::class, 'getDatosAction']);
    Route::post('/mercurio02/datos', [Mercurio02Controller::class, 'updateDatosAction']);
    Route::post('/mercurio02/aplicar_filtro', [Mercurio02Controller::class, 'aplicarFiltroAction']);
    Route::post('/mercurio02/change_cantidad_pagina', [Mercurio02Controller::class, 'changeCantidadPagina']);
});
