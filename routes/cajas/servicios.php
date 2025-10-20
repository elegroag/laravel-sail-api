<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio59Controller;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/servicios')->group(function () {

        Route::get('/index', [Mercurio59Controller::class, 'index'])->name('mercurio59.index');
        Route::post('/aplicar-filtro', [Mercurio59Controller::class, 'aplicarFiltro'])->name('mercurio59.aplicar-filtro');
        Route::post('/change-cantidad-pagina', [Mercurio59Controller::class, 'changeCantidadPagina'])->name('mercurio59.change-cantidad-pagina');
        Route::get('/buscar', [Mercurio59Controller::class, 'buscar'])->name('mercurio59.buscar');
        Route::get('/editar', [Mercurio59Controller::class, 'editar'])->name('mercurio59.editar');
        Route::delete('/borrar', [Mercurio59Controller::class, 'borrar'])->name('mercurio59.borrar');
        Route::post('/guardar', [Mercurio59Controller::class, 'guardar'])->name('mercurio59.guardar');
        Route::post('/valide-pk', [Mercurio59Controller::class, 'validePk'])->name('mercurio59.valide-pk');
        Route::get('/reporte/{format?}', [Mercurio59Controller::class, 'reporte'])->name('mercurio59.reporte');
    });
});
