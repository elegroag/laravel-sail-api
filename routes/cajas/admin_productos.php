<?php

use App\Http\Controllers\Cajas\AdmproductosController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Conyuge (migrado desde Kumbia)
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/admproductos')->group(function () {
        Route::get('/lista', [AdmproductosController::class, 'listaAction'])->name('admproductos.lista');
        Route::post('/buscar_lista', [AdmproductosController::class, 'buscarListaAction']);
        Route::post('/nuevo', [AdmproductosController::class, 'nuevoAction'])->name('admproductos.nuevo');
        Route::post('/guardar/{id?}', [AdmproductosController::class, 'guardarAction']);
        Route::post('/editar/{id}', [AdmproductosController::class, 'editarAction'])->name('admproductos.editar');
        Route::post('/cambio_estado', [AdmproductosController::class, 'changeEstadoAction']);
        Route::get('/aplicados/{codser}', [AdmproductosController::class, 'aplicadosAction']);
        Route::post('/buscar_afiliados-aplicados/{codser}', [AdmproductosController::class, 'buscarAfiliadosAplicadosAction']);
        Route::get('/cargue_pagos/{codser}', [AdmproductosController::class, 'cargue_pagosAction'])->name('admproductos.cargue_pagos');
        Route::post('/detalle-aplicado/{id}', [AdmproductosController::class, 'detalleAplicadoAction']);
        Route::post('/rechazar/{id}', [AdmproductosController::class, 'rechazarAction']);
    });
});
