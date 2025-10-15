<?php

use App\Http\Controllers\Cajas\ApruebaFacultativoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobacionfac')->group(function () {
        Route::get('/index', [ApruebaFacultativoController::class, 'indexAction']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaFacultativoController::class, 'aplicarFiltroAction']);
        Route::post('/buscar/{estado?}', [ApruebaFacultativoController::class, 'buscarAction']);
        Route::post('/infor', [ApruebaFacultativoController::class, 'inforAction']);
        Route::post('/aprobar', [ApruebaFacultativoController::class, 'apruebaAction']);
        Route::post('/devolver', [ApruebaFacultativoController::class, 'devolverAction']);
        Route::post('/rechazar', [ApruebaFacultativoController::class, 'rechazarAction']);
        Route::post('/borrar_filtro', [ApruebaFacultativoController::class, 'borrarFiltroAction']);
        Route::get('/editar/{id}', [ApruebaFacultativoController::class, 'editarViewAction']);
        Route::post('/editar', [ApruebaFacultativoController::class, 'edita_empresaAction']);
    });
});
