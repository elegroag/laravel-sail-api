<?php

use App\Http\Controllers\Cajas\Mercurio53Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio53')->group(function () {
        Route::get('/index', [Mercurio53Controller::class, 'index'])->name('cajas.mercurio53.index');
        Route::post('/', [Mercurio53Controller::class, 'store'])->name('cajas.mercurio53.store');
        Route::post('/{numero}/arriba', [Mercurio53Controller::class, 'moveUp'])->name('cajas.mercurio53.moveUp')->whereNumber('numero');
        Route::post('/{numero}/abajo', [Mercurio53Controller::class, 'moveDown'])->name('cajas.mercurio53.moveDown')->whereNumber('numero');
        Route::post('/{numero}', [Mercurio53Controller::class, 'update'])->name('cajas.mercurio53.update')->whereNumber('numero');
        Route::delete('/{numero}', [Mercurio53Controller::class, 'destroy'])->name('cajas.mercurio53.destroy')->whereNumber('numero');
    });
});
