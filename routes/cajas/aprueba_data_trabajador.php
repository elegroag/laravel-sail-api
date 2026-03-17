<?php

use App\Http\Controllers\Cajas\ApruebaUpTrabajadorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/actualizatra')->group(function () {
        Route::get('/index', [ApruebaUpTrabajadorController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaUpTrabajadorController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaUpTrabajadorController::class, 'buscar']);
        Route::post('/infor', [ApruebaUpTrabajadorController::class, 'infor']);
        Route::post('/aprueba', [ApruebaUpTrabajadorController::class, 'aprueba']);
        Route::post('/rechazar', [ApruebaUpTrabajadorController::class, 'rechazar']);
        Route::post('/borrar-filtro', [ApruebaUpTrabajadorController::class, 'borrarFiltro']);
    });
});
