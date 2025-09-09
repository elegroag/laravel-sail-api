<?php

use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

# Firmas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    // Firmas (migrado desde Kumbia)
    Route::get('mercurio/firmas/index', [FirmasController::class, 'indexAction'])->name('firmas.index');
    Route::post('mercurio/firmas/guardar', [FirmasController::class, 'guardarAction'])->name('firmas.guardar');
    Route::get('mercurio/firmas/show', [FirmasController::class, 'showAction'])->name('firmas.show');
});
