<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\ComandoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([CajasCookieAuthenticated::class])->group(function () {
    // Ruta para obtener el estado de un comando
    Route::get('/comando/status', [ComandoController::class, 'statusComando']);

    // Ruta para listar comandos con filtros
    Route::get('/comando/listar', [ComandoController::class, 'listarComandos']);

    // Ruta para obtener el resultado de un comando
    Route::get('/comando/resultado', [ComandoController::class, 'resultadoComando']);
});
