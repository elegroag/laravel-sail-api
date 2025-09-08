<?php

use App\Http\Controllers\Mercurio\LoginController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Controllers\Mercurio\TrabajadorController;
use App\Http\Controllers\Mercurio\EmpresaController;
use App\Http\Controllers\Mercurio\FacultativoController;
use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Controllers\Mercurio\IndependienteController;
use App\Http\Controllers\Mercurio\PensionadoController;
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

# Principal
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {

    Route::get('mercurio/principal/index', [PrincipalController::class, 'indexAction'])->name('principal.index');
    Route::get('mercurio/principal/dashboard_trabajador', [PrincipalController::class, 'dashboardTrabajadorAction'])->name('principal.dashboard_trabajador');
    Route::get('mercurio/principal/dashboard_empresa', [PrincipalController::class, 'dashboardEmpresaAction'])->name('principal.dashboard_empresa');

    Route::post('mercurio/principal/file_existe_global/{filepath}', [PrincipalController::class, 'fileExisteGlobalAction']);

    Route::post('mercurio/principal/traer_aportes_empresa', [PrincipalController::class, 'traerAportesEmpresaAction']);
    Route::post('mercurio/principal/traer_giro_empresa', [PrincipalController::class, 'traerGiroEmpresaAction']);
    Route::post('mercurio/principal/traer_categorias_empresa', [PrincipalController::class, 'traerCategoriasEmpresaAction']);
    Route::post('mercurio/principal/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
    Route::post('mercurio/principal/traer_giros_trabajador', [PrincipalController::class, 'traerGirosTrabajadorAction']);
    Route::post('mercurio/principal/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);

    Route::post('mercurio/principal/valida_syncro', [PrincipalController::class, 'validaSyncroAction'])->name('valida_syncro');
    Route::post('mercurio/principal/servicios', [PrincipalController::class, 'serviciosAction'])->name('servicios');
    Route::post('mercurio/principal/lista_adress', [PrincipalController::class, 'listaAdressAction']);

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
    Route::post('/mercurio/empresa/archivos_requeridos/{id}', [EmpresaController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/empresa/enviar_caja', [EmpresaController::class, 'enviarCajaAction']);
    Route::post('/mercurio/empresa/seguimiento/{id}', [EmpresaController::class, 'seguimientoAction']);
    Route::post('/mercurio/empresa/params', [EmpresaController::class, 'paramsAction']);
    Route::get('/mercurio/empresa/download_temp/{archivo}', [EmpresaController::class, 'downloadFileAction']);
    Route::get('/mercurio/empresa/download_docs/{archivo}', [EmpresaController::class, 'downloadDocsAction']);

    Route::post('/mercurio/empresa/search_request/{id}', [EmpresaController::class, 'searchRequestAction']);
    Route::post('/mercurio/empresa/consulta_documentos/{id}', [EmpresaController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/empresa/borrar', [EmpresaController::class, 'borrarAction']);
    Route::post('/mercurio/empresas/params', [EmpresaController::class, 'paramsAction']);
    Route::post('/mercurio/empresa/render_table', [EmpresaController::class, 'renderTableAction']);
    Route::post('/mercurio/empresa/render_table/{estado}', [EmpresaController::class, 'renderTableAction']);

    Route::post('/mercurio/empresa/valida', [EmpresaController::class, 'validaAction']);
    Route::post('/mercurio/empresa/digito_verification', [EmpresaController::class, 'digitoVerificationAction']);
});

// Independiente (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/independiente/index', [IndependienteController::class, 'indexAction']);
    Route::post('/mercurio/independiente/buscar_empresa', [IndependienteController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/independiente/guardar', [IndependienteController::class, 'guardarAction']);
    Route::post('/mercurio/independiente/borrar_archivo', [IndependienteController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/independiente/guardar_archivo', [IndependienteController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/independiente/archivos_requeridos/{id}', [IndependienteController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/independiente/enviar_caja', [IndependienteController::class, 'enviarCajaAction']);
    Route::post('/mercurio/independiente/seguimiento/{id}', [IndependienteController::class, 'seguimientoAction']);
    Route::post('/mercurio/independiente/params', [IndependienteController::class, 'paramsAction']);
    Route::get('/mercurio/independiente/download_temp/{archivo}', [IndependienteController::class, 'downloadFileAction']);
    Route::get('/mercurio/independiente/download_docs/{archivo}', [IndependienteController::class, 'downloadDocsAction']);

    Route::post('/mercurio/independiente/search_request/{id}', [IndependienteController::class, 'searchRequestAction']);
    Route::post('/mercurio/independiente/consulta_documentos/{id}', [IndependienteController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/independiente/borrar', [IndependienteController::class, 'borrarAction']);
    Route::post('/mercurio/independiente/params', [IndependienteController::class, 'paramsAction']);
    Route::post('/mercurio/independiente/render_table', [IndependienteController::class, 'renderTableAction']);
    Route::post('/mercurio/independiente/render_table/{estado}', [IndependienteController::class, 'renderTableAction']);

    Route::post('/mercurio/independiente/valida', [IndependienteController::class, 'validaAction']);
    Route::post('/mercurio/independiente/digito_verification', [IndependienteController::class, 'digitoVerificationAction']);
});


// Pensionado (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/pensionado/index', [PensionadoController::class, 'indexAction']);
    Route::post('/mercurio/pensionado/buscar_empresa', [PensionadoController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/pensionado/guardar', [PensionadoController::class, 'guardarAction']);
    Route::post('/mercurio/pensionado/borrar_archivo', [PensionadoController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/pensionado/guardar_archivo', [PensionadoController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/pensionado/archivos_requeridos/{id}', [PensionadoController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/pensionado/enviar_caja', [PensionadoController::class, 'enviarCajaAction']);
    Route::post('/mercurio/pensionado/seguimiento/{id}', [PensionadoController::class, 'seguimientoAction']);
    Route::post('/mercurio/pensionado/params', [PensionadoController::class, 'paramsAction']);
    Route::get('/mercurio/pensionado/download_temp/{archivo}', [PensionadoController::class, 'downloadFileAction']);
    Route::get('/mercurio/pensionado/download_docs/{archivo}', [PensionadoController::class, 'downloadDocsAction']);

    Route::post('/mercurio/pensionado/search_request/{id}', [PensionadoController::class, 'searchRequestAction']);
    Route::post('/mercurio/pensionado/consulta_documentos/{id}', [PensionadoController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/pensionado/borrar', [PensionadoController::class, 'borrarAction']);
    Route::post('/mercurio/pensionado/params', [PensionadoController::class, 'paramsAction']);
    Route::post('/mercurio/pensionado/render_table', [PensionadoController::class, 'renderTableAction']);
    Route::post('/mercurio/pensionado/render_table/{estado}', [PensionadoController::class, 'renderTableAction']);

    Route::post('/mercurio/pensionado/valida', [PensionadoController::class, 'validaAction']);
    Route::post('/mercurio/pensionado/digito_verification', [PensionadoController::class, 'digitoVerificationAction']);
});


// Facultativo (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/facultativo/index', [FacultativoController::class, 'indexAction']);
    Route::post('/mercurio/facultativo/buscar_empresa', [FacultativoController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/facultativo/guardar', [FacultativoController::class, 'guardarAction']);
    Route::post('/mercurio/facultativo/borrar_archivo', [FacultativoController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/facultativo/guardar_archivo', [FacultativoController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/facultativo/archivos_requeridos/{id}', [FacultativoController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/facultativo/enviar_caja', [FacultativoController::class, 'enviarCajaAction']);
    Route::post('/mercurio/facultativo/seguimiento/{id}', [FacultativoController::class, 'seguimientoAction']);
    Route::post('/mercurio/facultativo/params', [FacultativoController::class, 'paramsAction']);
    Route::get('/mercurio/facultativo/download_temp/{archivo}', [FacultativoController::class, 'downloadFileAction']);
    Route::get('/mercurio/facultativo/download_docs/{archivo}', [FacultativoController::class, 'downloadDocsAction']);

    Route::post('/mercurio/facultativo/search_request/{id}', [FacultativoController::class, 'searchRequestAction']);
    Route::post('/mercurio/facultativo/consulta_documentos/{id}', [FacultativoController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/facultativo/borrar', [FacultativoController::class, 'borrarAction']);
    Route::post('/mercurio/facultativo/params', [FacultativoController::class, 'paramsAction']);
    Route::post('/mercurio/facultativo/render_table', [FacultativoController::class, 'renderTableAction']);
    Route::post('/mercurio/facultativo/render_table/{estado}', [FacultativoController::class, 'renderTableAction']);

    Route::post('/mercurio/facultativo/valida', [FacultativoController::class, 'validaAction']);
    Route::post('/mercurio/facultativo/digito_verification', [FacultativoController::class, 'digitoVerificationAction']);
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
