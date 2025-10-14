<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio52Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

// Rutas para Mercurio52Controller - Menú Móvil
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio52')->group(function () {
        Route::get('/index', [Mercurio52Controller::class, 'indexAction'])->name('mercurio52.index');
        Route::post('/aplicar_filtro', [Mercurio52Controller::class, 'aplicarFiltroAction'])->name('mercurio52.aplicar_filtro');
        Route::post('/change_cantidad_pagina', [Mercurio52Controller::class, 'changeCantidadPaginaAction'])->name('mercurio52.change_cantidad_pagina');
        Route::get('/buscar', [Mercurio52Controller::class, 'buscarAction'])->name('mercurio52.buscar');
        Route::get('/editar', [Mercurio52Controller::class, 'editarAction'])->name('mercurio52.editar');
        Route::delete('/borrar', [Mercurio52Controller::class, 'borrarAction'])->name('mercurio52.borrar');
        Route::post('/guardar', [Mercurio52Controller::class, 'guardarAction'])->name('mercurio52.guardar');
        Route::post('/valide_pk', [Mercurio52Controller::class, 'validePkAction'])->name('mercurio52.valide_pk');
        Route::get('/reporte/{format?}', [Mercurio52Controller::class, 'reporteAction'])->name('mercurio52.reporte');
    });
});
