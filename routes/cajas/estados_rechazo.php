<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio11Controller;

Route::prefix('/cajas/mercurio11')->group(function () {
    Route::get('/index', [Mercurio11Controller::class, 'indexAction']);
    Route::post('/buscar', [Mercurio11Controller::class, 'buscarAction']);
    Route::post('/editar', [Mercurio11Controller::class, 'editarAction']);
    Route::post('/borrar', [Mercurio11Controller::class, 'borrarAction']);
    Route::post('/guardar', [Mercurio11Controller::class, 'guardarAction']);
    Route::post('/valide-pk', [Mercurio11Controller::class, 'validePkAction']);
    Route::get('/reporte/{format?}', [Mercurio11Controller::class, 'reporteAction']);

    Route::post('/aplicar_filtro', [Mercurio11Controller::class, 'aplicarFiltroAction']);
    Route::post('/change_cantidad_pagina', [Mercurio11Controller::class, 'changeCantidadPagina']);
    Route::post('/borrar_filtro', [Mercurio11Controller::class, 'borrarFiltroAction']);
});
