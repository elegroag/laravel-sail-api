<?php

use App\Http\Controllers\Cajas\ApruebaUpTrabajadorController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;


Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/actualizadatostra')->group(function () {
        Route::get('/index', [ApruebaUpTrabajadorController::class, 'indexAction'])->name('aprueba_up_trabajador.index');
        Route::post('/aplicar_filtro/{estado?}', [ApruebaUpTrabajadorController::class, 'aplicarFiltroAction'])->name('aprueba_up_trabajador.aplicarFiltro');
        Route::post('/buscar/{estado?}', [ApruebaUpTrabajadorController::class, 'buscarAction'])->name('aprueba_up_trabajador.buscar');
        Route::get('/infor/{id}', [ApruebaUpTrabajadorController::class, 'inforAction'])->name('aprueba_up_trabajador.infor');
        Route::post('/aprueba', [ApruebaUpTrabajadorController::class, 'apruebaAction'])->name('aprueba_up_trabajador.aprueba');
        Route::post('/rechazar', [ApruebaUpTrabajadorController::class, 'rechazarAction'])->name('aprueba_up_trabajador.rechazar');
    });
});
