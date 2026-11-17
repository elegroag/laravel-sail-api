<?php

use App\Http\Controllers\Cajas\Mercurio11Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio11')->group(function () {
        Route::get('/index', [Mercurio11Controller::class, 'index'])->name('cajas.mercurio11.index');
        Route::post('/', [Mercurio11Controller::class, 'store'])->name('cajas.mercurio11.store');
        Route::put('/{codest}', [Mercurio11Controller::class, 'update'])->name('cajas.mercurio11.update')->where('codest', '[A-Za-z0-9]{2}');
        Route::delete('/{codest}', [Mercurio11Controller::class, 'destroy'])->name('cajas.mercurio11.destroy')->where('codest', '[A-Za-z0-9]{2}');
    });
});
