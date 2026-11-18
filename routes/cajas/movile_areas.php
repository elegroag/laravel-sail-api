<?php

use App\Http\Controllers\Cajas\Mercurio55Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio55')->group(function () {
        Route::get('/index', [Mercurio55Controller::class, 'index'])->name('mercurio55.index');
        Route::post('/aplicar_filtro', [Mercurio55Controller::class, 'aplicarFiltro'])->name('mercurio55.aplicar_filtro');
        Route::post('/change_cantidad_pagina', [Mercurio55Controller::class, 'changeCantidadPagina'])->name('mercurio55.change_cantidad_pagina');
        Route::match(['get', 'post'], '/buscar', [Mercurio55Controller::class, 'buscar'])->name('mercurio55.buscar');
        Route::post('/nuevo', [Mercurio55Controller::class, 'nuevo'])->name('mercurio55.nuevo');
        Route::post('/editar', [Mercurio55Controller::class, 'editar'])->name('mercurio55.editar');
        Route::post('/borrar', [Mercurio55Controller::class, 'borrar'])->name('mercurio55.borrar');
        Route::post('/guardar', [Mercurio55Controller::class, 'guardar'])->name('mercurio55.guardar');
        Route::post('/validePk', [Mercurio55Controller::class, 'validePk'])->name('mercurio55.valide_pk');
        Route::get('/reporte/{format?}', [Mercurio55Controller::class, 'reporte'])->name('mercurio55.reporte');
    });
});
