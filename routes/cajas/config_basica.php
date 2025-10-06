<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio01Controller;

Route::prefix('/cajas')->group(function () {
    Route::get('/mercurio01/index', [Mercurio01Controller::class, 'indexAction']);

    Route::post('/mercurio01/buscar', [Mercurio01Controller::class, 'buscarAction']);

    Route::get('/mercurio01/editar', [Mercurio01Controller::class, 'editarAction']);

    Route::post('/mercurio01/guardar', [Mercurio01Controller::class, 'guardarAction']);
});
