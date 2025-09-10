<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio53Controller;

// Rutas para Mercurio53Controller - Destacadas
Route::get('/destacadas/index', [Mercurio53Controller::class, 'indexAction'])->name('mercurio53.index')->middleware('auth');
Route::get('/destacadas/galeria', [Mercurio53Controller::class, 'galeriaAction'])->name('mercurio53.galeria')->middleware('auth');
Route::post('/destacadas/guardar', [Mercurio53Controller::class, 'guardarAction'])->name('mercurio53.guardar')->middleware('auth');
Route::post('/destacadas/arriba', [Mercurio53Controller::class, 'arribaAction'])->name('mercurio53.arriba')->middleware('auth');
Route::post('/destacadas/abajo', [Mercurio53Controller::class, 'abajoAction'])->name('mercurio53.abajo')->middleware('auth');
Route::delete('/destacadas/borrar', [Mercurio53Controller::class, 'borrarAction'])->name('mercurio53.borrar')->middleware('auth');
