<?php

use App\Http\Controllers\Cajas\ApruebaTrabajadorController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/aprobaciontra')->group(function () {
        Route::get('/index', [ApruebaTrabajadorController::class, 'indexAction']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaTrabajadorController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaTrabajadorController::class, 'buscarAction']);

        Route::get('/info', [ApruebaTrabajadorController::class, 'inforAction']);
        Route::post('/aprobar', [ApruebaTrabajadorController::class, 'apruebaAction']);
        Route::post('/devolver', [ApruebaTrabajadorController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaTrabajadorController::class, 'rechazarAction']);
        Route::get('/opcional', [ApruebaTrabajadorController::class, 'opcionalAction']);
        Route::get('/editar/{id}', [ApruebaTrabajadorController::class, 'editarViewAction']);
        Route::post('/editar-solicitud', [ApruebaTrabajadorController::class, 'editar_solicitudAction']);
        Route::post('/buscar-sisu', [ApruebaTrabajadorController::class, 'buscar_sisuAction']);
        Route::post('/reaprobar', [ApruebaTrabajadorController::class, 'reaprobarAction']);
        Route::post('/validar-multiafiliacion', [ApruebaTrabajadorController::class, 'validarMultiafiliacionAction']);
    });
});
