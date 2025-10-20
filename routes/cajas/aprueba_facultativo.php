<?php

use App\Http\Controllers\Cajas\ApruebaFacultativoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionfac')->group(function () {
        Route::get('/index', [ApruebaFacultativoController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaFacultativoController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaFacultativoController::class, 'buscar']);
        Route::post('/infor', [ApruebaFacultativoController::class, 'infor']);
        Route::post('/aprobar', [ApruebaFacultativoController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaFacultativoController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaFacultativoController::class, 'rechazar']);
        Route::post('/borrar_filtro', [ApruebaFacultativoController::class, 'borrarFiltro']);
        Route::get('/editar/{id}', [ApruebaFacultativoController::class, 'editarView']);
        Route::post('/editar', [ApruebaFacultativoController::class, 'editaEmpresa']);
    });
});
