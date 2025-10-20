<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio73Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio73')->group(function () {
        // Rutas para Mercurio73Controller - Promociones Educación
        Route::get('/index', [Mercurio73Controller::class, 'index']);
        Route::get('/galeria', [Mercurio73Controller::class, 'galeria']);
        Route::post('/guardar', [Mercurio73Controller::class, 'guardar']);
        Route::post('/arriba', [Mercurio73Controller::class, 'arriba']);
        Route::post('/abajo', [Mercurio73Controller::class, 'abajo']);
        Route::delete('/borrar', [Mercurio73Controller::class, 'borrar']);
    });
});
