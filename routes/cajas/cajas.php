<?php

use App\Http\Controllers\Cajas\AuthController;
use App\Http\Controllers\Cajas\PrincipalController;
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
