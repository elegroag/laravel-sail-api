<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio56Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio56')->group(function () {
        // Definir rutas para el controlador Mercurio56
        Route::get('/index', [Mercurio56Controller::class, 'index']);
        Route::post('/buscar', [Mercurio56Controller::class, 'buscar']);
        Route::post('/editar', [Mercurio56Controller::class, 'editar']);
        Route::post('/borrar', [Mercurio56Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio56Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio56Controller::class, 'validePk']);
        Route::get('/reporte/{format?}', [Mercurio56Controller::class, 'reporte']);
    });
});
