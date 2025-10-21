<?php

use App\Http\Controllers\Cajas\AuthController;
use App\Http\Controllers\Cajas\PrincipalController;
use App\Http\Controllers\Cajas\MenuController;
use App\Http\Middleware\CajasCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::prefix('/cajas')->group(function () {
    Route::get('/', function () {
        return redirect()->route('cajas.login');
    });
    Route::get('/salir', [AuthController::class, 'logout'])->name('cajas.salir');
    Route::get('/login', [AuthController::class, 'index'])->name('cajas.login');
    Route::post('/autenticar', [AuthController::class, 'authenticate'])->name('cajas.autenticar');
    Route::post('/cambio_correo', [AuthController::class, 'cambioCorreo'])->name('cajas.cambio_correo');
});

Route::middleware([CajasCookieAuthenticated::class], function () {
    Route::prefix('/cajas/principal')->group(function () {
        Route::get('/index', [PrincipalController::class, 'index'])->name('cajas.principal');
        Route::get('/dashboard', [PrincipalController::class, 'dashboard'])->name('cajas.dashboard');

        Route::post('/traer_usuarios_registrados', [PrincipalController::class, 'traerUsuariosRegistrados']);
        Route::post('/traer_opcion_mas_usada', [PrincipalController::class, 'traerOpcionMasUsuada']);
        Route::post('/traer_motivo_mas_usada', [PrincipalController::class, 'traerMotivoMasUsuada']);
        Route::post('/traer_carga_laboral', [PrincipalController::class, 'traerCargaLaboral']);
        Route::post('/download_global/{hash?}', [PrincipalController::class, 'downloadGlobal']);
        Route::post('/file_existe_global/{hash?}', [PrincipalController::class, 'fileExisteGlobal']);
    });
});

// Rutas de administración de Menú (CRUD)
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
