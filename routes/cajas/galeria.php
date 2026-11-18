<?php

use App\Http\Controllers\Cajas\Mercurio26Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio26')->group(function () {
        Route::get('/index', [Mercurio26Controller::class, 'index'])->name('cajas.mercurio26.index');
        Route::post('/', [Mercurio26Controller::class, 'store'])->name('cajas.mercurio26.store');
        Route::delete('/{numero}', [Mercurio26Controller::class, 'destroy'])->name('cajas.mercurio26.destroy')->whereNumber('numero');
        Route::post('/{numero}/arriba', [Mercurio26Controller::class, 'moveUp'])->name('cajas.mercurio26.moveUp')->whereNumber('numero');
        Route::post('/{numero}/abajo', [Mercurio26Controller::class, 'moveDown'])->name('cajas.mercurio26.moveDown')->whereNumber('numero');
    });
});
