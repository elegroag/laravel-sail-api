<?php

use App\Http\Controllers\Cajas\MenuPermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cajas/menu_permission')->group(function () {
    Route::get('/', [MenuPermissionController::class, 'index'])->name('cajas.menu_permission.index');
    Route::get('/create', [MenuPermissionController::class, 'create'])->name('cajas.menu_permission.create');
    Route::post('/', [MenuPermissionController::class, 'store'])->name('cajas.menu_permission.store');
    Route::get('/{id}/edit', [MenuPermissionController::class, 'edit'])->name('cajas.menu_permission.edit');
    Route::put('/{id}', [MenuPermissionController::class, 'update'])->name('cajas.menu_permission.update');
    Route::delete('/{id}', [MenuPermissionController::class, 'destroy'])->name('cajas.menu_permission.destroy');

    // API routes for index page interaction
    Route::get('/{menu_item_id}/permissions', [MenuPermissionController::class, 'permissions']);
    Route::post('/ajax', [MenuPermissionController::class, 'ajaxStore']);
});
