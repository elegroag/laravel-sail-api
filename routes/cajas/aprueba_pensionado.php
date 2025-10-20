<?php

use App\Http\Controllers\Cajas\ApruebaPensionadoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionpen')->group(function () {
        Route::get('/index', [ApruebaPensionadoController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaPensionadoController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaPensionadoController::class, 'buscar']);
        Route::post('/infor', [ApruebaPensionadoController::class, 'infor']);
        Route::post('/aprobar', [ApruebaPensionadoController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaPensionadoController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaPensionadoController::class, 'rechazar']);
        Route::post('/borrar-filtro', [ApruebaPensionadoController::class, 'borrarFiltro']);
        Route::get('/buscar-en-sisu/{id}/{nit}', [ApruebaPensionadoController::class, 'buscarEnSisuView']);
        Route::get('/editar/{id}', [ApruebaPensionadoController::class, 'editarView']);
        Route::post('/editar', [ApruebaPensionadoController::class, 'editaEmpresa']);
        Route::get('/aportes/{id}', [ApruebaPensionadoController::class, 'aportes']);
        Route::post('/deshacer', [ApruebaPensionadoController::class, 'deshacer']);
    });
});
