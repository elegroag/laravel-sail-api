<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionben')->group(function () {
        Route::get('/index', [ApruebaBeneficiarioController::class, 'index']);
        Route::post('/filtrar', [ApruebaBeneficiarioController::class, 'aplicarFiltro']);
        Route::post('/aprobar', [ApruebaBeneficiarioController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaBeneficiarioController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaBeneficiarioController::class, 'rechazar']);
        Route::post('/borrar_filtro', [ApruebaBeneficiarioController::class, 'borrarFiltro']);
        Route::post('/infor', [ApruebaBeneficiarioController::class, 'infor']);
        Route::post('/deshacer', [ApruebaBeneficiarioController::class, 'deshacer']);

        Route::post('/aplicar_filtro/{estado?}', [ApruebaBeneficiarioController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaBeneficiarioController::class, 'buscar']);
    });
});
