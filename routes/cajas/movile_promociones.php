<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio57Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio57')->group(function () {
        Route::get('/index', [Mercurio57Controller::class, 'index']);
        Route::post('/galeria', [Mercurio57Controller::class, 'galeria']);
        Route::post('/guardar', [Mercurio57Controller::class, 'guardar']);
        Route::post('/arriba', [Mercurio57Controller::class, 'arriba']);
        Route::post('/abajo', [Mercurio57Controller::class, 'abajo']);
        Route::post('/borrar', [Mercurio57Controller::class, 'borrar']);
    });
});
