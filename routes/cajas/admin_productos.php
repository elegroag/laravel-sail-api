<?php

use App\Http\Controllers\Cajas\AdmproductosController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;


// Conyuge (migrado desde Kumbia)
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    // Lista de productos y servicios
    Route::get('/cajas/admproductos', [AdmproductosController::class, 'listaAction'])->name('admproductos.lista');

    // Obtener lista de servicios con datos de cupos
    Route::get('/cajas/admproductos/lista', [AdmproductosController::class, 'buscarListaAction']);

    // Vista para crear nuevo servicio
    Route::get('/cajas/admproductos/nuevo', [AdmproductosController::class, 'nuevoAction'])->name('admproductos.nuevo');

    // Guardar o actualizar servicio (POST)
    Route::post('/cajas/admproductos/guardar/{id?}', [AdmproductosController::class, 'guardarAction']);

    // Editar servicio (vista)
    Route::get('/cajas/admproductos/editar/{id}', [AdmproductosController::class, 'editarAction'])->name('admproductos.editar');

    // Cambiar estado de un servicio
    Route::post('/cajas/admproductos/cambio-estado', [AdmproductosController::class, 'changeEstadoAction']);

    // Ver aplicados de un servicio
    Route::get('/cajas/admproductos/aplicados/{codser}', [AdmproductosController::class, 'aplicadosAction']);

    // Buscar afiliados aplicados (ajax)
    Route::post('/cajas/admproductos/buscar-afiliados-aplicados/{codser}', [AdmproductosController::class, 'buscarAfiliadosAplicadosAction']);

    // Cargue de pagos para un servicio
    Route::get('/cajas/admproductos/cargue-pagos/{codser}', [AdmproductosController::class, 'cargue_pagosAction']);

    // Detalle de un afiliado aplicado (ajax)
    Route::post('/cajas/admproductos/detalle-aplicado/{id}', [AdmproductosController::class, 'detalleAplicadoAction']);

    // Rechazar afiliado aplicado
    Route::post('/cajas/admproductos/rechazar/{id}', [AdmproductosController::class, 'rechazarAction']);
});
