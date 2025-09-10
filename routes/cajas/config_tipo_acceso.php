<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio06Controller;

// Ruta GET para mostrar la página principal de tipos de acceso
Route::get('/tipo-acceso', [Mercurio06Controller::class, 'indexAction']);

// Ruta POST para buscar tipos de acceso (método AJAX)
Route::post('/tipo-acceso/buscar', [Mercurio06Controller::class, 'buscarAction']);

// Ruta GET para editar tipos de acceso (método AJAX, con parámetro opcional)
Route::get('/tipo-acceso/editar/{tipo?}', [Mercurio06Controller::class, 'editarAction']);

// Ruta POST para borrar tipos de acceso (método AJAX)
Route::post('/tipo-acceso/borrar', [Mercurio06Controller::class, 'borrarAction']);

// Ruta POST para guardar tipos de acceso (método AJAX)
Route::post('/tipo-acceso/guardar', [Mercurio06Controller::class, 'guardarAction']);

// Ruta POST para validar clave primaria (método AJAX)
Route::post('/tipo-acceso/valide-pk', [Mercurio06Controller::class, 'validePkAction']);

// Ruta POST para validar clave primaria de campos (método AJAX)
Route::post('/tipo-acceso/valide-pk-campo', [Mercurio06Controller::class, 'validePkCampoAction']);

// Ruta GET para vista de campos (método AJAX, con parámetro opcional)
Route::get('/tipo-acceso/campo-view/{tipo?}', [Mercurio06Controller::class, 'campo_viewAction']);

// Ruta POST para guardar campos (método AJAX)
Route::post('/tipo-acceso/guardar-campo', [Mercurio06Controller::class, 'guardarCampoAction']);

// Ruta GET para editar campos (método AJAX)
Route::get('/tipo-acceso/editar-campo', [Mercurio06Controller::class, 'editarCampoAction']);

// Ruta POST para borrar campos (método AJAX)
Route::post('/tipo-acceso/borrar-campo', [Mercurio06Controller::class, 'borrarCampoAction']);

// Ruta GET para generar reportes de tipos de acceso (con parámetro de formato)
Route::get('/tipo-acceso/reporte/{format?}', [Mercurio06Controller::class, 'reporteAction']);
