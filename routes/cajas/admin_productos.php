<?php

use App\Http\Controllers\Cajas\AdmproductosController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Conyuge (migrado desde Kumbia)
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/admproductos')->group(function () {
        Route::get('/lista', [AdmproductosController::class, 'lista'])->name('admproductos.lista');
        Route::post('/buscar_lista', [AdmproductosController::class, 'buscarLista']);
        Route::post('/nuevo', [AdmproductosController::class, 'nuevo']);
        Route::post('/guardar/{id?}', [AdmproductosController::class, 'guardar']);
        Route::post('/editar/{id}', [AdmproductosController::class, 'editar'])->name('admproductos.editar');
        Route::post('/cambio_estado', [AdmproductosController::class, 'changeEstado']);
        Route::get('/aplicados/{codser}', [AdmproductosController::class, 'aplicados']);
        Route::post('/buscar_afiliados-aplicados/{codser}', [AdmproductosController::class, 'buscarAfiliadosAplicados']);
        Route::get('/cargue_pagos/{codser}', [AdmproductosController::class, 'carguePagos'])->name('admproductos.cargue_pagos');
        Route::post('/detalle-aplicado/{id}', [AdmproductosController::class, 'detalleAplicado']);
        Route::post('/rechazar/{id}', [AdmproductosController::class, 'rechazar']);
    });
});
