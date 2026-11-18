<?php

use App\Http\Controllers\Cajas\Mercurio57Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio57')->group(function () {
        Route::get('/index', [Mercurio57Controller::class, 'index'])->name('cajas.mercurio57.index');
        Route::post('/', [Mercurio57Controller::class, 'store'])->name('cajas.mercurio57.store');
        Route::post('/{numpro}/arriba', [Mercurio57Controller::class, 'moveUp'])->name('cajas.mercurio57.moveUp')->whereNumber('numpro');
        Route::post('/{numpro}/abajo', [Mercurio57Controller::class, 'moveDown'])->name('cajas.mercurio57.moveDown')->whereNumber('numpro');
        Route::post('/{numpro}', [Mercurio57Controller::class, 'update'])->name('cajas.mercurio57.update')->whereNumber('numpro');
        Route::delete('/{numpro}', [Mercurio57Controller::class, 'destroy'])->name('cajas.mercurio57.destroy')->whereNumber('numpro');
    });
});
