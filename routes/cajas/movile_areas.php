<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio56Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Rutas para Mercurio56Controller - Áreas
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio55')->group(function () {
        Route::get('/index', [Mercurio56Controller::class, 'indexAction'])->name('mercurio55.index');
        Route::post('/aplicar_filtro', [Mercurio56Controller::class, 'aplicarFiltroAction'])->name('mercurio55.aplicar_filtro');
        Route::post('/change_cantidad_pagina', [Mercurio56Controller::class, 'changeCantidadPaginaAction'])->name('mercurio55.change_cantidad_pagina');
        Route::get('/buscar', [Mercurio56Controller::class, 'buscarAction'])->name('mercurio55.buscar');
        Route::get('/editar', [Mercurio56Controller::class, 'editarAction'])->name('mercurio55.editar');
        Route::delete('/borrar', [Mercurio56Controller::class, 'borrarAction'])->name('mercurio55.borrar');
        Route::post('/guardar', [Mercurio56Controller::class, 'guardarAction'])->name('mercurio55.guardar');
        Route::post('/valide_pk', [Mercurio56Controller::class, 'validePkAction'])->name('mercurio55.valide_pk');
        Route::get('/reporte/{format?}', [Mercurio56Controller::class, 'reporteAction'])->name('mercurio55.reporte');
    });
});
