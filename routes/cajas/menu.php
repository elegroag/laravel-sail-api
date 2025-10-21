<?php

use App\Http\Controllers\Cajas\MenuController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cajas/menu')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('cajas.menu.index');
    Route::get('/create', [MenuController::class, 'create'])->name('cajas.menu.create');
    Route::post('/', [MenuController::class, 'store'])->name('cajas.menu.store');
    Route::get('/{id}/show', [MenuController::class, 'show'])->name('cajas.menu.show');
    Route::get('/{id}/edit', [MenuController::class, 'edit'])->name('cajas.menu.edit');
    Route::put('/{id}', [MenuController::class, 'update'])->name('cajas.menu.update');
    Route::delete('/{id}', [MenuController::class, 'destroy'])->name('cajas.menu.destroy');
    Route::get('/{id}/children', [MenuController::class, 'children'])->name('cajas.menu.children');
    Route::post('/options', [MenuController::class, 'options'])->name('cajas.menu.options');
    Route::post('/{id}/attach-child', [MenuController::class, 'attachChild'])->name('cajas.menu.attachChild');
});
