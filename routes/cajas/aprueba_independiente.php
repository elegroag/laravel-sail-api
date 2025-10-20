<?php

use App\Http\Controllers\Cajas\ApruebaIndependienteController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/aprobaindepen')->group(function () {
        Route::get('/index', [ApruebaIndependienteController::class, 'index']);
        Route::post('/aplicar_filtro/{estado?}', [ApruebaIndependienteController::class, 'aplicarFiltro']);
        Route::post('/buscar/{estado?}', [ApruebaIndependienteController::class, 'buscar']);
        Route::get('/opcional', [ApruebaIndependienteController::class, 'opcional']);
        Route::post('/infor', [ApruebaIndependienteController::class, 'infor']);
        Route::post('/aprobar', [ApruebaIndependienteController::class, 'aprueba']);
        Route::post('/devolver', [ApruebaIndependienteController::class, 'devolver']);
        Route::post('/rechazar', [ApruebaIndependienteController::class, 'rechazar']);
        Route::post('/pendiente-email', [ApruebaIndependienteController::class, 'pendienteEmail']);
        Route::post('/rezago-correo', [ApruebaIndependienteController::class, 'rezagoCorreo']);
        Route::post('/empresa-search', [ApruebaIndependienteController::class, 'empresaSearch']);
        Route::post('/borrar-filtro', [ApruebaIndependienteController::class, 'borrarFiltro']);
        Route::get('/aportes-view/{id}', [ApruebaIndependienteController::class, 'aportesView']);
        Route::get('/aportes/{id}', [ApruebaIndependienteController::class, 'aportes']);
        Route::get('/info-aprobado/{id}', [ApruebaIndependienteController::class, 'infoAprobadoView']);
        Route::post('/deshacer', [ApruebaIndependienteController::class, 'deshacer']);
        Route::get('/editar/{id}', [ApruebaIndependienteController::class, 'editarView']);
        Route::post('/editar', [ApruebaIndependienteController::class, 'editaEmpresa']);
        Route::get('/buscar-en-sisu/{id}', [ApruebaIndependienteController::class, 'buscarEnSisuView']);
    });
});
