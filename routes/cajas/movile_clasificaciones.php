<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio67Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio67')->group(function () {
        // Definir rutas para Mercurio67Controller
        Route::get('/index', [Mercurio67Controller::class, 'index']);
        Route::post('buscar', [Mercurio67Controller::class, 'buscar']);
        Route::get('nuevo', [Mercurio67Controller::class, 'nuevo']);
        Route::post('editar', [Mercurio67Controller::class, 'editar']);
        Route::post('borrar', [Mercurio67Controller::class, 'borrar']);
        Route::post('guardar', [Mercurio67Controller::class, 'guardar']);
        Route::post('valide-pk', [Mercurio67Controller::class, 'validePk']);
        Route::get('reporte/{format?}', [Mercurio67Controller::class, 'reporte']);
        Route::post('aplicar-filtro', [Mercurio67Controller::class, 'aplicarFiltro']);
        Route::post('change-cantidad-pagina', [Mercurio67Controller::class, 'changeCantidadPagina']);
    });
});
