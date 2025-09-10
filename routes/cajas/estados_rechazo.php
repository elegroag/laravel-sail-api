<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio11Controller;

// Ruta GET para mostrar la página principal de motivos de rechazo
Route::get('/motivo-rechazo', [Mercurio11Controller::class, 'indexAction']);

// Ruta POST para buscar motivos de rechazo (método AJAX)
Route::post('/motivo-rechazo/buscar', [Mercurio11Controller::class, 'buscarAction']);

// Ruta GET para editar motivos de rechazo (método AJAX, con parámetro opcional)
Route::get('/motivo-rechazo/editar/{codest?}', [Mercurio11Controller::class, 'editarAction']);

// Ruta POST para borrar motivos de rechazo (método AJAX)
Route::post('/motivo-rechazo/borrar', [Mercurio11Controller::class, 'borrarAction']);

// Ruta POST para guardar motivos de rechazo (método AJAX)
Route::post('/motivo-rechazo/guardar', [Mercurio11Controller::class, 'guardarAction']);

// Ruta POST para validar clave primaria (método AJAX)
Route::post('/motivo-rechazo/valide-pk', [Mercurio11Controller::class, 'validePkAction']);

// Ruta GET para generar reportes de motivos de rechazo (con parámetro de formato)
Route::get('/motivo-rechazo/reporte/{format?}', [Mercurio11Controller::class, 'reporteAction']);
