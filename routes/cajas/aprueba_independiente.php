<?php

use App\Http\Controllers\Cajas\ApruebaIndependienteController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('aprobaindepen')->group(function () {
        Route::get('/', [ApruebaIndependienteController::class, 'indexAction']);
        Route::post('aplicar-filtro', [ApruebaIndependienteController::class, 'aplicarFiltroAction']);
        Route::get('opcional', [ApruebaIndependienteController::class, 'opcionalAction']);
        Route::post('buscar', [ApruebaIndependienteController::class, 'buscarAction']);
        Route::get('info', [ApruebaIndependienteController::class, 'inforAction']);
        Route::post('aprobar', [ApruebaIndependienteController::class, 'apruebaAction']);
        Route::post('devolver', [ApruebaIndependienteController::class, 'devolverAction']);
        Route::post('rechazar', [ApruebaIndependienteController::class, 'rechazarAction']);
        Route::post('pendiente-email', [ApruebaIndependienteController::class, 'pendiente_emailAction']);
        Route::post('rezago-correo', [ApruebaIndependienteController::class, 'rezagoCorreoAction']);
        Route::post('empresa-search', [ApruebaIndependienteController::class, 'empresa_searchAction']);
        Route::post('borrar-filtro', [ApruebaIndependienteController::class, 'borrarFiltroAction']);
        Route::get('aportes-view/{id}', [ApruebaIndependienteController::class, 'aportesViewAction']);
        Route::get('aportes/{id}', [ApruebaIndependienteController::class, 'aportesAction']);
        Route::get('info-aprobado/{id}', [ApruebaIndependienteController::class, 'infoAprobadoViewAction']);
        Route::post('deshacer', [ApruebaIndependienteController::class, 'deshacerAction']);
        Route::get('editar/{id}', [ApruebaIndependienteController::class, 'editarViewAction']);
        Route::post('editar', [ApruebaIndependienteController::class, 'edita_empresaAction']);
        Route::get('buscar-en-sisu/{id}', [ApruebaIndependienteController::class, 'buscarEnSisuViewAction']);
    });
});
