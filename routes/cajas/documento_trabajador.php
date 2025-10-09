<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio13Controller;
use Illuminate\Support\Facades\Route;

// Ruta GET para mostrar la página principal de documentos de trabajador
Route::get('/documento-trabajador', [Mercurio13Controller::class, 'indexAction']);

// Ruta GET para obtener información de documentos (método AJAX, con parámetros opcionales)
Route::get('/documento-trabajador/infor/{tipopc?}/{coddoc?}', [Mercurio13Controller::class, 'inforAction']);

// Ruta POST para guardar documentos de trabajador (método AJAX)
Route::post('/documento-trabajador/guardar', [Mercurio13Controller::class, 'guardarAction']);

// Ruta POST para borrar documentos de trabajador (método AJAX)
Route::post('/documento-trabajador/borrar', [Mercurio13Controller::class, 'borrarAction']);
