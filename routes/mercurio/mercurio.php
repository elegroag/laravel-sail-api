<?php

use App\Http\Controllers\Mercurio\AuthController;
use App\Http\Controllers\Mercurio\LoginController;
use App\Http\Controllers\Mercurio\NotificacionesController;
use App\Http\Controllers\Mercurio\ParticularController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Controllers\Mercurio\UsuarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/web/login', [AuthController::class, 'index'])->name('login');
Route::post('/web/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::get('/web/register', [AuthController::class, 'register'])->name('register');
Route::get('/web/password/request', [AuthController::class, 'resetPassword'])->name('password.request');
Route::get('/web/verify/{tipo}/{coddoc}/{documento}', [AuthController::class, 'verify'])->name('verify.show');
Route::post('/web/verify', [AuthController::class, 'verify'])->name('verify.request');
Route::post('/web/verify_action', [AuthController::class, 'verifyAction'])->name('verify.action');
Route::post('/web/load_session', [AuthController::class, 'loadSession'])->name('load.session');
Route::post('/web/salir', [AuthController::class, 'logoutAction'])->name('login.salir');
Route::get('/web/salir', [AuthController::class, 'logoutAction'])->name('logout');

Route::post('/mercurio/recuperar_clave', [LoginController::class, 'recuperarClaveAction']);
Route::post('/mercurio/registro', [LoginController::class, 'registroAction']);
Route::post('/mercurio/paramsLogin', [LoginController::class, 'paramsLoginAction']);

Route::get('/mercurio/show_registro', [LoginController::class, 'showRegisterAction'])->name('mercurio.register');
Route::get('/mercurio/fuera_servicio', [LoginController::class, 'fueraServicioAction']);

Route::post('/mercurio/tokenParticular', [LoginController::class, 'tokenParticularAction']);
Route::post('/mercurio/cambio_correo', [LoginController::class, 'cambioCorreoAction']);

Route::post('/mercurio/valida_email', [LoginController::class, 'validaEmailAction']);
Route::get('/mercurio/integracion_servicio', [LoginController::class, 'integracionServicioAction']);
Route::post('/mercurio/download_docs/{archivo}', [LoginController::class, 'downloadDocumentsAction']);
Route::post('/mercurio/documentos/ver-pdf', [LoginController::class, 'showPdfAction'])->name('documentos.ver-pdf');
Route::post('mercurio/principal/ingreso_dirigido', [PrincipalController::class, 'ingresoDirigidoAction']);

// Movimientos
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/usuario/index', [UsuarioController::class, 'indexAction'])->name('usuario.index');
    Route::get('/mercurio/notificaciones/index', [NotificacionesController::class, 'indexAction'])->name('mercurio.notificaciones.index');
    Route::post('/mercurio/notificaciones/procesar_notificacion', [NotificacionesController::class, 'procesarNotificacionAction']);
    Route::post('/mercurio/usuario/show_perfil', [UsuarioController::class, 'showPerfilAction']);
    Route::post('/mercurio/usuario/params', [UsuarioController::class, 'paramsAction']);
    Route::post('/mercurio/usuario/guardar', [UsuarioController::class, 'guardarAction']);
    Route::get('/mercurio/particular/historial', [ParticularController::class, 'historialAction'])->name('particular.historial');
});
