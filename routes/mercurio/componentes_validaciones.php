<?php

use App\Http\Controllers\Mercurio\ComponenteValidacionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Componentes Validaciones Routes
|--------------------------------------------------------------------------
|
| Rutas para la gestión de validaciones de componentes dinámicos
|
*/

Route::middleware(['auth'])->group(function () {
    // CRUD principal
    Route::resource('componente-validacion', ComponenteValidacionController::class)->parameters([
        'componente-validacion' => 'validacion'
    ]);

    // Rutas adicionales
    Route::get('componente-validacion/options', [ComponenteValidacionController::class, 'options'])
        ->name('componente-validacion.options');

    Route::get('componente-validacion/by-componente/{componenteId}', [ComponenteValidacionController::class, 'byComponente'])
        ->name('componente-validacion.by-componente');

    Route::post('componente-validacion/{id}/duplicate', [ComponenteValidacionController::class, 'duplicate'])
        ->name('componente-validacion.duplicate');

    Route::post('componente-validacion/validate-rules', [ComponenteValidacionController::class, 'validateRules'])
        ->name('componente-validacion.validate-rules');
});
