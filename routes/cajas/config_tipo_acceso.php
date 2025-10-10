<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio06Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio06')->group(function () {
        Route::get('/index', [Mercurio06Controller::class, 'indexAction']);
        Route::post('/buscar', [Mercurio06Controller::class, 'buscarAction']);
        Route::post('/editar/{tipo?}', [Mercurio06Controller::class, 'editarAction']);

        Route::post('/borrar', [Mercurio06Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio06Controller::class, 'guardarAction']);
        Route::post('/valide-pk', [Mercurio06Controller::class, 'validePkAction']);
        Route::post('/valide-pk-campo', [Mercurio06Controller::class, 'validePkCampoAction']);
        Route::post('/campo_view', [Mercurio06Controller::class, 'campoViewAction']);
        Route::post('/guardar_campo', [Mercurio06Controller::class, 'guardarCampoAction']);
        Route::post('/editar_campo', [Mercurio06Controller::class, 'editarCampoAction']);
        Route::post('/borrar_campo', [Mercurio06Controller::class, 'borrarCampoAction']);
        Route::post('/reporte/{format?}', [Mercurio06Controller::class, 'reporteAction']);

        Route::post('/aplicar_filtro', [Mercurio06Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio06Controller::class, 'changeCantidadPaginaAction']);
    });
});
