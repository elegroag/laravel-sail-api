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

    Route::get('mercurio/principal/index', [PrincipalController::class, 'indexAction'])->name('principal.index');
    Route::get('mercurio/principal/dashboard_trabajador', [PrincipalController::class, 'dashboardTrabajadorAction'])->name('principal.dashboard_trabajador');
    Route::get('mercurio/principal/dashboard_empresa', [PrincipalController::class, 'dashboardEmpresaAction'])->name('principal.dashboard_empresa');

    Route::post('mercurio/principal/file_existe_global', [PrincipalController::class, 'fileExisteGlobalAction']);
    Route::post('mercurio/principal/traer_aportes_empresa', [PrincipalController::class, 'traerAportesEmpresaAction']);
    Route::post('mercurio/principal/traer_giro_empresa', [PrincipalController::class, 'traerGiroEmpresaAction']);
    Route::post('mercurio/principal/traer_categorias_empresa', [PrincipalController::class, 'traerCategoriasEmpresaAction']);
    Route::post('mercurio/principal/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
    Route::post('mercurio/principal/traer_giros_trabajador', [PrincipalController::class, 'traerGirosTrabajadorAction']);
    Route::post('mercurio/principal/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);

    Route::post('mercurio/principal/valida_syncro', [PrincipalController::class, 'validaSyncroAction'])->name('valida_syncro');
    Route::post('mercurio/principal/servicios', [PrincipalController::class, 'serviciosAction'])->name('servicios');
    Route::post('mercurio/principal/listaAdress', [PrincipalController::class, 'listaAdressAction']);

    Route::get('mercurio/movimientos/historial', [MovimientosController::class, 'historialAction'])->name('movimientos.historial');
    Route::get('mercurio/movimientos/cambio_email_view', [MovimientosController::class, 'cambioEmailViewAction'])->name('movimientos.cambio_email_view');
    Route::get('mercurio/movimientos/cambio_clave_view', [MovimientosController::class, 'cambioClaveViewAction'])->name('movimientos.cambio_clave_view');

    // Firmas (migrado desde Kumbia)
    Route::get('mercurio/firmas/index', [FirmasController::class, 'indexAction'])->name('firmas.index');
    Route::post('mercurio/firmas/guardar', [FirmasController::class, 'guardarAction'])->name('firmas.guardar');
    Route::get('mercurio/firmas/show', [FirmasController::class, 'showAction'])->name('firmas.show');
});

Route::post('mercurio/principal/ingreso_dirigido', [PrincipalController::class, 'ingresoDirigidoAction']);

// Trabajador (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::post('mercurio/trabajador/valide_nit', [TrabajadorController::class, 'valideNitAction']);
    Route::post('mercurio/trabajador/borrar_archivo', [TrabajadorController::class, 'borrarArchivoAction']);
    Route::post('mercurio/trabajador/guardar_archivo', [TrabajadorController::class, 'guardarArchivoAction']);
    Route::post('mercurio/trabajador/traer_trabajador', [TrabajadorController::class, 'traerTrabajadorAction']);
    Route::post('mercurio/trabajador/enviar_caja', [TrabajadorController::class, 'enviarCajaAction']);
    Route::get('mercurio/trabajador/seguimiento/{id}', [TrabajadorController::class, 'seguimientoAction']);
});

// Empresa (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/empresa/index', [EmpresaController::class, 'indexAction']);
    Route::post('/mercurio/empresa/buscar_empresa', [EmpresaController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/empresa/guardar', [EmpresaController::class, 'guardarAction']);
    Route::post('/mercurio/empresa/borrar_archivo', [EmpresaController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/empresa/guardar_archivo', [EmpresaController::class, 'guardarArchivoAction']);
    Route::get('/mercurio/empresa/archivos_requeridos/{id}', [EmpresaController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/empresa/enviar_caja', [EmpresaController::class, 'enviarCajaAction']);
    Route::get('/mercurio/empresa/seguimiento/{id}', [EmpresaController::class, 'seguimientoAction']);
    Route::post('/mercurio/empresa/params', [EmpresaController::class, 'paramsAction']);
    Route::get('/mercurio/empresa/download_temp/{archivo}', [EmpresaController::class, 'downloadFileAction']);
    Route::get('/mercurio/empresa/download_docs/{archivo}', [EmpresaController::class, 'downloadDocsAction']);

    Route::get('/mercurio/empresa/search_request/{id}', [EmpresaController::class, 'searchRequestAction']);
    Route::get('/mercurio/empresa/consulta_documentos/{id}', [EmpresaController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/empresa/borrar', [EmpresaController::class, 'borrarAction']);
    Route::post('/mercurio/empresas/params', [EmpresaController::class, 'paramsAction']);
    Route::post('/mercurio/empresa/renderTable', [EmpresaController::class, 'renderTableAction']);
    Route::post('/mercurio/empresa/renderTable/{estado}', [EmpresaController::class, 'renderTableAction']);

    Route::post('/mercurio/empresa/valida', [EmpresaController::class, 'validaAction']);
    Route::post('/mercurio/empresa/digito_verification', [EmpresaController::class, 'digitoVerificationAction']);
});


// Trabajador (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::post('mercurio/trabajador/buscar_trabajador', [TrabajadorController::class, 'buscarTrabajadorAction']);
    Route::post('mercurio/trabajador/guardar', [TrabajadorController::class, 'guardarAction']);
    Route::post('mercurio/trabajador/borrar_archivo', [TrabajadorController::class, 'borrarArchivoAction']);
    Route::post('mercurio/trabajador/guardar_archivo', [TrabajadorController::class, 'guardarArchivoAction']);
    Route::get('mercurio/trabajador/archivos_requeridos/{id}', [TrabajadorController::class, 'archivosRequeridosAction']);
    Route::post('mercurio/trabajador/enviar_caja', [TrabajadorController::class, 'enviarCajaAction']);
    Route::get('mercurio/trabajador/seguimiento/{id}', [TrabajadorController::class, 'seguimientoAction']);
    Route::post('mercurio/trabajador/params', [TrabajadorController::class, 'paramsAction']);
    Route::get('mercurio/trabajador/download_temp/{archivo}', [TrabajadorController::class, 'downloadFileAction']);
    Route::get('mercurio/trabajador/download_docs/{archivo}', [TrabajadorController::class, 'downloadDocsAction']);
    Route::get('mercurio/trabajador/digito_verification', [TrabajadorController::class, 'digitoVerificationAction']);
    Route::get('mercurio/trabajador/search_request/{id}', [TrabajadorController::class, 'searchRequestAction']);
    Route::get('mercurio/trabajador/consulta_documentos/{id}', [TrabajadorController::class, 'consultaDocumentosAction']);
    Route::post('mercurio/trabajador/borrar', [TrabajadorController::class, 'borrarAction']);
});
