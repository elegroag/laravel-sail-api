<?php

use App\Http\Controllers\Cajas\ApruebaPensionadoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionpen')->group(function () {
        Route::get('/index', [ApruebaPensionadoController::class, 'indexAction']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaPensionadoController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaPensionadoController::class, 'buscarAction']);
        Route::post('/infor', [ApruebaPensionadoController::class, 'inforAction']);
        Route::post('/aprobar', [ApruebaPensionadoController::class, 'apruebaAction']);
        Route::post('/devolver', [ApruebaPensionadoController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaPensionadoController::class, 'rechazarAction']);
        Route::post('/borrar-filtro', [ApruebaPensionadoController::class, 'borrarFiltroAction']);
        Route::get('/buscar-en-sisu/{id}/{nit}', [ApruebaPensionadoController::class, 'buscarEnSisuViewAction']);
        Route::get('/editar/{id}', [ApruebaPensionadoController::class, 'editarViewAction']);
        Route::post('/editar', [ApruebaPensionadoController::class, 'edita_empresaAction']);
        Route::get('/aportes/{id}', [ApruebaPensionadoController::class, 'aportesAction']);
        Route::post('/deshacer', [ApruebaPensionadoController::class, 'deshacerAction']);
    });
});
