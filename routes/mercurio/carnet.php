<?php

use App\Http\Controllers\Mercurio\CarnetController;
use App\Http\Controllers\Web\CarnetVerificacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['mercurio.auth'])->group(function () {
    Route::get('mercurio/carnet', [CarnetController::class, 'index'])->name('carnet.index');
    Route::get('mercurio/carnet/descargar/{formato}', [CarnetController::class, 'descargar'])
        ->whereIn('formato', ['pdf', 'png'])
        ->middleware('throttle:10,1')
        ->name('carnet.descargar');
    Route::post('mercurio/carnet/enviar/{formato}', [CarnetController::class, 'enviar'])
        ->whereIn('formato', ['pdf', 'png'])
        ->middleware('throttle:10,1')
        ->name('carnet.enviar');
});

Route::get('web/carnet/verificar/{token}', [CarnetVerificacionController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{24}')
    ->middleware('throttle:30,1')
    ->name('carnet.verificar');
