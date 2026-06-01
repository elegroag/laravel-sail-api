<?php

use App\Http\Controllers\Mercurio\EcommerceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::prefix('/mercurio/servicios')->group(function () {
        Route::get('/index', [EcommerceController::class, 'index'])->name('servicios.index');
        Route::get('/ver-compras', [EcommerceController::class, 'verCompras'])->name('servicios.ver-compras');
        Route::post('/identificar-trabajador', [EcommerceController::class, 'identificarTrabajador']);
        Route::post('/listar-servicios', [EcommerceController::class, 'listarServicios']);
        Route::post('/validar-tarifa', [EcommerceController::class, 'validarTarifa']);
        Route::post('/validar-pago-epayco', [EcommerceController::class, 'validarPagoEpayco']);
        Route::post('/guardar-venta', [EcommerceController::class, 'guardarVenta']);
        Route::post('/mis-compras', [EcommerceController::class, 'misCompras']);
    });
});