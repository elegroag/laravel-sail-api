<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio65Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio65')->group(function () {
        // Rutas para Mercurio65Controller - Comercios
        Route::get('/index', [Mercurio65Controller::class, 'indexAction']);
        Route::post('/aplicar-filtro', [Mercurio65Controller::class, 'aplicarFiltroAction']);
        Route::post('/change-cantidad-pagina', [Mercurio65Controller::class, 'changeCantidadPaginaAction']);
        Route::get('/buscar', [Mercurio65Controller::class, 'buscarAction']);
        Route::get('/editar', [Mercurio65Controller::class, 'editarAction']);
        Route::delete('/borrar', [Mercurio65Controller::class, 'borrarAction']);
        Route::post('/guardar', [Mercurio65Controller::class, 'guardarAction']);
        Route::post('/valide-pk', [Mercurio65Controller::class, 'validePkAction']);
        Route::get('/reporte/{format?}', [Mercurio65Controller::class, 'reporteAction']);
    });
});
