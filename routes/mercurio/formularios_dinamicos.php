<?php

use App\Http\Controllers\Mercurio\FormularioDinamicoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Formularios Dinámicos Routes
|--------------------------------------------------------------------------
|
| Rutas para la gestión de formularios dinámicos
|
*/

Route::middleware(['auth'])->group(function () {
    // CRUD principal
    Route::resource('formulario-dinamico', FormularioDinamicoController::class)->parameters([
        'formulario-dinamico' => 'formulario'
    ]);

    // Rutas adicionales
    Route::get('formulario-dinamico/options', [FormularioDinamicoController::class, 'options'])
        ->name('formulario-dinamico.options');

    Route::patch('formulario-dinamico/{id}/toggle-active', [FormularioDinamicoController::class, 'toggleActive'])
        ->name('formulario-dinamico.toggle-active');

    Route::post('formulario-dinamico/{id}/duplicate', [FormularioDinamicoController::class, 'duplicate'])
        ->name('formulario-dinamico.duplicate');
});
