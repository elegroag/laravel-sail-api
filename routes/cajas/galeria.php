<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio26Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio26')->group(function () {

        // Rutas para Mercurio26Controller - Galería
        Route::get('/index', [Mercurio26Controller::class, 'index'])->name('mercurio26.index');
        Route::post('/galeria', [Mercurio26Controller::class, 'galeria'])->name('mercurio26.galeria');
        Route::post('/guardar', [Mercurio26Controller::class, 'guardar'])->name('mercurio26.guardar');
        Route::post('/arriba', [Mercurio26Controller::class, 'arriba'])->name('mercurio26.arriba');
        Route::post('/abajo', [Mercurio26Controller::class, 'abajo'])->name('mercurio26.abajo');
        Route::post('/borrar', [Mercurio26Controller::class, 'borrar'])->name('mercurio26.borrar');
        Route::post('/aplicar_filtro', [Mercurio26Controller::class, 'aplicarFiltro']);
        Route::post('/change_cantidad_pagina', [Mercurio26Controller::class, 'changeCantidadPagina']);
    });
});
