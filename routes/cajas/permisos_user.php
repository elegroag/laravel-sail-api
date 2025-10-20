<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Gener42Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/gener42')->group(function () {
        Route::get('/index', [Gener42Controller::class, 'index']);
        Route::post('/buscar', [Gener42Controller::class, 'buscar']);
        Route::post('/guardar', [Gener42Controller::class, 'guardar']);
    });
});
