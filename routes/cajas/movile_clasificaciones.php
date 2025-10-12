<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio67Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('cajas/mercurio67')->group(function () {
        // Definir rutas para Mercurio67Controller
        Route::get('/index', [Mercurio67Controller::class, 'indexAction']);
        Route::post('buscar', [Mercurio67Controller::class, 'buscarAction']);
        Route::get('nuevo', [Mercurio67Controller::class, 'nuevoAction']);
        Route::post('editar', [Mercurio67Controller::class, 'editarAction']);
        Route::post('borrar', [Mercurio67Controller::class, 'borrarAction']);
        Route::post('guardar', [Mercurio67Controller::class, 'guardarAction']);
        Route::post('valide-pk', [Mercurio67Controller::class, 'validePkAction']);
        Route::get('reporte/{format?}', [Mercurio67Controller::class, 'reporteAction']);
        Route::post('aplicar-filtro', [Mercurio67Controller::class, 'aplicarFiltroAction']);
        Route::post('change-cantidad-pagina', [Mercurio67Controller::class, 'changeCantidadPaginaAction']);
    });
});
