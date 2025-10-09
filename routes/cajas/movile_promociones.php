<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio57Controller;
use Illuminate\Support\Facades\Route;

// Rutas para Mercurio57Controller - Promociones Móvil
Route::get('/movile-promociones/index', [Mercurio57Controller::class, 'indexAction'])->name('mercurio57.index')->middleware('auth');
Route::get('/movile-promociones/galeria', [Mercurio57Controller::class, 'galeriaAction'])->name('mercurio57.galeria')->middleware('auth');
Route::post('/movile-promociones/guardar', [Mercurio57Controller::class, 'guardarAction'])->name('mercurio57.guardar')->middleware('auth');
Route::post('/movile-promociones/arriba', [Mercurio57Controller::class, 'arribaAction'])->name('mercurio57.arriba')->middleware('auth');
Route::post('/movile-promociones/abajo', [Mercurio57Controller::class, 'abajoAction'])->name('mercurio57.abajo')->middleware('auth');
Route::delete('/movile-promociones/borrar', [Mercurio57Controller::class, 'borrarAction'])->name('mercurio57.borrar')->middleware('auth');
