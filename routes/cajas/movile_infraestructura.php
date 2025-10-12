<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio56Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio56')->group(function () {
        // Definir rutas para el controlador Mercurio56
        Route::get('/index', [Mercurio56Controller::class, 'indexAction']);
        Route::post('/buscar', [Mercurio56Controller::class, 'buscarAction']);
        Route::post('/editar', [Mercurio56Controller::class, 'editarAction']);
        Route::post('/borrar', [Mercurio56Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio56Controller::class, 'guardarAction']);
        Route::post('/valide-pk', [Mercurio56Controller::class, 'validePkAction']);
        Route::get('/reporte/{format?}', [Mercurio56Controller::class, 'reporteAction']);
    });
});
