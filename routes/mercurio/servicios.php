<?php

use App\Http\Controllers\Mercurio\EcommerceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::prefix('/mercurio/servicios')->group(function () {
        Route::get('/index', [EcommerceController::class, 'index'])->name('servicios.index');
        Route::get('/ver-compras', [EcommerceController::class, 'verCompras'])->name('servicios.ver-compras');
        Route::get('/compras-pendientes', [EcommerceController::class, 'comprasPendientes'])->name('servicios.compras-pendientes');
        Route::post('/listar-precompras', [EcommerceController::class, 'listarPrecompras'])->name('servicios.listar-precompras');
        Route::post('/desestimar-precompra', [EcommerceController::class, 'desestimarPrecompra'])->name('servicios.desestimar-precompra');
        Route::post('/identificar-trabajador', [EcommerceController::class, 'identificarTrabajador'])->name('servicios.identificar-trabajador');
        Route::post('/listar-servicios', [EcommerceController::class, 'listarServicios'])->name('servicios.listar-servicios');
        Route::post('/validar-tarifa', [EcommerceController::class, 'validarTarifa'])->name('servicios.validar-tarifa');
        Route::post('/crear-precompra', [EcommerceController::class, 'crearPrecompra'])->name('servicios.crear-precompra');
        Route::post('/validar-pago-epayco', [EcommerceController::class, 'validarPagoEpayco'])->name('servicios.validar-pago-epayco');
        Route::post('/guardar-venta', [EcommerceController::class, 'guardarVenta'])->name('servicios.guardar-venta');
        Route::post('/mis-compras', [EcommerceController::class, 'misCompras'])->name('servicios.mis-compras');
    });
});
