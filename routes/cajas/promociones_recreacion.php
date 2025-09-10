<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio74Controller;

// Definir rutas para el controlador Mercurio74
Route::get('/', [Mercurio74Controller::class, 'indexAction'])->name('promociones.recreacion.index'); // Mostrar lista de promociones
Route::get('/galeria', [Mercurio74Controller::class, 'galeriaAction'])->name('promociones.recreacion.galeria'); // Mostrar galería
Route::post('/guardar', [Mercurio74Controller::class, 'guardarAction'])->name('promociones.recreacion.guardar'); // Guardar promoción
Route::post('/arriba', [Mercurio74Controller::class, 'arribaAction'])->name('promociones.recreacion.arriba'); // Mover promoción arriba
Route::post('/abajo', [Mercurio74Controller::class, 'abajoAction'])->name('promociones.recreacion.abajo'); // Mover promoción abajo
Route::post('/borrar', [Mercurio74Controller::class, 'borrarAction'])->name('promociones.recreacion.borrar'); // Borrar promoción
