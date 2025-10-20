<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio52Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

// Rutas para Mercurio52Controller - Menú Móvil
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio52')->group(function () {
        Route::get('/index', [Mercurio52Controller::class, 'index'])->name('mercurio52.index');
        Route::post('/aplicar_filtro', [Mercurio52Controller::class, 'aplicarFiltro'])->name('mercurio52.aplicar_filtro');
        Route::post('/change_cantidad_pagina', [Mercurio52Controller::class, 'changeCantidadPagina'])->name('mercurio52.change_cantidad_pagina');
        Route::get('/buscar', [Mercurio52Controller::class, 'buscar'])->name('mercurio52.buscar');
        Route::get('/editar', [Mercurio52Controller::class, 'editar'])->name('mercurio52.editar');
        Route::delete('/borrar', [Mercurio52Controller::class, 'borrar'])->name('mercurio52.borrar');
        Route::post('/guardar', [Mercurio52Controller::class, 'guardar'])->name('mercurio52.guardar');
        Route::post('/valide_pk', [Mercurio52Controller::class, 'validePk'])->name('mercurio52.valide_pk');
        Route::get('/reporte/{format?}', [Mercurio52Controller::class, 'reporte'])->name('mercurio52.reporte');
    });
});
