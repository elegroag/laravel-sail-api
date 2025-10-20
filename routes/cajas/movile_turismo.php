<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio72Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio72')->group(function () {
        // Definir rutas para el controlador Mercurio72
        Route::get('/index', [Mercurio72Controller::class, 'index'])->name('promociones.turismo.index'); // Mostrar la lista de promociones
        Route::post('/galeria', [Mercurio72Controller::class, 'galeria'])->name('promociones.turismo.galeria'); // Mostrar la galería
        Route::post('/guardar', [Mercurio72Controller::class, 'guardar'])->name('promociones.turismo.guardar'); // Guardar una nueva promoción
        Route::post('/arriba', [Mercurio72Controller::class, 'arriba'])->name('promociones.turismo.arriba'); // Mover promoción arriba
        Route::post('/abajo', [Mercurio72Controller::class, 'abajo'])->name('promociones.turismo.abajo'); // Mover promoción abajo
        Route::post('/borrar', [Mercurio72Controller::class, 'borrar'])->name('promociones.turismo.borrar'); // Borrar una promoción
    });
});
