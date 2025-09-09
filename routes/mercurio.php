<?php

use App\Http\Controllers\Mercurio\ConsultasEmpresaController;
use App\Http\Controllers\Mercurio\LoginController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Controllers\Mercurio\NotificacionesController;
use App\Http\Controllers\Mercurio\ParticularController;
use App\Http\Controllers\Mercurio\SubsidioController;
use App\Http\Controllers\Mercurio\SubsidioempController;
use App\Http\Controllers\Mercurio\UsuarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/mercurio/login', [LoginController::class, 'indexAction'])->name('login');
Route::post('/mercurio/autenticar', [LoginController::class, 'authenticateAction']);
Route::post('/mercurio/salir', [LoginController::class, 'logoutAction'])->name('login.salir');
Route::get('/mercurio/salir', [LoginController::class, 'logoutAction']);

Route::post('/mercurio/recuperar_clave', [LoginController::class, 'recuperarClaveAction']);
Route::post('/mercurio/registro', [LoginController::class, 'registroAction']);
Route::post('/mercurio/paramsLogin', [LoginController::class, 'paramsLoginAction']);

Route::get('/mercurio/show_registro', [LoginController::class, 'showRegisterAction'])->name('register');
Route::get('/mercurio/fuera_servicio', [LoginController::class, 'fueraServicioAction']);

Route::post('/mercurio/verify', [LoginController::class, 'verifyAction']);
Route::post('/mercurio/tokenParticular', [LoginController::class, 'tokenParticularAction']);
Route::post('/mercurio/cambio_correo', [LoginController::class, 'cambioCorreoAction']);

Route::post('/mercurio/valida_email', [LoginController::class, 'validaEmailAction']);
Route::get('/mercurio/integracion_servicio', [LoginController::class, 'integracionServicioAction']);
Route::get('/mercurio/guia_videos', [LoginController::class, 'guiaVideosAction']);
Route::post('/mercurio/download_docs/{archivo}', [LoginController::class, 'downloadDocumentsAction']);
Route::post('/mercurio/documentos/ver-pdf', [LoginController::class, 'showPdfAction'])->name('documentos.ver-pdf');
Route::post('mercurio/principal/ingreso_dirigido', [PrincipalController::class, 'ingresoDirigidoAction']);

// Movimientos
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/usuario/index', [UsuarioController::class, 'indexAction'])->name('usuario.index');
    Route::get('/mercurio/movimientos/historial', [MovimientosController::class, 'historialAction'])->name('movimientos.historial');

    Route::get('/mercurio/notificaciones/index', [NotificacionesController::class, 'indexAction'])->name('notificaciones.index');
    Route::post('/mercurio/notificaciones/procesar_notificacion', [NotificacionesController::class, 'procesarNotificacionAction']);
    Route::post('/mercurio/usuario/show_perfil', [UsuarioController::class, 'showPerfilAction']);
    Route::post('/mercurio/usuario/params', [UsuarioController::class, 'paramsAction']);
    Route::post('/mercurio/usuario/guardar', [UsuarioController::class, 'guardarAction']);
});

//Consultas de empresas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/subsidioemp/consulta_trabajadores_view', [ConsultasEmpresaController::class, 'consultaTrabajadoresViewAction']);
    Route::get('/mercurio/subsidioemp/consulta_giro_view', [ConsultasEmpresaController::class, 'consultaGiroViewAction']);
    Route::get('/mercurio/subsidioemp/consulta_aportes_view', [ConsultasEmpresaController::class, 'consultaAportesViewAction']);
    Route::get('/mercurio/subsidioemp/consulta_nomina_view', [ConsultasEmpresaController::class, 'consultaNominaViewAction']);

    Route::post('/mercurio/consulta_nomina', [ConsultasEmpresaController::class, 'consultaNominaAction']);
    Route::post('/mercurio/consulta_aportes', [ConsultasEmpresaController::class, 'consultaAportesAction']);
    Route::post('/mercurio/consulta_giro', [ConsultasEmpresaController::class, 'consultaGiroAction']);
    Route::post('/mercurio/consulta_trabajadores', [ConsultasEmpresaController::class, 'consultaTrabajadoresAction']);

    Route::get('/mercurio/subsidioemp/historial', [ConsultasEmpresaController::class, 'historialAction'])->name('subsidioemp.historial');
    Route::get('/mercurio/subsidio/historial', [ConsultasEmpresaController::class, 'historialAction'])->name('subsidio.historial');
    Route::get('/mercurio/particular/historial', [ParticularController::class, 'historialAction'])->name('particular.historial');
});
