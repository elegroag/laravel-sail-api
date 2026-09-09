<?php

use App\Http\Controllers\Cajas\EpaycoCuentaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/epayco-cuentas')->group(function () {
        Route::get('/index', [EpaycoCuentaController::class, 'index']);
        Route::post('/buscar-cuenta', [EpaycoCuentaController::class, 'buscarCuenta']);
        Route::post('/editar', [EpaycoCuentaController::class, 'editar']);
        Route::post('/guardar', [EpaycoCuentaController::class, 'guardar']);
        Route::post('/borrar', [EpaycoCuentaController::class, 'borrar']);
    });
});
