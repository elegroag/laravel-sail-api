<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio06Controller;

Route::prefix('/cajas/mercurio06')->group(function () {
    Route::get('/index', [Mercurio06Controller::class, 'indexAction']);
    Route::post('/buscar', [Mercurio06Controller::class, 'buscarAction']);
    Route::get('/editar/{tipo?}', [Mercurio06Controller::class, 'editarAction']);
    Route::post('/borrar', [Mercurio06Controller::class, 'borrarAction']);
    Route::post('/guardar', [Mercurio06Controller::class, 'guardarAction']);
    Route::post('/valide-pk', [Mercurio06Controller::class, 'validePkAction']);
    Route::post('/valide-pk-campo', [Mercurio06Controller::class, 'validePkCampoAction']);
    Route::get('/campo-view/{tipo?}', [Mercurio06Controller::class, 'campo_viewAction']);
    Route::post('/guardar-campo', [Mercurio06Controller::class, 'guardarCampoAction']);
    Route::get('/editar-campo', [Mercurio06Controller::class, 'editarCampoAction']);
    Route::post('/borrar-campo', [Mercurio06Controller::class, 'borrarCampoAction']);
    Route::get('/reporte/{format?}', [Mercurio06Controller::class, 'reporteAction']);

    Route::post('/aplicar_filtro', [Mercurio06Controller::class, 'aplicarFiltroAction']);
    Route::post('/change_cantidad_pagina', [Mercurio06Controller::class, 'changeCantidadPaginaAction']);
});
