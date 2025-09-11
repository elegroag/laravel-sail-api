<?php

use App\Http\Controllers\Cajas\AuthController;
use App\Http\Controllers\Cajas\PrincipalController;
use Illuminate\Support\Facades\Route;

Route::get('/cajas/login', [AuthController::class, 'indexAction'])->name('cajas.login');
Route::post('/cajas/autenticar', [AuthController::class, 'authenticateAction'])->name('cajas.autenticar');
Route::post('/cajas/salir', [AuthController::class, 'logoutAction'])->name('cajas.salir');
Route::post('/cajas/cambio_correo', [AuthController::class, 'cambioCorreoAction'])->name('cajas.cambio_correo');

Route::get('/cajas/principal', [PrincipalController::class, 'indexAction'])->name('cajas.principal');
Route::get('/cajas/dashboard', [PrincipalController::class, 'dashboardAction'])->name('cajas.dashboard');

Route::get('/cajas/traer_usuarios_registrados', [PrincipalController::class, 'traerUsuariosRegistradosAction']);
Route::get('/cajas/traer_opcion_mas_usuada', [PrincipalController::class, 'traerOpcionMasUsuadaAction']);
Route::get('/cajas/traer_motivo_mas_usuada', [PrincipalController::class, 'traerMotivoMasUsuadaAction']);
Route::get('/cajas/traer_carga_laboral', [PrincipalController::class, 'traerCargaLaboralAction']);
Route::get('/cajas/download_global', [PrincipalController::class, 'downloadGlobalAction']);
Route::get('/cajas/file_existe_global', [PrincipalController::class, 'fileExisteGlobalAction']);
