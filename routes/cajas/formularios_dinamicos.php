<?php

use App\Http\Controllers\Cajas\FormularioDinamicoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

//Route::middleware([CajasCookieAuthenticated::class])->group(function () {

Route::prefix('/cajas/formulario-dinamico')->group(function () {

    Route::get('/', [FormularioDinamicoController::class, 'index'])->name('formulario-dinamico');
    Route::get('/create', [FormularioDinamicoController::class, 'create'])->name('formulario-dinamico.create');
    Route::post('/', [FormularioDinamicoController::class, 'store'])->name('formulario-dinamico.store');
    Route::get('/{id}/show', [FormularioDinamicoController::class, 'show'])->name('formulario-dinamico.show');
    Route::get('/{id}/edit', [FormularioDinamicoController::class, 'edit'])->name('formulario-dinamico.edit');
    Route::put('/{id}', [FormularioDinamicoController::class, 'update'])->name('formulario-dinamico.update');
    Route::delete('/{id}', [FormularioDinamicoController::class, 'destroy'])->name('formulario-dinamico.destroy');

    Route::get('/options', [FormularioDinamicoController::class, 'options'])
        ->name('formulario-dinamico.options');

    Route::get('/{id}/children', [FormularioDinamicoController::class, 'children'])
        ->name('formulario-dinamico.children');

    Route::post('/{id}/toggle-active', [FormularioDinamicoController::class, 'toggleActive'])
        ->name('formulario-dinamico.toggle-active');

    Route::post('/{id}/duplicate', [FormularioDinamicoController::class, 'duplicate'])
        ->name('formulario-dinamico.duplicate');
});
//});
