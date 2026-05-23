<?php

use App\Http\Controllers\MercurioV2\InicioController;
use Illuminate\Support\Facades\Route;

Route::prefix('mercurio-v2')->group(function () {
    Route::middleware(['mercurio.auth'])->group(function () {
        Route::get('/', [InicioController::class, 'index'])->name('mercurio-v2.inicio');
        Route::get('/inicio', [InicioController::class, 'index'])->name('mercurio-v2.inicio.index');
    });
});
