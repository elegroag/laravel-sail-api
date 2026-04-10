<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\ComandoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {

    Route::prefix('/cajas/comando')->group(function () {
        // Ruta para obtener el estado de un comando
        Route::get('/status', [ComandoController::class, 'statusComando']);

        // Ruta para listar comandos con filtros
        Route::get('/listar', [ComandoController::class, 'listarComandos']);

        // Ruta para obtener el resultado de un comando
        Route::get('/resultado', [ComandoController::class, 'resultadoComando']);
    });
});
