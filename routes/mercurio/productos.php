<?php

use App\Http\Controllers\Mercurio\ProductosController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::prefix('/mercurio/productos')->group(function () {
        Route::get('/', function () {
            return redirect()->route('productos.index');
        });
        Route::get('/index', [ProductosController::class, 'index'])->name('productos.index');
        Route::get('/complemento_nutricional', [ProductosController::class, 'complementoNutricional']);
        Route::post('/aplicar_cupo', [ProductosController::class, 'aplicarCupo']);
        Route::post('/numero_cupos_disponibles', [ProductosController::class, 'numeroCuposDisponibles']);
        Route::post('/servicios_aplicados', [ProductosController::class, 'serviciosAplicados']);
        Route::post('/buscar_cupo', [ProductosController::class, 'buscarCupo']);
    });
});
