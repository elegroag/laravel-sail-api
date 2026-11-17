<?php

use App\Http\Controllers\Cajas\Mercurio12Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio12')->group(function () {
        Route::get('/index', [Mercurio12Controller::class, 'index'])->name('cajas.mercurio12.index');
        Route::post('/', [Mercurio12Controller::class, 'store'])->name('cajas.mercurio12.store');
        Route::put('/{coddoc}', [Mercurio12Controller::class, 'update'])->name('cajas.mercurio12.update');
        Route::delete('/{coddoc}', [Mercurio12Controller::class, 'destroy'])->name('cajas.mercurio12.destroy');
    });
});
