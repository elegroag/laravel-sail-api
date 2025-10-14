<?php

use App\Http\Controllers\Cajas\AuthController;
use App\Http\Controllers\Cajas\PrincipalController;
use Illuminate\Support\Facades\Route;

Route::prefix('/cajas')->group(function () {
    Route::get('/', function () {
        return redirect()->route('cajas.login');
    });
    Route::get('/login', [AuthController::class, 'indexAction'])->name('cajas.login');
    Route::post('/autenticar', [AuthController::class, 'authenticateAction'])->name('cajas.autenticar');
    Route::post('/salir', [AuthController::class, 'logoutAction'])->name('cajas.salir');
    Route::post('/cambio_correo', [AuthController::class, 'cambioCorreoAction'])->name('cajas.cambio_correo');
});

Route::prefix('/cajas/principal')->group(function () {
    Route::get('/index', [PrincipalController::class, 'indexAction'])->name('cajas.principal');
    Route::get('/dashboard', [PrincipalController::class, 'dashboardAction'])->name('cajas.dashboard');

    Route::post('/traer_usuarios_registrados', [PrincipalController::class, 'traerUsuariosRegistradosAction']);
    Route::post('/traer_opcion_mas_usada', [PrincipalController::class, 'traerOpcionMasUsuadaAction']);
    Route::post('/traer_motivo_mas_usada', [PrincipalController::class, 'traerMotivoMasUsuadaAction']);
    Route::post('/traer_carga_laboral', [PrincipalController::class, 'traerCargaLaboralAction']);
    Route::post('/download_global/{hash?}', [PrincipalController::class, 'downloadGlobalAction']);
    Route::post('/file_existe_global/{hash?}', [PrincipalController::class, 'fileExisteGlobalAction']);
});
