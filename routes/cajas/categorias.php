<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio51Controller;

// Rutas para Mercurio51Controller - Categorías
Route::get('/categorias/index', [Mercurio51Controller::class, 'indexAction'])->name('mercurio51.index')->middleware('auth');
Route::post('/categorias/aplicar-filtro', [Mercurio51Controller::class, 'aplicarFiltroAction'])->name('mercurio51.aplicar-filtro')->middleware('auth');
Route::post('/categorias/change-cantidad-pagina', [Mercurio51Controller::class, 'changeCantidadPaginaAction'])->name('mercurio51.change-cantidad-pagina')->middleware('auth');
Route::get('/categorias/buscar', [Mercurio51Controller::class, 'buscarAction'])->name('mercurio51.buscar')->middleware('auth');
Route::get('/categorias/editar', [Mercurio51Controller::class, 'editarAction'])->name('mercurio51.editar')->middleware('auth');
Route::delete('/categorias/borrar', [Mercurio51Controller::class, 'borrarAction'])->name('mercurio51.borrar')->middleware('auth');
Route::post('/categorias/guardar', [Mercurio51Controller::class, 'guardarAction'])->name('mercurio51.guardar')->middleware('auth');
Route::post('/categorias/valide-pk', [Mercurio51Controller::class, 'validePkAction'])->name('mercurio51.valide-pk')->middleware('auth');
Route::get('/categorias/reporte/{format?}', [Mercurio51Controller::class, 'reporteAction'])->name('mercurio51.reporte')->middleware('auth');
