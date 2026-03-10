<?php

use App\Http\Controllers\Mercurio\LoginController;
use App\Http\Controllers\Mercurio\NotificacionesController;
use App\Http\Controllers\Mercurio\ParticularController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::post('/mercurio/recuperar_clave', [LoginController::class, 'recuperarClave']);
Route::post('/mercurio/registro', [LoginController::class, 'registro']);
Route::get('/mercurio/show_registro', [LoginController::class, 'showRegister'])->name('mercurio.register');
Route::get('/mercurio/fuera_servicio', [LoginController::class, 'fueraServicio']);
Route::post('/mercurio/tokenParticular', [LoginController::class, 'tokenParticular']);
Route::post('/mercurio/valida_email', [LoginController::class, 'validaEmail']);
Route::get('/mercurio/integracion_servicio', [LoginController::class, 'integracionServicio']);
Route::post('/mercurio/download_docs/{archivo}', [LoginController::class, 'downloadDocuments']);
Route::post('/mercurio/documentos/ver-pdf', [LoginController::class, 'showPdf'])->name('documentos.ver-pdf');
Route::post('/mercurio/principal/ingreso_dirigido', [PrincipalController::class, 'ingresoDirigido']);

// Movimientos
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/notificaciones/index', [NotificacionesController::class, 'index'])->name('mercurio.notificaciones.index');
    Route::post('/mercurio/notificaciones/procesar_notificacion', [NotificacionesController::class, 'procesarNotificacion']);
    Route::get('/mercurio/particular/historial', [ParticularController::class, 'historial'])->name('particular.historial');
});
