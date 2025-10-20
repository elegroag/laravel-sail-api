<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio65Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio65')->group(function () {
        // Rutas para Mercurio65Controller - Comercios
        Route::get('/index', [Mercurio65Controller::class, 'index']);
        Route::post('/aplicar-filtro', [Mercurio65Controller::class, 'aplicarFiltro']);
        Route::post('/change-cantidad-pagina', [Mercurio65Controller::class, 'changeCantidadPagina']);
        Route::get('/buscar', [Mercurio65Controller::class, 'buscar']);
        Route::get('/editar', [Mercurio65Controller::class, 'editar']);
        Route::delete('/borrar', [Mercurio65Controller::class, 'borrar']);
        Route::post('/guardar', [Mercurio65Controller::class, 'guardar']);
        Route::post('/valide-pk', [Mercurio65Controller::class, 'validePk']);
        Route::get('/reporte/{format?}', [Mercurio65Controller::class, 'reporte']);
    });
});
