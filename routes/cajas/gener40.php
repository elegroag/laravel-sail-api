<?php

use App\Http\Controllers\Cajas\Gener40Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/gener40')->group(function () {
        Route::get('/index', [Gener40Controller::class, 'index'])->name('cajas.gener40.index');
        Route::get('/exportar', [Gener40Controller::class, 'exportar'])->name('cajas.gener40.exportar');
        Route::post('/', [Gener40Controller::class, 'store'])->name('cajas.gener40.store');
        Route::put('/{codigo}', [Gener40Controller::class, 'update'])->name('cajas.gener40.update')->where('codigo', '[A-Za-z0-9]{1,5}');
        Route::delete('/{codigo}', [Gener40Controller::class, 'destroy'])->name('cajas.gener40.destroy')->where('codigo', '[A-Za-z0-9]{1,5}');
    });
});
