<?php

use App\Http\Controllers\Mercurio\NotificacionesController;
use App\Http\Controllers\Mercurio\ParticularController;
use Illuminate\Support\Facades\Route;

Route::prefix('/mercurio/notificaciones')->group(function () {
    Route::middleware(['mercurio.auth'])->group(function () {
        Route::get('/index', [NotificacionesController::class, 'index'])->name('mercurio.notificaciones.index');
        Route::post('/procesar_notificacion', [NotificacionesController::class, 'procesarNotificacion']);
    });
});

Route::get('/particular/historial', [ParticularController::class, 'historial'])
    ->name('particular.historial')
    ->middleware(['mercurio.auth']);
