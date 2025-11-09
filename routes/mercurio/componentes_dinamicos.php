<?php

use App\Http\Controllers\Mercurio\ComponenteDinamicoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Componentes Dinámicos Routes
|--------------------------------------------------------------------------
|
| Rutas para la gestión de componentes dinámicos
|
*/

Route::middleware(['auth'])->group(function () {
    // CRUD principal
    Route::resource('componente-dinamico', ComponenteDinamicoController::class)->parameters([
        'componente-dinamico' => 'componente'
    ]);

    // Rutas adicionales
    Route::get('componente-dinamico/options', [ComponenteDinamicoController::class, 'options'])
        ->name('componente-dinamico.options');

    Route::post('componente-dinamico/reorder', [ComponenteDinamicoController::class, 'reorder'])
        ->name('componente-dinamico.reorder');

    Route::post('componente-dinamico/{id}/duplicate', [ComponenteDinamicoController::class, 'duplicate'])
        ->name('componente-dinamico.duplicate');

    Route::get('componente-dinamico/by-formulario/{formularioId}', [ComponenteDinamicoController::class, 'byFormulario'])
        ->name('componente-dinamico.by-formulario');
});
