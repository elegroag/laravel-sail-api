<?php

use App\Http\Controllers\Cajas\ApruebaFacultativoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/aprobacionfac')->group(function () {
        Route::get('/index', [ApruebaFacultativoController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaFacultativoController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaFacultativoController::class, 'buscar']);
        Route::post('/infor', [ApruebaFacultativoController::class, 'infor']);
        Route::post('/aprueba', [ApruebaFacultativoController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaFacultativoController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaFacultativoController::class, 'rechazar']);
        Route::get('/editar/{id}', [ApruebaFacultativoController::class, 'editarView']);
        Route::post('/editar', [ApruebaFacultativoController::class, 'editaEmpresa']);
        Route::post('/borrar-filtro', [ApruebaFacultativoController::class, 'borrarFiltro']);
    });
});
