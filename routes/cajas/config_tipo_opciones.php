<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio09Controller;

// Ruta GET para mostrar la página principal de tipos de opciones
Route::get('/tipo-opciones', [Mercurio09Controller::class, 'indexAction']);

// Ruta POST para buscar tipos de opciones (método AJAX)
Route::post('/tipo-opciones/buscar', [Mercurio09Controller::class, 'buscarAction']);

// Ruta GET para editar tipos de opciones (método AJAX, con parámetro opcional)
Route::get('/tipo-opciones/editar/{tipopc?}', [Mercurio09Controller::class, 'editarAction']);

// Ruta POST para borrar tipos de opciones (método AJAX)
Route::post('/tipo-opciones/borrar', [Mercurio09Controller::class, 'borrarAction']);

// Ruta POST para guardar tipos de opciones (método AJAX)
Route::post('/tipo-opciones/guardar', [Mercurio09Controller::class, 'guardarAction']);

// Ruta POST para validar clave primaria (método AJAX)
Route::post('/tipo-opciones/valide-pk', [Mercurio09Controller::class, 'validePkAction']);

// Ruta GET para vista de archivos (método AJAX, con parámetro opcional)
Route::get('/tipo-opciones/archivos-view/{tipopc?}', [Mercurio09Controller::class, 'archivos_viewAction']);

// Ruta POST para guardar archivos (método AJAX)
Route::post('/tipo-opciones/guardar-archivos', [Mercurio09Controller::class, 'guardarArchivosAction']);

// Ruta POST para establecer obligación de archivos (método AJAX)
Route::post('/tipo-opciones/obliga-archivos', [Mercurio09Controller::class, 'obligaArchivosAction']);

// Ruta GET para vista de archivos de empresa (método AJAX)
Route::get('/tipo-opciones/archivos-empresa-view', [Mercurio09Controller::class, 'archivos_empresa_viewAction']);

// Ruta POST para guardar archivos de empresa (método AJAX)
Route::post('/tipo-opciones/guardar-empresa-archivos', [Mercurio09Controller::class, 'guardarEmpresaArchivosAction']);

// Ruta POST para establecer obligación de archivos de empresa (método AJAX)
Route::post('/tipo-opciones/obliga-empresa-archivos', [Mercurio09Controller::class, 'obligaEmpresaArchivosAction']);

// Ruta GET para generar reportes de tipos de opciones (con parámetro de formato)
Route::get('/tipo-opciones/reporte/{format?}', [Mercurio09Controller::class, 'reporteAction']);
