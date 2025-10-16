<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio59Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/servicios')->group(function () {

        Route::get('/index', [Mercurio59Controller::class, 'indexAction'])->name('mercurio59.index')->middleware('auth');
        Route::post('/aplicar-filtro', [Mercurio59Controller::class, 'aplicarFiltroAction'])->name('mercurio59.aplicar-filtro')->middleware('auth');
        Route::post('/change-cantidad-pagina', [Mercurio59Controller::class, 'changeCantidadPaginaAction'])->name('mercurio59.change-cantidad-pagina')->middleware('auth');
        Route::get('/buscar', [Mercurio59Controller::class, 'buscarAction'])->name('mercurio59.buscar')->middleware('auth');
        Route::get('/editar', [Mercurio59Controller::class, 'editarAction'])->name('mercurio59.editar')->middleware('auth');
        Route::delete('/borrar', [Mercurio59Controller::class, 'borrarAction'])->name('mercurio59.borrar')->middleware('auth');
        Route::post('/guardar', [Mercurio59Controller::class, 'guardarAction'])->name('mercurio59.guardar')->middleware('auth');
        Route::post('/valide-pk', [Mercurio59Controller::class, 'validePkAction'])->name('mercurio59.valide-pk')->middleware('auth');
        Route::get('/reporte/{format?}', [Mercurio59Controller::class, 'reporteAction'])->name('mercurio59.reporte')->middleware('auth');
    });
});
