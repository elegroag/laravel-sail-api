<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio01Controller;

// Ruta GET para mostrar la página principal
Route::get('/config/basica', [Mercurio01Controller::class, 'indexAction']);

// Ruta POST para buscar datos (método AJAX)
Route::post('/config/basica/buscar', [Mercurio01Controller::class, 'buscarAction']);

// Ruta GET para editar datos (método AJAX)
Route::get('/config/basica/editar', [Mercurio01Controller::class, 'editarAction']);

// Ruta POST para guardar cambios (método AJAX)
Route::post('/config/basica/guardar', [Mercurio01Controller::class, 'guardarAction']);
