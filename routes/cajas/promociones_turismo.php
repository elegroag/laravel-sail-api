<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio72Controller;

// Definir rutas para el controlador Mercurio72
Route::get('/', [Mercurio72Controller::class, 'indexAction'])->name('promociones.turismo.index'); // Mostrar la lista de promociones
Route::get('/galeria', [Mercurio72Controller::class, 'galeriaAction'])->name('promociones.turismo.galeria'); // Mostrar la galería
Route::post('/guardar', [Mercurio72Controller::class, 'guardarAction'])->name('promociones.turismo.guardar'); // Guardar una nueva promoción
Route::post('/arriba', [Mercurio72Controller::class, 'arribaAction'])->name('promociones.turismo.arriba'); // Mover promoción arriba
Route::post('/abajo', [Mercurio72Controller::class, 'abajoAction'])->name('promociones.turismo.abajo'); // Mover promoción abajo
Route::post('/borrar', [Mercurio72Controller::class, 'borrarAction'])->name('promociones.turismo.borrar'); // Borrar una promoción
