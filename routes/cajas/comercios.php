<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio65Controller;

// Rutas para Mercurio65Controller - Comercios
Route::get('/comercios/index', [Mercurio65Controller::class, 'indexAction'])->name('mercurio65.index')->middleware('auth');
Route::post('/comercios/aplicar-filtro', [Mercurio65Controller::class, 'aplicarFiltroAction'])->name('mercurio65.aplicar-filtro')->middleware('auth');
Route::post('/comercios/change-cantidad-pagina', [Mercurio65Controller::class, 'changeCantidadPaginaAction'])->name('mercurio65.change-cantidad-pagina')->middleware('auth');
Route::get('/comercios/buscar', [Mercurio65Controller::class, 'buscarAction'])->name('mercurio65.buscar')->middleware('auth');
Route::get('/comercios/editar', [Mercurio65Controller::class, 'editarAction'])->name('mercurio65.editar')->middleware('auth');
Route::delete('/comercios/borrar', [Mercurio65Controller::class, 'borrarAction'])->name('mercurio65.borrar')->middleware('auth');
Route::post('/comercios/guardar', [Mercurio65Controller::class, 'guardarAction'])->name('mercurio65.guardar')->middleware('auth');
Route::post('/comercios/valide-pk', [Mercurio65Controller::class, 'validePkAction'])->name('mercurio65.valide-pk')->middleware('auth');
Route::get('/comercios/reporte/{format?}', [Mercurio65Controller::class, 'reporteAction'])->name('mercurio65.reporte')->middleware('auth');
