<?php

use App\Http\Controllers\Mercurio\ProductosController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/productos/index', [ProductosController::class, 'indexAction']);
    Route::get('/mercurio/productos/complemento_nutricional', [ProductosController::class, 'complementoNutricionalAction']);
    Route::post('/mercurio/productos/aplicar_cupo', [ProductosController::class, 'aplicarCupoAction']);
    Route::post('/mercurio/productos/numero_cupos_disponibles', [ProductosController::class, 'numeroCuposDisponiblesAction']);
    Route::post('/mercurio/productos/servicios_aplicados', [ProductosController::class, 'serviciosAplicadosAction']);
    Route::post('/mercurio/productos/buscar_cupo', [ProductosController::class, 'buscarCupoAction']);
});
