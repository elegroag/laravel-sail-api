<?php

use App\Http\Controllers\Mercurio\EcommerceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::prefix('/mercurio/servicios')->group(function () {
        Route::get('/index', [EcommerceController::class, 'index'])->name('servicios.index');
        Route::get('/ver-compras', [EcommerceController::class, 'verCompras'])->name('servicios.ver-compras');
        Route::post('/identificar-trabajador', [EcommerceController::class, 'identificarTrabajador'])->name('servicios.identificar-trabajador');
        Route::post('/listar-servicios', [EcommerceController::class, 'listarServicios'])->name('servicios.listar-servicios');
        Route::post('/validar-tarifa', [EcommerceController::class, 'validarTarifa'])->name('servicios.validar-tarifa');
        Route::post('/validar-pago-epayco', [EcommerceController::class, 'validarPagoEpayco'])->name('servicios.validar-pago-epayco');
        Route::post('/guardar-venta', [EcommerceController::class, 'guardarVenta'])->name('servicios.guardar-venta');
        Route::post('/mis-compras', [EcommerceController::class, 'misCompras'])->name('servicios.mis-compras');
    });
});