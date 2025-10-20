<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio53Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio53')->group(function () {
        // Rutas para Mercurio53Controller - Destacadas
        Route::get('/index', [Mercurio53Controller::class, 'index']);
        Route::get('/galeria', [Mercurio53Controller::class, 'galeria']);
        Route::post('/guardar', [Mercurio53Controller::class, 'guardar']);
        Route::post('/arriba', [Mercurio53Controller::class, 'arriba']);
        Route::post('/abajo', [Mercurio53Controller::class, 'abajo']);
        Route::delete('/borrar', [Mercurio53Controller::class, 'borrar']);
    });
});
