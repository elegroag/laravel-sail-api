<?php

use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

# Firmas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio')->group(function () {
        Route::get('/firmas/index', [FirmasController::class, 'indexAction'])->name('firmas.index');
        Route::post('/firmas/guardar', [FirmasController::class, 'guardarAction'])->name('firmas.guardar');
        Route::get('/firmas/show', [FirmasController::class, 'showAction'])->name('firmas.show');
    });
});
