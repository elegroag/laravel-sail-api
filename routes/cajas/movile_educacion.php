<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio73Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio73')->group(function () {
        // Rutas para Mercurio73Controller - Promociones Educación
        Route::get('/index', [Mercurio73Controller::class, 'indexAction']);
        Route::get('/galeria', [Mercurio73Controller::class, 'galeriaAction']);
        Route::post('/guardar', [Mercurio73Controller::class, 'guardarAction']);
        Route::post('/arriba', [Mercurio73Controller::class, 'arribaAction']);
        Route::post('/abajo', [Mercurio73Controller::class, 'abajoAction']);
        Route::delete('/borrar', [Mercurio73Controller::class, 'borrarAction']);
    });
});
