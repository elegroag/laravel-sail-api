<?php

use App\Http\Controllers\Cajas\ComponenteValidacionController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

//Route::middleware([CajasCookieAuthenticated::class])->group(function () {
Route::prefix('/cajas/componente-validacion')->group(function () {
    Route::get('/', [ComponenteValidacionController::class, 'index'])->name('componente-validacion');
    Route::get('/create', [ComponenteValidacionController::class, 'create'])->name('componente-validacion.create');
    Route::post('/', [ComponenteValidacionController::class, 'store'])->name('componente-validacion.store');
    Route::get('/{id}/show', [ComponenteValidacionController::class, 'show'])->name('componente-validacion.show');
    Route::get('/{id}/edit', [ComponenteValidacionController::class, 'edit'])->name('componente-validacion.edit');
    Route::put('/{id}', [ComponenteValidacionController::class, 'update'])->name('componente-validacion.update');
    Route::delete('/{id}', [ComponenteValidacionController::class, 'destroy'])->name('componente-validacion.destroy');

    Route::get('/options', [ComponenteValidacionController::class, 'options'])->name('componente-validacion.options');
    Route::get('/by-componente/{componenteId}', [ComponenteValidacionController::class, 'byComponente'])->name('componente-validacion.by-componente');
    Route::post('/{id}/duplicate', [ComponenteValidacionController::class, 'duplicate'])->name('componente-validacion.duplicate');
    Route::post('/validate-rules', [ComponenteValidacionController::class, 'validateRules'])->name('componente-validacion.validate-rules');
});
//});
