<?php

use App\Http\Controllers\Cajas\ApruebaTrabajadorController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/aprobaciontra')->group(function () {
        Route::get('/index', [ApruebaTrabajadorController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaTrabajadorController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaTrabajadorController::class, 'buscar']);

        Route::post('/infor', [ApruebaTrabajadorController::class, 'infor']);
        Route::post('/aprueba', [ApruebaTrabajadorController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaTrabajadorController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaTrabajadorController::class, 'rechazar']);
        Route::get('/opcional', [ApruebaTrabajadorController::class, 'opcional']);
        Route::get('/editar/{id}', [ApruebaTrabajadorController::class, 'editarView']);
        Route::post('/editar-solicitud', [ApruebaTrabajadorController::class, 'editarSolicitud']);
        Route::post('/buscar-sisu', [ApruebaTrabajadorController::class, 'buscarSisu']);
        Route::post('/reaprobar', [ApruebaTrabajadorController::class, 'reaprobar']);
        Route::post('/validar-multiafiliacion', [ApruebaTrabajadorController::class, 'validarMultiafiliacion']);
    });
});
