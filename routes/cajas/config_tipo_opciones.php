<?php

use App\Http\Controllers\Cajas\Mercurio09Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio09')->group(function () {
        Route::get('/index', [Mercurio09Controller::class, 'index'])->name('cajas.mercurio09.index');
        Route::post('/', [Mercurio09Controller::class, 'store'])->name('cajas.mercurio09.store');
        Route::put('/{tipopc}', [Mercurio09Controller::class, 'update'])->name('cajas.mercurio09.update')->where('tipopc', '[A-Za-z0-9]{1,2}');
        Route::delete('/{tipopc}', [Mercurio09Controller::class, 'destroy'])->name('cajas.mercurio09.destroy')->where('tipopc', '[A-Za-z0-9]{1,2}');

        Route::get('/{tipopc}/documentos', [Mercurio09Controller::class, 'documentos'])->name('cajas.mercurio09.documentos')->where('tipopc', '[A-Za-z0-9]{1,2}');
        Route::post('/{tipopc}/documentos/trabajador', [Mercurio09Controller::class, 'syncTrabajador'])->name('cajas.mercurio09.documentos.trabajador')->where('tipopc', '[A-Za-z0-9]{1,2}');
        Route::post('/{tipopc}/documentos/empresa', [Mercurio09Controller::class, 'syncEmpresa'])->name('cajas.mercurio09.documentos.empresa')->where('tipopc', '[A-Za-z0-9]{1,2}');
    });
});
