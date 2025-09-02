<?php

use App\Http\Controllers\Mercurio\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/mercurio/login/', [LoginController::class, 'index']);
Route::post('/mercurio/autenticar/', [LoginController::class, 'authenticateAction']);
Route::post('/mercurio/salir/', [LoginController::class, 'logoutAction']);
Route::post('/mercurio/recuperar_clave/', [LoginController::class, 'recuperarClaveAction']);
Route::post('/mercurio/registro/', [LoginController::class, 'registroAction']);
Route::get('/mercurio/fuera_servicio/', [LoginController::class, 'fueraServicioAction']);

Route::get('/mercurio/verify/', [LoginController::class, 'verifyAction']);
Route::get('/mercurio/tokenParticular/', [LoginController::class, 'tokenParticularAction']);
Route::get('/mercurio/cambio_correo/', [LoginController::class, 'cambioCorreoAction']);
Route::get('/mercurio/paramsLogin/', [LoginController::class, 'paramsLoginAction']);

Route::get('/mercurio/valida_email/', [LoginController::class, 'validaEmailAction']);
Route::get('/mercurio/integracion_servicio/', [LoginController::class, 'integracionServicioAction']);
Route::get('/mercurio/guia_videos/', [LoginController::class, 'guiaVideosAction']);
Route::get('/mercurio/download_docs/', [LoginController::class, 'downloadDocumentsAction']);