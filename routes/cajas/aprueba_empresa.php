<?php

use App\Http\Controllers\Cajas\ApruebaEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('aprobacionemp')->group(function () {
        Route::get('/', [ApruebaEmpresaController::class, 'indexAction']);
        Route::post('aplicar-filtro', [ApruebaEmpresaController::class, 'aplicarFiltroAction']);
        Route::get('listar', [ApruebaEmpresaController::class, 'listarAction']);
        Route::post('buscar', [ApruebaEmpresaController::class, 'buscarAction']);
        Route::post('devolver', [ApruebaEmpresaController::class, 'devolverAction']);
        Route::post('rechazar', [ApruebaEmpresaController::class, 'rechazarAction']);
        Route::get('opcional', [ApruebaEmpresaController::class, 'opcionalAction']);
        Route::get('info/{id}', [ApruebaEmpresaController::class, 'inforAction']);
        Route::get('editar/{id}', [ApruebaEmpresaController::class, 'editarViewAction']);
        Route::post('editar', [ApruebaEmpresaController::class, 'edita_empresaAction']);
    });
});
