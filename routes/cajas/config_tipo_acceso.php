<?php

use App\Http\Controllers\Cajas\Mercurio06Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/mercurio06')->group(function () {
        Route::get('/index', [Mercurio06Controller::class, 'index'])->name('cajas.mercurio06.index');
        Route::post('/', [Mercurio06Controller::class, 'store'])->name('cajas.mercurio06.store');
        Route::put('/{tipo}', [Mercurio06Controller::class, 'update'])->name('cajas.mercurio06.update');
        Route::delete('/{tipo}', [Mercurio06Controller::class, 'destroy'])->name('cajas.mercurio06.destroy');

        Route::post('/{tipo}/campos', [Mercurio06Controller::class, 'storeCampo'])->name('cajas.mercurio06.campos.store');
        Route::put('/{tipo}/campos/{campo}', [Mercurio06Controller::class, 'updateCampo'])->name('cajas.mercurio06.campos.update');
        Route::delete('/{tipo}/campos/{campo}', [Mercurio06Controller::class, 'destroyCampo'])->name('cajas.mercurio06.campos.destroy');
    });
});
