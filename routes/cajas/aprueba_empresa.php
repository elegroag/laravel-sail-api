<?php

use App\Http\Controllers\Cajas\ApruebaEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionemp')->group(function () {
        Route::get('/index', [ApruebaEmpresaController::class, 'indexAction']);
        Route::get('/listar', [ApruebaEmpresaController::class, 'listarAction']);
        Route::post('/buscar', [ApruebaEmpresaController::class, 'buscarAction']);
        Route::post('/devolver', [ApruebaEmpresaController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaEmpresaController::class, 'rechazarAction']);
        Route::get('/opcional', [ApruebaEmpresaController::class, 'opcionalAction']);
        Route::get('/info/{id}', [ApruebaEmpresaController::class, 'inforAction']);
        Route::get('/editar/{id}', [ApruebaEmpresaController::class, 'editarViewAction']);
        Route::post('/editar', [ApruebaEmpresaController::class, 'edita_empresaAction']);

        Route::post('/aplicar_filtro/{estado}', [ApruebaEmpresaController::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina/{estado}', [ApruebaEmpresaController::class, 'changeCantidadPagina']);
    });
});
