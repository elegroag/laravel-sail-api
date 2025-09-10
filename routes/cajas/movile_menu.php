<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio52Controller;

// Rutas para Mercurio52Controller - Menú Móvil
Route::get('/movile-menu/index', [Mercurio52Controller::class, 'indexAction'])->name('mercurio52.index')->middleware('auth');
Route::post('/movile-menu/aplicar-filtro', [Mercurio52Controller::class, 'aplicarFiltroAction'])->name('mercurio52.aplicar-filtro')->middleware('auth');
Route::post('/movile-menu/change-cantidad-pagina', [Mercurio52Controller::class, 'changeCantidadPaginaAction'])->name('mercurio52.change-cantidad-pagina')->middleware('auth');
Route::get('/movile-menu/buscar', [Mercurio52Controller::class, 'buscarAction'])->name('mercurio52.buscar')->middleware('auth');
Route::get('/movile-menu/editar', [Mercurio52Controller::class, 'editarAction'])->name('mercurio52.editar')->middleware('auth');
Route::delete('/movile-menu/borrar', [Mercurio52Controller::class, 'borrarAction'])->name('mercurio52.borrar')->middleware('auth');
Route::post('/movile-menu/guardar', [Mercurio52Controller::class, 'guardarAction'])->name('mercurio52.guardar')->middleware('auth');
Route::post('/movile-menu/valide-pk', [Mercurio52Controller::class, 'validePkAction'])->name('mercurio52.valide-pk')->middleware('auth');
Route::get('/movile-menu/reporte/{format?}', [Mercurio52Controller::class, 'reporteAction'])->name('mercurio52.reporte')->middleware('auth');
