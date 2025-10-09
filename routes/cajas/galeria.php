<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio26Controller;
use Illuminate\Support\Facades\Route;

// Rutas para Mercurio26Controller - Galería
Route::get('/galeria/index', [Mercurio26Controller::class, 'indexAction'])->name('mercurio26.index')->middleware('auth');
Route::get('/galeria/galeria', [Mercurio26Controller::class, 'galeriaAction'])->name('mercurio26.galeria')->middleware('auth');
Route::post('/galeria/guardar', [Mercurio26Controller::class, 'guardarAction'])->name('mercurio26.guardar')->middleware('auth');
Route::post('/galeria/arriba', [Mercurio26Controller::class, 'arribaAction'])->name('mercurio26.arriba')->middleware('auth');
Route::post('/galeria/abajo', [Mercurio26Controller::class, 'abajoAction'])->name('mercurio26.abajo')->middleware('auth');
Route::delete('/galeria/borrar', [Mercurio26Controller::class, 'borrarAction'])->name('mercurio26.borrar')->middleware('auth');
