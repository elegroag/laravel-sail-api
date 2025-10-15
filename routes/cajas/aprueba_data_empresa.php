<?php

use App\Http\Controllers\Cajas\ApruebaUpEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/actualizaemp')->group(function () {
        Route::get('/index', [ApruebaUpEmpresaController::class, 'indexAction']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaUpEmpresaController::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [ApruebaUpEmpresaController::class, 'changeCantidadPaginaAction']);
        Route::get('/opcional/{estado?}', [ApruebaUpEmpresaController::class, 'opcionalAction']);
        Route::post('/buscar/{estado?}', [ApruebaUpEmpresaController::class, 'buscarAction']);
        Route::post('/devolver', [ApruebaUpEmpresaController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaUpEmpresaController::class, 'rechazarAction']);
        Route::post('/aprueba', [ApruebaUpEmpresaController::class, 'apruebaAction']);
        Route::post('/borrar_filtro', [ApruebaUpEmpresaController::class, 'borrarFiltroAction']);
        Route::post('/infor', [ApruebaUpEmpresaController::class, 'inforAction']);
    });
});
