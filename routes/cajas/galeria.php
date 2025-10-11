<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio26Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio26')->group(function () {
        // Rutas para Mercurio26Controller - Galería
        Route::get('/index', [Mercurio26Controller::class, 'indexAction'])->name('mercurio26.index');
        Route::get('/galeria', [Mercurio26Controller::class, 'galeriaAction'])->name('mercurio26.galeria');
        Route::post('/guardar', [Mercurio26Controller::class, 'guardarAction'])->name('mercurio26.guardar');
        Route::post('/arriba', [Mercurio26Controller::class, 'arribaAction'])->name('mercurio26.arriba');
        Route::post('/abajo', [Mercurio26Controller::class, 'abajoAction'])->name('mercurio26.abajo');
        Route::delete('/borrar', [Mercurio26Controller::class, 'borrarAction'])->name('mercurio26.borrar');

        Route::post('/aplicar_filtro', [Mercurio26Controller::class, 'aplicarFiltroAction']);
        Route::post('/change_cantidad_pagina', [Mercurio26Controller::class, 'changeCantidadPaginaAction']);
    });
});
