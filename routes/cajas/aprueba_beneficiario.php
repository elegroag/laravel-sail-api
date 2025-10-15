<?php

use App\Http\Controllers\Cajas\ApruebaBeneficiarioController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionben')->group(function () {
        Route::get('/index', [ApruebaBeneficiarioController::class, 'indexAction']);
        Route::post('/filtrar', [ApruebaBeneficiarioController::class, 'aplicarFiltroAction']);
        Route::post('/aprobar', [ApruebaBeneficiarioController::class, 'apruebaAction']);
        Route::post('/devolver', [ApruebaBeneficiarioController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaBeneficiarioController::class, 'rechazarAction']);
        Route::post('/borrar_filtro', [ApruebaBeneficiarioController::class, 'borrarFiltroAction']);
        Route::post('/infor', [ApruebaBeneficiarioController::class, 'inforAction']);
        Route::post('/deshacer', [ApruebaBeneficiarioController::class, 'deshacerAction']);

        Route::post('/aplicar_filtro/{estado?}', [ApruebaBeneficiarioController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaBeneficiarioController::class, 'buscarAction']);
    });
});
