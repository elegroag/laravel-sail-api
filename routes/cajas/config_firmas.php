<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio03Controller;

// Ruta GET para mostrar la página principal de firmas
Route::get('/firmas', [Mercurio03Controller::class, 'indexAction']);

// Ruta POST para buscar firmas (método AJAX)
Route::post('/firmas/buscar', [Mercurio03Controller::class, 'buscarAction']);

// Ruta GET para editar firmas (método AJAX, con parámetro opcional)
Route::get('/firmas/editar/{codfir?}', [Mercurio03Controller::class, 'editarAction']);

// Ruta POST para borrar firmas (método AJAX)
Route::post('/firmas/borrar', [Mercurio03Controller::class, 'borrarAction']);

// Ruta POST para guardar firmas (método AJAX)
Route::post('/firmas/guardar', [Mercurio03Controller::class, 'guardarAction']);

// Ruta POST para validar clave primaria (método AJAX)
Route::post('/firmas/valide-pk', [Mercurio03Controller::class, 'validePkAction']);

// Ruta GET para generar reportes de firmas (con parámetro de formato)
Route::get('/firmas/reporte/{format?}', [Mercurio03Controller::class, 'reporteAction']);
