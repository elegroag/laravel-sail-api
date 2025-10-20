<?php

use App\Http\Controllers\Cajas\ApruebaEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionemp')->group(function () {
        Route::get('/index', [ApruebaEmpresaController::class, 'index']);
        Route::get('/listar', [ApruebaEmpresaController::class, 'listar']);
        Route::post('/buscar', [ApruebaEmpresaController::class, 'buscar']);
        Route::post('/devolver', [ApruebaEmpresaController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaEmpresaController::class, 'rechazar']);
        Route::get('/opcional', [ApruebaEmpresaController::class, 'opcional']);
        Route::post('/infor', [ApruebaEmpresaController::class, 'infor']);
        Route::get('/editar/{id}', [ApruebaEmpresaController::class, 'editarView']);
        Route::post('/editar', [ApruebaEmpresaController::class, 'editaEmpresa']);
        Route::post('/aportes/{id?}', [ApruebaEmpresaController::class, 'aportes']);

        Route::post('/aplicar_filtro/{estado}', [ApruebaEmpresaController::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina/{estado}', [ApruebaEmpresaController::class, 'changeCantidadPagina']);
    });
});
