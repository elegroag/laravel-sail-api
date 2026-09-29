<?php

use App\Http\Controllers\Mercurio\CarnetController;
use App\Http\Controllers\Web\CarnetVerificacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::get('mercurio/carnet', [CarnetController::class, 'index'])->name('carnet.index');
});

Route::get('web/carnet/verificar/{token}', [CarnetVerificacionController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{24}')
    ->middleware('throttle:30,1')
    ->name('carnet.verificar');
