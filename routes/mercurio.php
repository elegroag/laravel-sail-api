<?php

use App\Http\Controllers\Mercurio\LoginController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Controllers\Mercurio\TrabajadorController;
use App\Http\Controllers\Mercurio\EmpresaController;
use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/mercurio/login', [LoginController::class, 'indexAction'])->name('login');
Route::post('/mercurio/autenticar', [LoginController::class, 'authenticateAction']);
Route::post('/mercurio/salir', [LoginController::class, 'logoutAction'])->name('login.salir');
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

# Principal 
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    
    Route::get('/principal/index', [PrincipalController::class, 'indexAction'])->name('principal.index');
    Route::get('/principal/dashboard_trabajador', [PrincipalController::class, 'dashboardTrabajadorAction'])->name('principal.dashboard_trabajador');
    Route::get('/principal/dashboard_empresa', [PrincipalController::class, 'dashboardEmpresaAction'])->name('principal.dashboard_empresa');

    Route::post('/principal/file_existe_global', [PrincipalController::class, 'fileExisteGlobalAction']);
    Route::post('/principal/traer_aportes_empresa', [PrincipalController::class, 'traerAportesEmpresaAction']);
    Route::post('/principal/traer_giro_empresa', [PrincipalController::class, 'traerGiroEmpresaAction']);
    Route::post('/principal/traer_categorias_empresa', [PrincipalController::class, 'traerCategoriasEmpresaAction']);
    Route::post('/principal/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
    Route::post('/principal/traer_giros_trabajador', [PrincipalController::class, 'traerGirosTrabajadorAction']);
    Route::post('/principal/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);

    Route::post('/principal/valida_syncro', [PrincipalController::class, 'validaSyncroAction'])->name('valida_syncro');
    Route::post('/principal/servicios', [PrincipalController::class, 'serviciosAction'])->name('servicios');

    Route::get('/movimientos/historial', [MovimientosController::class, 'historialAction'])->name('movimientos.historial');
    Route::get('/movimientos/cambio_email_view', [MovimientosController::class, 'cambioEmailViewAction'])->name('movimientos.cambio_email_view');
    Route::get('/movimientos/cambio_clave_view', [MovimientosController::class, 'cambioClaveViewAction'])->name('movimientos.cambio_clave_view');
    
    // Firmas (migrado desde Kumbia)
    Route::get('/firmas/index', [FirmasController::class, 'indexAction'])->name('firmas.index');
    Route::post('/firmas/guardar', [FirmasController::class, 'guardarAction'])->name('firmas.guardar');
    Route::get('/firmas/show', [FirmasController::class, 'showAction'])->name('firmas.show');
});

Route::post('/principal/ingreso_dirigido', [PrincipalController::class, 'ingresoDirigidoAction']);

// Trabajador (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::post('/trabajador/valide_nit', [TrabajadorController::class, 'valideNitAction']);
    Route::post('/trabajador/borrar_archivo', [TrabajadorController::class, 'borrarArchivoAction']);
    Route::post('/trabajador/guardar_archivo', [TrabajadorController::class, 'guardarArchivoAction']);
    Route::post('/trabajador/traer_trabajador', [TrabajadorController::class, 'traerTrabajadorAction']);
    Route::post('/trabajador/enviar_caja', [TrabajadorController::class, 'enviarCajaAction']);
    Route::get('/trabajador/seguimiento/{id}', [TrabajadorController::class, 'seguimientoAction']);
});

// Empresa (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::post('/empresa/buscar_empresa', [EmpresaController::class, 'buscarEmpresaAction']);
    Route::post('/empresa/guardar', [EmpresaController::class, 'guardarAction']);
    Route::post('/empresa/borrar_archivo', [EmpresaController::class, 'borrarArchivoAction']);
    Route::post('/empresa/guardar_archivo', [EmpresaController::class, 'guardarArchivoAction']);
    Route::get('/empresa/archivos_requeridos/{id}', [EmpresaController::class, 'archivosRequeridosAction']);
    Route::post('/empresa/enviar_caja', [EmpresaController::class, 'enviarCajaAction']);
    Route::get('/empresa/seguimiento/{id}', [EmpresaController::class, 'seguimientoAction']);
    Route::post('/empresa/params', [EmpresaController::class, 'paramsAction']);
    Route::get('/empresa/download_temp/{archivo}', [EmpresaController::class, 'downloadFileAction']);
    Route::get('/empresa/download_docs/{archivo}', [EmpresaController::class, 'downloadDocsAction']);
    Route::get('/empresa/digito_verification', [EmpresaController::class, 'digitoVerificationAction']);
    Route::get('/empresa/search_request/{id}', [EmpresaController::class, 'searchRequestAction']);
    Route::get('/empresa/consulta_documentos/{id}', [EmpresaController::class, 'consultaDocumentosAction']);
    Route::post('/empresa/borrar', [EmpresaController::class, 'borrarAction']);
});
