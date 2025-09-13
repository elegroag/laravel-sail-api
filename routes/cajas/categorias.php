<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio51Controller;

// Rutas para Mercurio51Controller - Categorías
Route::prefix('/cajas/categorias')->group(function () {
    Route::get('/', [Mercurio51Controller::class, 'indexAction'])->name('mercurio51.index');
    Route::get('/index', [Mercurio51Controller::class, 'indexAction'])->name('mercurio51.index');
    Route::post('/aplicar-filtro', [Mercurio51Controller::class, 'aplicarFiltroAction'])->name('mercurio51.aplicar-filtro');
    Route::post('/change-cantidad-pagina', [Mercurio51Controller::class, 'changeCantidadPaginaAction'])->name('mercurio51.change-cantidad-pagina');
    Route::get('/buscar', [Mercurio51Controller::class, 'buscarAction'])->name('mercurio51.buscar');
    Route::get('/editar', [Mercurio51Controller::class, 'editarAction'])->name('mercurio51.editar');
    Route::delete('/borrar', [Mercurio51Controller::class, 'borrarAction'])->name('mercurio51.borrar');
    Route::post('/guardar', [Mercurio51Controller::class, 'guardarAction'])->name('mercurio51.guardar');
    Route::post('/valide-pk', [Mercurio51Controller::class, 'validePkAction'])->name('mercurio51.valide-pk');
    Route::get('/reporte/{format?}', [Mercurio51Controller::class, 'reporteAction'])->name('mercurio51.reporte');
});
