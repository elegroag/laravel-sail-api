<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio74Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio74')->group(function () {
        // Definir rutas para el controlador Mercurio74
        Route::get('/index', [Mercurio74Controller::class, 'indexAction']);
        Route::get('/galeria', [Mercurio74Controller::class, 'galeriaAction']);
        Route::post('/guardar', [Mercurio74Controller::class, 'guardarAction']);
        Route::post('/arriba', [Mercurio74Controller::class, 'arribaAction']);
        Route::post('/abajo', [Mercurio74Controller::class, 'abajoAction']);
        Route::post('/borrar', [Mercurio74Controller::class, 'borrarAction']);
    });
});
