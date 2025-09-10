<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio58Controller;

// Rutas para Mercurio58Controller - Archivos Áreas
Route::get('/archivo-areas/index', [Mercurio58Controller::class, 'indexAction'])->name('mercurio58.index')->middleware('auth');
Route::get('/archivo-areas/galeria', [Mercurio58Controller::class, 'galeriaAction'])->name('mercurio58.galeria')->middleware('auth');
Route::post('/archivo-areas/guardar', [Mercurio58Controller::class, 'guardarAction'])->name('mercurio58.guardar')->middleware('auth');
Route::post('/archivo-areas/arriba', [Mercurio58Controller::class, 'arribaAction'])->name('mercurio58.arriba')->middleware('auth');
Route::post('/archivo-areas/abajo', [Mercurio58Controller::class, 'abajoAction'])->name('mercurio58.abajo')->middleware('auth');
Route::delete('/archivo-areas/borrar', [Mercurio58Controller::class, 'borrarAction'])->name('mercurio58.borrar')->middleware('auth');
