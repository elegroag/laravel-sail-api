<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio73Controller;
use Illuminate\Support\Facades\Route;

// Rutas para Mercurio73Controller - Promociones Educación
Route::get('/promociones-educacion/index', [Mercurio73Controller::class, 'indexAction'])->name('mercurio73.index')->middleware('auth');
Route::get('/promociones-educacion/galeria', [Mercurio73Controller::class, 'galeriaAction'])->name('mercurio73.galeria')->middleware('auth');
Route::post('/promociones-educacion/guardar', [Mercurio73Controller::class, 'guardarAction'])->name('mercurio73.guardar')->middleware('auth');
Route::post('/promociones-educacion/arriba', [Mercurio73Controller::class, 'arribaAction'])->name('mercurio73.arriba')->middleware('auth');
Route::post('/promociones-educacion/abajo', [Mercurio73Controller::class, 'abajoAction'])->name('mercurio73.abajo')->middleware('auth');
Route::delete('/promociones-educacion/borrar', [Mercurio73Controller::class, 'borrarAction'])->name('mercurio73.borrar')->middleware('auth');
