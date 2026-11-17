<?php

use App\Http\Controllers\Cajas\Mercurio14Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio14')->group(function () {
        Route::get('/index', [Mercurio14Controller::class, 'index'])->name('cajas.mercurio14.index');
        Route::post('/', [Mercurio14Controller::class, 'store'])->name('cajas.mercurio14.store');
        Route::put('/{tipopc}/{tipsoc}/{coddoc}', [Mercurio14Controller::class, 'update'])->name('cajas.mercurio14.update');
        Route::delete('/{tipopc}/{tipsoc}/{coddoc}', [Mercurio14Controller::class, 'destroy'])->name('cajas.mercurio14.destroy');
    });
});
