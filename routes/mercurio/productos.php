<?php

use App\Http\Controllers\Mercurio\ProductosController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/productos')->group(function () {
        Route::get('/', function () {
            return redirect()->route('productos.index');
        });
        Route::get('/index', [ProductosController::class, 'indexAction'])->name('productos.index');
        Route::get('/complemento_nutricional', [ProductosController::class, 'complementoNutricionalAction']);
        Route::post('/aplicar_cupo', [ProductosController::class, 'aplicarCupoAction']);
        Route::post('/numero_cupos_disponibles', [ProductosController::class, 'numeroCuposDisponiblesAction']);
        Route::post('/servicios_aplicados', [ProductosController::class, 'serviciosAplicadosAction']);
        Route::post('/buscar_cupo', [ProductosController::class, 'buscarCupoAction']);
    });
});
