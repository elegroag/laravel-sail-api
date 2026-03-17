<?php

use App\Http\Controllers\Mercurio\FirmasController;
use Illuminate\Support\Facades\Route;

// Firmas
Route::middleware(['mercurio.auth'])->group(function () {
    Route::prefix('/mercurio/firmas')->group(function () {
        Route::get('/', function () {
            return redirect()->route('firmas.index');
        });
        Route::get('/index', [FirmasController::class, 'index'])->name('firmas.index');
        Route::post('/guardar', [FirmasController::class, 'guardar'])->name('firmas.guardar');
        Route::get('/show', [FirmasController::class, 'show'])->name('firmas.show');
    });
});
