<?php

use App\Http\Controllers\Cajas\MenuPermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('cajas/menu-permission')->middleware(['auth', 'cors'])->group(function () {
    Route::get('/', [MenuPermissionController::class, 'index'])->name('menu-permission.index');
    Route::get('/create', [MenuPermissionController::class, 'create'])->name('menu-permission.create');
    Route::post('/', [MenuPermissionController::class, 'store'])->name('menu-permission.store');
    Route::get('/{id}/edit', [MenuPermissionController::class, 'edit'])->name('menu-permission.edit');
    Route::put('/{id}', [MenuPermissionController::class, 'update'])->name('menu-permission.update');
    Route::delete('/{id}', [MenuPermissionController::class, 'destroy'])->name('menu-permission.destroy');

    // API routes for index page interaction
    Route::get('/{menu_item_id}/permissions', [MenuPermissionController::class, 'permissions']);
    Route::post('/ajax', [MenuPermissionController::class, 'ajaxStore']);
});
