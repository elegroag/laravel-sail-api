<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio02Controller;

// Ruta GET para mostrar la página principal
Route::get('/config/caja', [Mercurio02Controller::class, 'indexAction']);

// Ruta POST para buscar datos (método AJAX)
Route::post('/config/caja/buscar', [Mercurio02Controller::class, 'buscarAction']);

// Ruta GET para editar datos (método AJAX)
Route::get('/config/caja/editar', [Mercurio02Controller::class, 'editarAction']);

// Ruta POST para guardar cambios (método AJAX)
Route::post('/config/caja/guardar', [Mercurio02Controller::class, 'guardarAction']);

// Ruta GET para obtener datos de configuración (método AJAX)
Route::get('/config/caja/configuracion', [Mercurio02Controller::class, 'getConfiguracionAction']);

// Ruta POST para actualizar datos de configuración (método AJAX)
Route::post('/config/caja/configuracion', [Mercurio02Controller::class, 'updateConfiguracionAction']);

// Ruta GET para obtener datos de caja (método AJAX)
Route::get('/config/caja/datos', [Mercurio02Controller::class, 'getDatosAction']);

// Ruta POST para actualizar datos de caja (método AJAX)
Route::post('/config/caja/datos', [Mercurio02Controller::class, 'updateDatosAction']);
