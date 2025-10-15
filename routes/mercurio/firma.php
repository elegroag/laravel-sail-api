<?php

use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Firmas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/firmas')->group(function () {
        Route::get('/', function () {
            return redirect()->route('firmas.index');
        });
        Route::get('/index', [FirmasController::class, 'indexAction'])->name('firmas.index');
        Route::post('/guardar', [FirmasController::class, 'guardarAction'])->name('firmas.guardar');
        Route::get('/show', [FirmasController::class, 'showAction'])->name('firmas.show');
    });
});
