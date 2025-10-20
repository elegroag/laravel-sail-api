<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio58Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {

    Route::prefix('/cajas/mercurio58')->group(function () {
        Route::get('/index', [Mercurio58Controller::class, 'index'])->name('mercurio58.index');
        Route::get('/galeria', [Mercurio58Controller::class, 'galeria'])->name('mercurio58.galeria');
        Route::post('/guardar', [Mercurio58Controller::class, 'guardar'])->name('mercurio58.guardar');
        Route::post('/arriba', [Mercurio58Controller::class, 'arriba'])->name('mercurio58.arriba');
        Route::post('/abajo', [Mercurio58Controller::class, 'abajo'])->name('mercurio58.abajo');
        Route::delete('/borrar', [Mercurio58Controller::class, 'borrar'])->name('mercurio58.borrar');
    });
});
