<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio57Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio57')->group(function () {
        Route::get('/index', [Mercurio57Controller::class, 'indexAction']);
        Route::get('/galeria', [Mercurio57Controller::class, 'galeriaAction']);
        Route::post('/guardar', [Mercurio57Controller::class, 'guardarAction']);
        Route::post('/arriba', [Mercurio57Controller::class, 'arribaAction']);
        Route::post('/abajo', [Mercurio57Controller::class, 'abajoAction']);
        Route::post('/borrar', [Mercurio57Controller::class, 'borrarAction']);
    });
});
