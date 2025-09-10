<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio12Controller;

// Ruta GET para mostrar la página principal de documentos
Route::get('/documentos', [Mercurio12Controller::class, 'indexAction']);

// Ruta POST para buscar documentos (método AJAX)
Route::post('/documentos/buscar', [Mercurio12Controller::class, 'buscarAction']);

// Ruta GET para editar documentos (método AJAX, con parámetro opcional)
Route::get('/documentos/editar/{coddoc?}', [Mercurio12Controller::class, 'editarAction']);

// Ruta POST para borrar documentos (método AJAX)
Route::post('/documentos/borrar', [Mercurio12Controller::class, 'borrarAction']);

// Ruta POST para guardar documentos (método AJAX)
Route::post('/documentos/guardar', [Mercurio12Controller::class, 'guardarAction']);

// Ruta POST para validar clave primaria (método AJAX)
Route::post('/documentos/valide-pk', [Mercurio12Controller::class, 'validePkAction']);

// Ruta GET para generar reportes de documentos (con parámetro de formato)
Route::get('/documentos/reporte/{format?}', [Mercurio12Controller::class, 'reporteAction']);
