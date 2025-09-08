<?php

use App\Http\Controllers\Mercurio\ActualizaEmpresaController;
use App\Http\Controllers\Mercurio\ActualizaTrabajadorController;
use App\Http\Controllers\Mercurio\BeneficiarioController;
use App\Http\Controllers\Mercurio\ConyugeController;
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
Route::post('mercurio/principal/ingreso_dirigido', [PrincipalController::class, 'ingresoDirigidoAction']);

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

    Route::post('mercurio/principal/actualiza_estado_solicitudes', [PrincipalController::class, 'actualizaEstadoSolicitudesAction']);
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
    Route::post('/mercurio/pensionado/borrar/{id}', [PensionadoController::class, 'borrarAction']);
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
    Route::post('/mercurio/facultativo/borrar/{id}', [FacultativoController::class, 'borrarAction']);

    Route::post('/mercurio/facultativo/params', [FacultativoController::class, 'paramsAction']);
    Route::post('/mercurio/facultativo/render_table', [FacultativoController::class, 'renderTableAction']);
    Route::post('/mercurio/facultativo/render_table/{estado}', [FacultativoController::class, 'renderTableAction']);

    Route::post('/mercurio/facultativo/valida', [FacultativoController::class, 'validaAction']);
    Route::post('/mercurio/facultativo/digito_verification', [FacultativoController::class, 'digitoVerificationAction']);
});

// Trabajador (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/trabajador/index', [TrabajadorController::class, 'indexAction']);
    Route::post('mercurio/trabajador/buscar_trabajador', [TrabajadorController::class, 'buscarTrabajadorAction']);
    Route::post('mercurio/trabajador/guardar', [TrabajadorController::class, 'guardarAction']);
    Route::post('mercurio/trabajador/borrar_archivo', [TrabajadorController::class, 'borrarArchivoAction']);
    Route::post('mercurio/trabajador/guardar_archivo', [TrabajadorController::class, 'guardarArchivoAction']);
    Route::get('mercurio/trabajador/archivos_requeridos/{id}', [TrabajadorController::class, 'archivosRequeridosAction']);
    Route::post('mercurio/trabajador/enviar_caja', [TrabajadorController::class, 'enviarCajaAction']);
    Route::get('mercurio/trabajador/seguimiento/{id}', [TrabajadorController::class, 'seguimientoAction']);

    Route::post('mercurio/trabajador/params', [TrabajadorController::class, 'paramsAction']);
    Route::post('mercurio/trabajador/render_table', [TrabajadorController::class, 'renderTableAction']);
    Route::post('mercurio/trabajador/render_table/{estado}', [TrabajadorController::class, 'renderTableAction']);
    Route::post('mercurio/trabajador/search_request/{id}', [TrabajadorController::class, 'searchRequestAction']);
    Route::post('mercurio/trabajador/consulta_documentos/{id}', [TrabajadorController::class, 'consultaDocumentosAction']);
    Route::post('mercurio/trabajador/valida', [TrabajadorController::class, 'validaAction']);

    Route::get('mercurio/trabajador/download_temp/{archivo}', [TrabajadorController::class, 'downloadFileAction']);
    Route::get('mercurio/trabajador/download_docs/{archivo}', [TrabajadorController::class, 'downloadDocsAction']);
    Route::post('mercurio/trabajador/borrar', [TrabajadorController::class, 'borrarAction']);
    Route::post('mercurio/trabajador/borrar/{id}', [TrabajadorController::class, 'borrarAction']);

    Route::post('mercurio/trabajador/valide_nit', [TrabajadorController::class, 'valideNitAction']);
    Route::post('mercurio/trabajador/traer_trabajador', [TrabajadorController::class, 'traerTrabajadorAction']);
});


// Conyuge (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/conyuge/index', [ConyugeController::class, 'indexAction']);
    Route::post('mercurio/conyuge/buscar_trabajador', [ConyugeController::class, 'buscarTrabajadorAction']);
    Route::post('mercurio/conyuge/guardar', [ConyugeController::class, 'guardarAction']);
    Route::post('mercurio/conyuge/borrar_archivo', [ConyugeController::class, 'borrarArchivoAction']);
    Route::post('mercurio/conyuge/guardar_archivo', [ConyugeController::class, 'guardarArchivoAction']);
    Route::get('mercurio/conyuge/archivos_requeridos/{id}', [ConyugeController::class, 'archivosRequeridosAction']);
    Route::post('mercurio/conyuge/enviar_caja', [ConyugeController::class, 'enviarCajaAction']);
    Route::get('mercurio/conyuge/seguimiento/{id}', [ConyugeController::class, 'seguimientoAction']);

    Route::post('mercurio/conyuge/params', [ConyugeController::class, 'paramsAction']);
    Route::post('mercurio/conyuge/render_table', [ConyugeController::class, 'renderTableAction']);
    Route::post('mercurio/conyuge/render_table/{estado}', [ConyugeController::class, 'renderTableAction']);
    Route::post('mercurio/conyuge/search_request/{id}', [ConyugeController::class, 'searchRequestAction']);
    Route::post('mercurio/conyuge/consulta_documentos/{id}', [ConyugeController::class, 'consultaDocumentosAction']);
    Route::post('mercurio/conyuge/valida', [ConyugeController::class, 'validaAction']);

    Route::get('mercurio/conyuge/download_temp/{archivo}', [ConyugeController::class, 'downloadFileAction']);
    Route::get('mercurio/conyuge/download_docs/{archivo}', [ConyugeController::class, 'downloadDocsAction']);
    Route::post('mercurio/conyuge/borrar', [ConyugeController::class, 'borrarAction']);
    Route::post('mercurio/conyuge/borrar/{id}', [ConyugeController::class, 'borrarAction']);
});

//  Beneficiario (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/beneficiario/index', [BeneficiarioController::class, 'indexAction']);
    Route::post('mercurio/beneficiario/buscar_trabajador', [BeneficiarioController::class, 'buscarTrabajadorAction']);
    Route::post('mercurio/beneficiario/guardar', [BeneficiarioController::class, 'guardarAction']);
    Route::post('mercurio/beneficiario/borrar_archivo', [BeneficiarioController::class, 'borrarArchivoAction']);
    Route::post('mercurio/beneficiario/guardar_archivo', [BeneficiarioController::class, 'guardarArchivoAction']);
    Route::get('mercurio/beneficiario/archivos_requeridos/{id}', [BeneficiarioController::class, 'archivosRequeridosAction']);
    Route::post('mercurio/beneficiario/enviar_caja', [BeneficiarioController::class, 'enviarCajaAction']);
    Route::get('mercurio/beneficiario/seguimiento/{id}', [BeneficiarioController::class, 'seguimientoAction']);

    Route::post('mercurio/beneficiario/params', [BeneficiarioController::class, 'paramsAction']);
    Route::post('mercurio/beneficiario/render_table', [BeneficiarioController::class, 'renderTableAction']);
    Route::post('mercurio/beneficiario/render_table/{estado}', [BeneficiarioController::class, 'renderTableAction']);
    Route::post('mercurio/beneficiario/search_request/{id}', [BeneficiarioController::class, 'searchRequestAction']);
    Route::post('mercurio/beneficiario/consulta_documentos/{id}', [BeneficiarioController::class, 'consultaDocumentosAction']);
    Route::post('mercurio/beneficiario/valida', [BeneficiarioController::class, 'validaAction']);

    Route::get('mercurio/beneficiario/download_temp/{archivo}', [BeneficiarioController::class, 'downloadFileAction']);
    Route::get('mercurio/beneficiario/download_docs/{archivo}', [BeneficiarioController::class, 'downloadDocsAction']);
    Route::post('mercurio/beneficiario/borrar', [BeneficiarioController::class, 'borrarAction']);
    Route::post('mercurio/beneficiario/borrar/{id}', [BeneficiarioController::class, 'borrarAction']);
});

// Actualiza datos empresa  (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/actualizadatos/index', [ActualizaEmpresaController::class, 'indexAction']);
    Route::post('/mercurio/actualizadatos/buscar_empresa', [ActualizaEmpresaController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/actualizadatos/guardar', [ActualizaEmpresaController::class, 'guardarAction']);
    Route::post('/mercurio/actualizadatos/borrar_archivo', [ActualizaEmpresaController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/actualizadatos/guardar_archivo', [ActualizaEmpresaController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/actualizadatos/archivos_requeridos/{id}', [ActualizaEmpresaController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/actualizadatos/enviar_caja', [ActualizaEmpresaController::class, 'enviarCajaAction']);
    Route::post('/mercurio/actualizadatos/seguimiento/{id}', [ActualizaEmpresaController::class, 'seguimientoAction']);
    Route::post('/mercurio/actualizadatos/params', [ActualizaEmpresaController::class, 'paramsAction']);
    Route::get('/mercurio/actualizadatos/download_temp/{archivo}', [ActualizaEmpresaController::class, 'downloadFileAction']);
    Route::get('/mercurio/actualizadatos/download_docs/{archivo}', [ActualizaEmpresaController::class, 'downloadDocsAction']);

    Route::post('/mercurio/actualizadatos/search_request/{id}', [ActualizaEmpresaController::class, 'searchRequestAction']);
    Route::post('/mercurio/actualizadatos/consulta_documentos/{id}', [ActualizaEmpresaController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/actualizadatos/borrar', [ActualizaEmpresaController::class, 'borrarAction']);
    Route::post('/mercurio/actualizadatos/params', [ActualizaEmpresaController::class, 'paramsAction']);
    Route::post('/mercurio/actualizadatos/render_table', [ActualizaEmpresaController::class, 'renderTableAction']);
    Route::post('/mercurio/actualizadatos/render_table/{estado}', [ActualizaEmpresaController::class, 'renderTableAction']);

    Route::post('/mercurio/actualizadatos/valida', [ActualizaEmpresaController::class, 'validaAction']);
    Route::post('/mercurio/actualizadatos/digito_verification', [ActualizaEmpresaController::class, 'digitoVerificationAction']);
    Route::post('/mercurio/actualizadatos/empresa_sisu', [ActualizaEmpresaController::class, 'empresaSisuAction']);
});


// Actualiza datos trabajador  (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/actualizadatostra/index', [ActualizaTrabajadorController::class, 'indexAction']);
    Route::post('/mercurio/actualizadatostra/buscar_empresa', [ActualizaTrabajadorController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/actualizadatostra/guardar', [ActualizaTrabajadorController::class, 'guardarAction']);
    Route::post('/mercurio/actualizadatostra/borrar_archivo', [ActualizaTrabajadorController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/actualizadatostra/guardar_archivo', [ActualizaTrabajadorController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/actualizadatostra/archivos_requeridos/{id}', [ActualizaTrabajadorController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/actualizadatostra/enviar_caja', [ActualizaTrabajadorController::class, 'enviarCajaAction']);
    Route::post('/mercurio/actualizadatostra/seguimiento/{id}', [ActualizaTrabajadorController::class, 'seguimientoAction']);
    Route::post('/mercurio/actualizadatostra/params', [ActualizaTrabajadorController::class, 'paramsAction']);
    Route::get('/mercurio/actualizadatostra/download_temp/{archivo}', [ActualizaTrabajadorController::class, 'downloadFileAction']);
    Route::get('/mercurio/actualizadatostra/download_docs/{archivo}', [ActualizaTrabajadorController::class, 'downloadDocsAction']);

    Route::post('/mercurio/actualizadatostra/search_request/{id}', [ActualizaTrabajadorController::class, 'searchRequestAction']);
    Route::post('/mercurio/actualizadatostra/consulta_documentos/{id}', [ActualizaTrabajadorController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/actualizadatostra/borrar', [ActualizaTrabajadorController::class, 'borrarAction']);
    Route::post('/mercurio/actualizadatostra/params', [ActualizaTrabajadorController::class, 'paramsAction']);
    Route::post('/mercurio/actualizadatostra/render_table', [ActualizaTrabajadorController::class, 'renderTableAction']);
    Route::post('/mercurio/actualizadatostra/render_table/{estado}', [ActualizaTrabajadorController::class, 'renderTableAction']);

    Route::post('/mercurio/actualizadatostra/valida', [ActualizaTrabajadorController::class, 'validaAction']);
    Route::post('/mercurio/actualizadatostra/digito_verification', [ActualizaTrabajadorController::class, 'digitoVerificationAction']);
});
