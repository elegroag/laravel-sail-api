<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio50Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio50')->group(function () {

        Route::get('/index', [Mercurio50Controller::class, 'indexAction'])->name('mercurio50.index');
        Route::post('/aplicar_filtro', [Mercurio50Controller::class, 'aplicarFiltroAction'])->name('mercurio50.aplicar_filtro');
        Route::post('/change_cantidad_pagina', [Mercurio50Controller::class, 'changeCantidadPaginaAction'])->name('mercurio50.change_cantidad_pagina');
        Route::get('/buscar', [Mercurio50Controller::class, 'buscarAction'])->name('mercurio50.buscar');
        Route::get('/editar', [Mercurio50Controller::class, 'editarAction'])->name('mercurio50.editar');
        Route::post('/guardar', [Mercurio50Controller::class, 'guardarAction'])->name('mercurio50.guardar');
    });
});
