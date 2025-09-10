<?php

use App\Http\Controllers\Mercurio\CertificadosController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Controllers\Mercurio\FirmasController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

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

    Route::post('mercurio/principal/actualiza_estado_solicitudes', [PrincipalController::class, 'actualizaEstadoSolicitudesAction']);

    Route::get('mercurio/certificados/index', [CertificadosController::class, 'indexAction']);
    Route::post('mercurio/certificados/guardar', [CertificadosController::class, 'guardarAction']);
});
