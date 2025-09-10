<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio50Controller;

// Rutas para Mercurio50Controller - Compartido
Route::get('/mercurio50/index', [Mercurio50Controller::class, 'indexAction'])->name('mercurio50.index')->middleware('auth');
Route::post('/mercurio50/aplicar-filtro', [Mercurio50Controller::class, 'aplicarFiltroAction'])->name('mercurio50.aplicar-filtro')->middleware('auth');
Route::post('/mercurio50/change-cantidad-pagina', [Mercurio50Controller::class, 'changeCantidadPaginaAction'])->name('mercurio50.change-cantidad-pagina')->middleware('auth');
Route::get('/mercurio50/buscar', [Mercurio50Controller::class, 'buscarAction'])->name('mercurio50.buscar')->middleware('auth');
Route::get('/mercurio50/editar', [Mercurio50Controller::class, 'editarAction'])->name('mercurio50.editar')->middleware('auth');
Route::post('/mercurio50/guardar', [Mercurio50Controller::class, 'guardarAction'])->name('mercurio50.guardar')->middleware('auth');
