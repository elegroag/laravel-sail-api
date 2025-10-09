<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio04Controller;
use Illuminate\Support\Facades\Route;

// Ruta GET para mostrar la página principal de oficinas
Route::get('/oficina', [Mercurio04Controller::class, 'indexAction']);

// Ruta POST para buscar oficinas (método AJAX)
Route::post('/oficina/buscar', [Mercurio04Controller::class, 'buscarAction']);

// Ruta GET para editar oficinas (método AJAX, con parámetro opcional)
Route::get('/oficina/editar/{codofi?}', [Mercurio04Controller::class, 'editarAction']);

// Ruta POST para borrar oficinas (método AJAX)
Route::post('/oficina/borrar', [Mercurio04Controller::class, 'borrarAction']);

// Ruta POST para guardar oficinas (método AJAX)
Route::post('/oficina/guardar', [Mercurio04Controller::class, 'guardarAction']);

// Ruta POST para validar clave primaria (método AJAX)
Route::post('/oficina/valide-pk', [Mercurio04Controller::class, 'validePkAction']);

// Ruta GET para generar reportes de oficinas (con parámetro de formato)
Route::get('/oficina/reporte/{format?}', [Mercurio04Controller::class, 'reporteAction']);

// Ruta POST para validar clave primaria de ciudades (método AJAX)
Route::post('/oficina/valide-pk-ciudad', [Mercurio04Controller::class, 'validePkCiudadAction']);

// Ruta GET para vista de ciudades (método AJAX, con parámetro opcional)
Route::get('/oficina/ciudad-view/{codofi?}', [Mercurio04Controller::class, 'ciudad_viewAction']);

// Ruta POST para guardar ciudades (método AJAX)
Route::post('/oficina/guardar-ciudad', [Mercurio04Controller::class, 'guardarCiudadAction']);

// Ruta GET para editar ciudades (método AJAX)
Route::get('/oficina/editar-ciudad', [Mercurio04Controller::class, 'editarCiudadAction']);

// Ruta POST para borrar ciudades (método AJAX)
Route::post('/oficina/borrar-ciudad', [Mercurio04Controller::class, 'borrarCiudadAction']);

// Ruta POST para validar clave primaria de opciones (método AJAX)
Route::post('/oficina/valide-pk-opcion', [Mercurio04Controller::class, 'validePkOpcionAction']);

// Ruta GET para vista de opciones (método AJAX, con parámetro opcional)
Route::get('/oficina/opcion-view/{codofi?}', [Mercurio04Controller::class, 'opcion_viewAction']);

// Ruta POST para guardar opciones (método AJAX)
Route::post('/oficina/guardar-opcion', [Mercurio04Controller::class, 'guardarOpcionAction']);

// Ruta POST para borrar opciones (método AJAX)
Route::post('/oficina/borrar-opcion', [Mercurio04Controller::class, 'borrarOpcionAction']);
