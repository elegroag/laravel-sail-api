<?php

use App\Http\Controllers\Cajas\ComponenteDinamicoController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

//Route::middleware([CajasCookieAuthenticated::class])->group(function () {
Route::prefix('/cajas/componente-dinamico')->group(function () {
    Route::get('/', [ComponenteDinamicoController::class, 'index'])->name('componente-dinamico');
    Route::get('/create', [ComponenteDinamicoController::class, 'create'])->name('componente-dinamico.create');
    Route::post('/', [ComponenteDinamicoController::class, 'store'])->name('componente-dinamico.store');
    Route::get('/{id}/show', [ComponenteDinamicoController::class, 'show'])->name('componente-dinamico.show');
    Route::get('/{id}/edit', [ComponenteDinamicoController::class, 'edit'])->name('componente-dinamico.edit');
    Route::put('/{id}', [ComponenteDinamicoController::class, 'update'])->name('componente-dinamico.update');
    Route::delete('/{id}', [ComponenteDinamicoController::class, 'destroy'])->name('componente-dinamico.destroy');

    Route::get('/options', [ComponenteDinamicoController::class, 'options'])->name('componente-dinamico.options');
    Route::post('/reorder', [ComponenteDinamicoController::class, 'reorder'])->name('componente-dinamico.reorder');
    Route::post('/{id}/duplicate', [ComponenteDinamicoController::class, 'duplicate'])->name('componente-dinamico.duplicate');
    Route::get('/{formularioId}/by-formulario', [ComponenteDinamicoController::class, 'byFormulario'])->name('componente-dinamico.by-formulario');
    Route::get('/{formularioId}/listar-por-formulario', [ComponenteDinamicoController::class, 'listarPorFormulario'])->name('componente-dinamico.listar-por-formulario');
});
//});
