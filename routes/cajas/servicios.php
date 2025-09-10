<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio59Controller;

// Rutas para Mercurio59Controller - Servicios
Route::get('/servicios/index', [Mercurio59Controller::class, 'indexAction'])->name('mercurio59.index')->middleware('auth');
Route::post('/servicios/aplicar-filtro', [Mercurio59Controller::class, 'aplicarFiltroAction'])->name('mercurio59.aplicar-filtro')->middleware('auth');
Route::post('/servicios/change-cantidad-pagina', [Mercurio59Controller::class, 'changeCantidadPaginaAction'])->name('mercurio59.change-cantidad-pagina')->middleware('auth');
Route::get('/servicios/buscar', [Mercurio59Controller::class, 'buscarAction'])->name('mercurio59.buscar')->middleware('auth');
Route::get('/servicios/editar', [Mercurio59Controller::class, 'editarAction'])->name('mercurio59.editar')->middleware('auth');
Route::delete('/servicios/borrar', [Mercurio59Controller::class, 'borrarAction'])->name('mercurio59.borrar')->middleware('auth');
Route::post('/servicios/guardar', [Mercurio59Controller::class, 'guardarAction'])->name('mercurio59.guardar')->middleware('auth');
Route::post('/servicios/valide-pk', [Mercurio59Controller::class, 'validePkAction'])->name('mercurio59.valide-pk')->middleware('auth');
Route::get('/servicios/reporte/{format?}', [Mercurio59Controller::class, 'reporteAction'])->name('mercurio59.reporte')->middleware('auth');
