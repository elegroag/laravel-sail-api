<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio03Controller;

Route::prefix('/cajas/mercurio03')->group(function () {
    Route::get('/index', [Mercurio03Controller::class, 'indexAction']);
    Route::post('/buscar', [Mercurio03Controller::class, 'buscarAction']);
    Route::post('/editar/{codfir?}', [Mercurio03Controller::class, 'editarAction']);
    Route::post('/borrar', [Mercurio03Controller::class, 'borrarAction']);
    Route::post('/guardar', [Mercurio03Controller::class, 'guardarAction']);
    Route::post('/valide-pk', [Mercurio03Controller::class, 'validePkAction']);
    Route::get('/reporte/{format?}', [Mercurio03Controller::class, 'reporteAction']);

    Route::post('/aplicar_filtro', [Mercurio03Controller::class, 'aplicarFiltroAction']);
    Route::post('/change_cantidad_pagina', [Mercurio03Controller::class, 'changeCantidadPagina']);
});
