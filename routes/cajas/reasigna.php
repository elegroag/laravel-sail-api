<?php

use App\Http\Controllers\Cajas\ReasignaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/reasigna')->group(function () {
        Route::get('/', [ReasignaController::class, 'index'])->name('reasigna');
        Route::get('/index', [ReasignaController::class, 'index'])->name('reasigna.index');
        Route::post('/proceso_reasignar_masivo', [ReasignaController::class, 'procesoReasignarMasivo'])->name('reasigna.proceso');
        Route::post('/cambiar_usuario', [ReasignaController::class, 'cambiarUsuario'])->name('reasigna.cambiar_usuario');
    });
});
