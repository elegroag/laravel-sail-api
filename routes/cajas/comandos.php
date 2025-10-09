<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\ComandoController;
use Illuminate\Support\Facades\Route;

// Ruta para obtener el estado de un comando
Route::get('/comando/status', [ComandoController::class, 'statusComandoAction']);

// Ruta para listar comandos con filtros
Route::get('/comando/listar', [ComandoController::class, 'listarComandosAction']);

// Ruta para obtener el resultado de un comando
Route::get('/comando/resultado', [ComandoController::class, 'resultadoComandoAction']);
