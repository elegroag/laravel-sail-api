<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Gener42Controller;

// Ruta GET para mostrar la página principal de permisos
Route::get('/permisos/user', [Gener42Controller::class, 'indexAction']);

// Ruta POST para buscar permisos (método AJAX)
Route::post('/permisos/user/buscar', [Gener42Controller::class, 'buscarAction']);

// Ruta POST para guardar cambios en permisos (método AJAX)
Route::post('/permisos/user/guardar', [Gener42Controller::class, 'guardarAction']);
