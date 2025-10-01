<?php

use App\Http\Controllers\Cajas\ApruebaUpEmpresaController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Definir rutas para ApruebaUpEmpresaController
Route::middleware(['cajas.cookie.authenticated'])->group(function () {
    Route::prefix('aprueba-up-empresa')->group(function () {
        Route::get('/', [ApruebaUpEmpresaController::class, 'indexAction'])->name('aprueba_up_empresa.index');
        Route::post('aplicar-filtro/{estado?}', [ApruebaUpEmpresaController::class, 'aplicarFiltroAction'])->name('aprueba_up_empresa.aplicarFiltro');
        Route::post('change-cantidad-pagina', [ApruebaUpEmpresaController::class, 'changeCantidadPaginaAction'])->name('aprueba_up_empresa.changeCantidadPagina');
        Route::get('opcional/{estado?}', [ApruebaUpEmpresaController::class, 'opcionalAction'])->name('aprueba_up_empresa.opcional');
        Route::post('buscar/{estado?}', [ApruebaUpEmpresaController::class, 'buscarAction'])->name('aprueba_up_empresa.buscar');
        Route::post('devolver', [ApruebaUpEmpresaController::class, 'devolverAction'])->name('aprueba_up_empresa.devolver');
        Route::post('rechazar', [ApruebaUpEmpresaController::class, 'rechazarAction'])->name('aprueba_up_empresa.rechazar');
        Route::post('aprueba', [ApruebaUpEmpresaController::class, 'apruebaAction'])->name('aprueba_up_empresa.aprueba');
        Route::post('borrar-filtro', [ApruebaUpEmpresaController::class, 'borrarFiltroAction'])->name('aprueba_up_empresa.borrarFiltro');
        Route::get('infor/{id}', [ApruebaUpEmpresaController::class, 'inforAction'])->name('aprueba_up_empresa.infor');
    });
});
