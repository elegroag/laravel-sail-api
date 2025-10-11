<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio09Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio09')->group(function () {
        Route::get('/index', [Mercurio09Controller::class, 'indexAction']);
        Route::post('/buscar', [Mercurio09Controller::class, 'buscarAction']);
        Route::post('/editar/{tipopc?}', [Mercurio09Controller::class, 'editarAction']);
        Route::post('/borrar', [Mercurio09Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio09Controller::class, 'guardarAction']);
        Route::post('/valide-pk', [Mercurio09Controller::class, 'validePkAction']);
        Route::post('/archivos_view/{tipopc?}', [Mercurio09Controller::class, 'archivosViewAction']);
        Route::post('/guardar_archivos', [Mercurio09Controller::class, 'guardarArchivosAction']);
        Route::post('/obliga_archivos', [Mercurio09Controller::class, 'obligaArchivosAction']);
        Route::post('/archivos_empresa_view', [Mercurio09Controller::class, 'archivosEmpresaViewAction']);
        Route::post('/guardar_empresa_archivos', [Mercurio09Controller::class, 'guardarEmpresaArchivosAction']);
        Route::post('/obliga_empresa_archivos', [Mercurio09Controller::class, 'obligaEmpresaArchivosAction']);
        Route::post('/reporte/{format?}', [Mercurio09Controller::class, 'reporteAction']);
        Route::post('/aplicar_filtro', [Mercurio09Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio09Controller::class, 'changeCantidadPaginaAction']);
    });
});
