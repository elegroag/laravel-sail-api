<?php

use App\Http\Controllers\Cajas\Mercurio02Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio02')->group(function () {
        Route::get('/index', [Mercurio02Controller::class, 'index'])->name('cajas.mercurio02.index');
        Route::post('/', [Mercurio02Controller::class, 'store'])->name('cajas.mercurio02.store');
        Route::put('/{codcaj}', [Mercurio02Controller::class, 'update'])->name('cajas.mercurio02.update');
    });
});
