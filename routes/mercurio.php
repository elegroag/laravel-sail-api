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
use App\Http\Controllers\Mercurio\NotificacionesController;
use App\Http\Controllers\Mercurio\ParticularController;
use App\Http\Controllers\Mercurio\PensionadoController;
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

// Movimientos
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/usuario/index', [UsuarioController::class, 'indexAction'])->name('usuario.index');
    Route::get('/mercurio/movimientos/historial', [MovimientosController::class, 'historialAction'])->name('movimientos.historial');
    Route::get('/mercurio/firmas/index', [FirmasController::class, 'indexAction'])->name('firmas.index');
    Route::get('/mercurio/notificaciones/index', [NotificacionesController::class, 'indexAction'])->name('notificaciones.index');
    Route::post('/mercurio/notificaciones/procesar_notificacion', [NotificacionesController::class, 'procesarNotificacionAction']);
    Route::post('/mercurio/usuario/show_perfil', [UsuarioController::class, 'showPerfilAction']);
    Route::post('/mercurio/usuario/params', [UsuarioController::class, 'paramsAction']);
    Route::post('/mercurio/usuario/guardar', [UsuarioController::class, 'guardarAction']);
});

//Consultas de empresas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/subsidioemp/consulta_trabajadores_view', [SubsidioempController::class, 'consultaTrabajadoresViewAction']);
    Route::get('/mercurio/subsidioemp/consulta_giro_view', [SubsidioEmpController::class, 'consultaGiroViewAction']);
    Route::get('/mercurio/subsidioemp/consulta_aportes_view', [SubsidioEmpController::class, 'consultaAportesViewAction']);
    Route::get('/mercurio/subsidioemp/consulta_nomina_view', [SubsidioEmpController::class, 'consultaNominaViewAction']);

    Route::post('/mercurio/consulta_nomina', [SubsidioEmpController::class, 'consultaNominaAction']);
    Route::post('/mercurio/consulta_aportes', [SubsidioEmpController::class, 'consultaAportesAction']);
    Route::post('/mercurio/consulta_giro', [SubsidioEmpController::class, 'consultaGiroAction']);
    Route::post('/mercurio/consulta_trabajadores', [SubsidioEmpController::class, 'consultaTrabajadoresAction']);

    Route::get('/mercurio/subsidioemp/historial', [SubsidioEmpController::class, 'historialAction'])->name('subsidioemp.historial');
    Route::get('/mercurio/subsidio/historial', [SubsidioController::class, 'historialAction'])->name('subsidio.historial');
    Route::get('/mercurio/particular/historial', [ParticularController::class, 'historialAction'])->name('particular.historial');
});
