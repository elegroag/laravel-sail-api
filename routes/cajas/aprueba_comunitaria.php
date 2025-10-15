<?php

use App\Http\Controllers\Cajas\ApruebaComunitariaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacioncom')->group(function () {
        Route::get('/index', [ApruebaComunitariaController::class, 'indexAction']);
        Route::post('/infor', [ApruebaComunitariaController::class, 'inforAction']);
        Route::post('/aprobar', [ApruebaComunitariaController::class, 'aprobarAction']);
        Route::post('/devolver', [ApruebaComunitariaController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaComunitariaController::class, 'rechazarAction']);
        Route::post('/borrar_filtro', [ApruebaComunitariaController::class, 'borrarFiltroAction']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaComunitariaController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaComunitariaController::class, 'buscarAction']);
    });
});
