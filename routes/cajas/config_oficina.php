<?php

use App\Http\Controllers\Cajas\Mercurio04Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio04')->group(function () {
        Route::get('/index', [Mercurio04Controller::class, 'index'])->name('cajas.mercurio04.index');
        Route::post('/', [Mercurio04Controller::class, 'store'])->name('cajas.mercurio04.store');
        Route::put('/{codofi}', [Mercurio04Controller::class, 'update'])->name('cajas.mercurio04.update')->where('codofi', '[A-Za-z0-9]{1,2}');
        Route::delete('/{codofi}', [Mercurio04Controller::class, 'destroy'])->name('cajas.mercurio04.destroy')->where('codofi', '[A-Za-z0-9]{1,2}');

        Route::get('/{codofi}/opciones', [Mercurio04Controller::class, 'opciones'])->name('cajas.mercurio04.opciones')->where('codofi', '[A-Za-z0-9]{1,2}');
        Route::post('/{codofi}/opciones', [Mercurio04Controller::class, 'storeOpcion'])->name('cajas.mercurio04.opciones.store')->where('codofi', '[A-Za-z0-9]{1,2}');
        Route::delete('/{codofi}/opciones', [Mercurio04Controller::class, 'destroyOpcion'])->name('cajas.mercurio04.opciones.destroy')->where('codofi', '[A-Za-z0-9]{1,2}');
    });
});
