<?php

use App\Http\Controllers\Mercurio\LoginController;
use App\Http\Controllers\Mercurio\PrincipalController;
use Illuminate\Support\Facades\Route;

Route::get('/mercurio/login', [LoginController::class, 'indexAction'])->name('login');
Route::post('/mercurio/autenticar', [LoginController::class, 'authenticateAction']);
Route::post('/mercurio/salir', [LoginController::class, 'logoutAction']);
Route::post('/mercurio/recuperar_clave', [LoginController::class, 'recuperarClaveAction']);
Route::post('/mercurio/registro', [LoginController::class, 'registroAction']);
Route::post('/mercurio/paramsLogin', [LoginController::class, 'paramsLoginAction']);

Route::get('/mercurio/show_registro', [LoginController::class, 'showRegisterAction'])->name('register');
Route::get('/mercurio/fuera_servicio', [LoginController::class, 'fueraServicioAction']);

Route::get('/mercurio/verify', [LoginController::class, 'verifyAction']);
Route::get('/mercurio/tokenParticular', [LoginController::class, 'tokenParticularAction']);
Route::get('/mercurio/cambio_correo', [LoginController::class, 'cambioCorreoAction']);

Route::post('/mercurio/valida_email', [LoginController::class, 'validaEmailAction']);
Route::get('/mercurio/integracion_servicio', [LoginController::class, 'integracionServicioAction']);
Route::get('/mercurio/guia_videos', [LoginController::class, 'guiaVideosAction']);
Route::post('/mercurio/download_docs/{archivo}', [LoginController::class, 'downloadDocumentsAction']);


# Principal 
Route::get('/mercurio/principal', [PrincipalController::class, 'principalAction']);
Route::get('/mercurio/index', [PrincipalController::class, 'indexAction']);
Route::get('/mercurio/dashboard_trabajador', [PrincipalController::class, 'dashboardTrabajadorAction']);
Route::get('/mercurio/dashboard_empresa', [PrincipalController::class, 'dashboardEmpresaAction']);

Route::post('/mercurio/file_existe_global', [PrincipalController::class, 'fileExisteGlobalAction']);
Route::post('/mercurio/traer_aportes_empresa', [PrincipalController::class, 'traerAportesEmpresaAction']);
Route::post('/mercurio/traer_giro_empresa', [PrincipalController::class, 'traerGiroEmpresaAction']);
Route::post('/mercurio/traer_categorias_empresa', [PrincipalController::class, 'traerCategoriasEmpresaAction']);
Route::post('/mercurio/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
Route::post('/mercurio/traer_giros_trabajador', [PrincipalController::class, 'traerGirosTrabajadorAction']);
Route::post('/mercurio/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
