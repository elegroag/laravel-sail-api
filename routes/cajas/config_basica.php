<?php

use App\Http\Controllers\Cajas\Mercurio01Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio01')->group(function () {
        Route::get('/index', [Mercurio01Controller::class, 'index'])->name('cajas.mercurio01.index');
        Route::post('/', [Mercurio01Controller::class, 'store'])->name('cajas.mercurio01.store');
        Route::put('/{codapl}', [Mercurio01Controller::class, 'update'])->name('cajas.mercurio01.update');
    });
});
