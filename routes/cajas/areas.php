<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio56Controller;
use Illuminate\Support\Facades\Route;

// Rutas para Mercurio56Controller - Áreas
Route::get('/areas/index', [Mercurio56Controller::class, 'indexAction'])->name('mercurio56.index')->middleware('auth');
Route::post('/areas/aplicar-filtro', [Mercurio56Controller::class, 'aplicarFiltroAction'])->name('mercurio56.aplicar-filtro')->middleware('auth');
Route::post('/areas/change-cantidad-pagina', [Mercurio56Controller::class, 'changeCantidadPaginaAction'])->name('mercurio56.change-cantidad-pagina')->middleware('auth');
Route::get('/areas/buscar', [Mercurio56Controller::class, 'buscarAction'])->name('mercurio56.buscar')->middleware('auth');
Route::get('/areas/editar', [Mercurio56Controller::class, 'editarAction'])->name('mercurio56.editar')->middleware('auth');
Route::delete('/areas/borrar', [Mercurio56Controller::class, 'borrarAction'])->name('mercurio56.borrar')->middleware('auth');
Route::post('/areas/guardar', [Mercurio56Controller::class, 'guardarAction'])->name('mercurio56.guardar')->middleware('auth');
Route::post('/areas/valide-pk', [Mercurio56Controller::class, 'validePkAction'])->name('mercurio56.valide-pk')->middleware('auth');
Route::get('/areas/reporte/{format?}', [Mercurio56Controller::class, 'reporteAction'])->name('mercurio56.reporte')->middleware('auth');
