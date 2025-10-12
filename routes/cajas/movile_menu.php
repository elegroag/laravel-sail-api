<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio52Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CajasCookieAuthenticated;

// Rutas para Mercurio52Controller - Menú Móvil
Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    Route::prefix('/cajas/mercurio52')->group(function () {
        Route::get('/index', [Mercurio52Controller::class, 'indexAction'])->name('mercurio52.index')->middleware('auth');
        Route::post('/aplicar-filtro', [Mercurio52Controller::class, 'aplicarFiltroAction'])->name('mercurio52.aplicar-filtro')->middleware('auth');
        Route::post('/change-cantidad-pagina', [Mercurio52Controller::class, 'changeCantidadPaginaAction'])->name('mercurio52.change-cantidad-pagina')->middleware('auth');
        Route::get('/buscar', [Mercurio52Controller::class, 'buscarAction'])->name('mercurio52.buscar')->middleware('auth');
        Route::get('/editar', [Mercurio52Controller::class, 'editarAction'])->name('mercurio52.editar')->middleware('auth');
        Route::delete('/borrar', [Mercurio52Controller::class, 'borrarAction'])->name('mercurio52.borrar')->middleware('auth');
        Route::post('/guardar', [Mercurio52Controller::class, 'guardarAction'])->name('mercurio52.guardar')->middleware('auth');
        Route::post('/valide-pk', [Mercurio52Controller::class, 'validePkAction'])->name('mercurio52.valide-pk')->middleware('auth');
        Route::get('/reporte/{format?}', [Mercurio52Controller::class, 'reporteAction'])->name('mercurio52.reporte')->middleware('auth');
    });
});
