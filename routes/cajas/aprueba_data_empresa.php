<?php

use App\Http\Controllers\Cajas\ApruebaUpEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/actualizardatos')->group(function () {
        Route::get('/index', [ApruebaUpEmpresaController::class, 'indexAction'])->name('aprueba_up_empresa.index');
        Route::post('/aplicar_filtro/{estado?}', [ApruebaUpEmpresaController::class, 'aplicarFiltroAction'])->name('aprueba_up_empresa.aplicarFiltro');
        Route::post('/change_cantidad_pagina', [ApruebaUpEmpresaController::class, 'changeCantidadPaginaAction'])->name('aprueba_up_empresa.changeCantidadPagina');
        Route::get('/opcional/{estado?}', [ApruebaUpEmpresaController::class, 'opcionalAction'])->name('aprueba_up_empresa.opcional');
        Route::post('/buscar/{estado?}', [ApruebaUpEmpresaController::class, 'buscarAction'])->name('aprueba_up_empresa.buscar');
        Route::post('/devolver', [ApruebaUpEmpresaController::class, 'devolverAction'])->name('aprueba_up_empresa.devolver');
        Route::post('/rechazar', [ApruebaUpEmpresaController::class, 'rechazarAction'])->name('aprueba_up_empresa.rechazar');
        Route::post('/aprueba', [ApruebaUpEmpresaController::class, 'apruebaAction'])->name('aprueba_up_empresa.aprueba');
        Route::post('/borrar_filtro', [ApruebaUpEmpresaController::class, 'borrarFiltroAction'])->name('aprueba_up_empresa.borrarFiltro');
        Route::get('/infor/{id}', [ApruebaUpEmpresaController::class, 'inforAction'])->name('aprueba_up_empresa.infor');
    });
});
