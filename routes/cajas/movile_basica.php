<?php

use App\Http\Controllers\Cajas\Mercurio50Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio50')->group(function () {
        Route::get('/index', [Mercurio50Controller::class, 'index'])->name('cajas.mercurio50.index');
        Route::post('/', [Mercurio50Controller::class, 'store'])->name('cajas.mercurio50.store');
        Route::put('/{codapl}', [Mercurio50Controller::class, 'update'])->name('cajas.mercurio50.update');
    });
});
