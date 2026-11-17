<?php

use App\Http\Controllers\Cajas\Mercurio13Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio13')->group(function () {
        Route::get('/index', [Mercurio13Controller::class, 'index'])->name('cajas.mercurio13.index');
        Route::post('/', [Mercurio13Controller::class, 'store'])->name('cajas.mercurio13.store');
        Route::put('/{tipopc}/{coddoc}', [Mercurio13Controller::class, 'update'])->name('cajas.mercurio13.update');
        Route::delete('/{tipopc}/{coddoc}', [Mercurio13Controller::class, 'destroy'])->name('cajas.mercurio13.destroy');
    });
});
