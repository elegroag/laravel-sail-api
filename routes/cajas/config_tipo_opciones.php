<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio09Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio09')->group(function () {
        Route::get('/index', [Mercurio09Controller::class, 'index']);
        Route::post('/buscar', [Mercurio09Controller::class, 'buscar']);
        Route::post('/editar/{tipopc?}', [Mercurio09Controller::class, 'editar']);
        Route::post('/borrar', [Mercurio09Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio09Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio09Controller::class, 'validePk']);
        Route::post('/archivos_view/{tipopc?}', [Mercurio09Controller::class, 'archivosView']);
        Route::post('/guardar_archivos', [Mercurio09Controller::class, 'guardarArchivos']);
        Route::post('/obliga_archivos', [Mercurio09Controller::class, 'obligaArchivos']);
        Route::post('/archivos_empresa_view', [Mercurio09Controller::class, 'archivosEmpresaView']);
        Route::post('/guardar_empresa_archivos', [Mercurio09Controller::class, 'guardarEmpresaArchivos']);
        Route::post('/obliga_empresa_archivos', [Mercurio09Controller::class, 'obligaEmpresaArchivos']);
        Route::post('/reporte/{format?}', [Mercurio09Controller::class, 'reporte']);

        Route::post('/aplicar_filtro', [Mercurio09Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio09Controller::class, 'changeCantidadPagina']);
    });
});
