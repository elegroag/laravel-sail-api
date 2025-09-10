<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Controllers\Cajas\ApruebaFacultativoController;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('aprobacionfac')->group(function () {
        Route::get('/', [ApruebaFacultativoController::class, 'indexAction']);
        Route::post('aplicar-filtro', [ApruebaFacultativoController::class, 'aplicarFiltroAction']);
        Route::post('buscar', [ApruebaFacultativoController::class, 'buscarAction']);
        Route::post('info', [ApruebaFacultativoController::class, 'inforAction']);
        Route::post('aprobar', [ApruebaFacultativoController::class, 'apruebaAction']);
        Route::post('devolver', [ApruebaFacultativoController::class, 'devolverAction']);
        Route::post('rechazar', [ApruebaFacultativoController::class, 'rechazarAction']);
        Route::post('borrar-filtro', [ApruebaFacultativoController::class, 'borrarFiltroAction']);
        Route::get('editar/{id}', [ApruebaFacultativoController::class, 'editarViewAction']);
        Route::post('editar', [ApruebaFacultativoController::class, 'edita_empresaAction']);
    });
});
