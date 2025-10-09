<?php

use App\Http\Controllers\Mercurio\CertificadosController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Principal
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/principal')->group(function () {
        Route::get('/index', [PrincipalController::class, 'indexAction'])->name('principal.index');
        Route::get('/dashboard_trabajador', [PrincipalController::class, 'dashboardTrabajadorAction'])->name('principal.dashboard_trabajador');
        Route::get('/dashboard_empresa', [PrincipalController::class, 'dashboardEmpresaAction'])->name('principal.dashboard_empresa');

        Route::post('/file_existe_global/{filepath}', [PrincipalController::class, 'fileExisteGlobalAction']);
        Route::post('/traer_aportes_empresa', [PrincipalController::class, 'traerAportesEmpresaAction']);
        Route::post('/traer_giro_empresa', [PrincipalController::class, 'traerGiroEmpresaAction']);
        Route::post('/traer_categorias_empresa', [PrincipalController::class, 'traerCategoriasEmpresaAction']);
        Route::post('/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
        Route::post('/traer_giros_trabajador', [PrincipalController::class, 'traerGirosTrabajadorAction']);
        Route::post('/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajadorAction']);
        Route::post('/valida_syncro', [PrincipalController::class, 'validaSyncroAction'])->name('valida_syncro');
        Route::post('/servicios', [PrincipalController::class, 'serviciosAction'])->name('servicios');
        Route::post('/lista_adress', [PrincipalController::class, 'listaAdressAction']);
        Route::post('/actualiza_estado_solicitudes', [PrincipalController::class, 'actualizaEstadoSolicitudesAction']);
    });

    Route::get('mercurio/movimientos/historial', [MovimientosController::class, 'historialAction'])->name('movimientos.historial');
    Route::get('mercurio/movimientos/cambio_email_view', [MovimientosController::class, 'cambioEmailViewAction'])->name('movimientos.cambio_email_view');
    Route::get('mercurio/movimientos/cambio_clave_view', [MovimientosController::class, 'cambioClaveViewAction'])->name('movimientos.cambio_clave_view');
    Route::get('mercurio/certificados/index', [CertificadosController::class, 'indexAction']);
    Route::post('mercurio/certificados/guardar', [CertificadosController::class, 'guardarAction']);
});
