<?php

use App\Http\Controllers\Mercurio\CertificadosController;
use App\Http\Controllers\Mercurio\MovimientosController;
use App\Http\Controllers\Mercurio\PrincipalController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Principal
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/principal')->group(function () {
        Route::get('/', function () {
            return redirect()->route('principal.index');
        });

        Route::get('/index', [PrincipalController::class, 'index'])->name('principal.index');
        Route::get('/dashboard_trabajador', [PrincipalController::class, 'dashboardTrabajador'])->name('principal.dashboard_trabajador');
        Route::get('/dashboard_empresa', [PrincipalController::class, 'dashboardEmpresa'])->name('principal.dashboard_empresa');

        Route::post('/file_existe_global/{filepath}', [PrincipalController::class, 'fileExisteGlobal']);
        Route::post('/traer_aportes_empresa', [PrincipalController::class, 'traerAportesEmpresa']);
        Route::post('/traer_giro_empresa', [PrincipalController::class, 'traerGiroEmpresa']);
        Route::post('/traer_categorias_empresa', [PrincipalController::class, 'traerCategoriasEmpresa']);
        Route::post('/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajador']);
        Route::post('/traer_giros_trabajador', [PrincipalController::class, 'traerGirosTrabajador']);
        Route::post('/traer_categorias_trabajador', [PrincipalController::class, 'traerCategoriasTrabajador']);
        Route::post('/valida_syncro', [PrincipalController::class, 'validaSyncro']);
        Route::post('/servicios', [PrincipalController::class, 'servicios']);
        Route::post('/lista_adress', [PrincipalController::class, 'listaAdress']);
        Route::post('/actualiza_estado_solicitudes', [PrincipalController::class, 'actualizaEstadoSolicitudes']);
    });

    Route::prefix('/mercurio/movimientos')->group(function () {
        Route::get('/historial', [MovimientosController::class, 'historial'])->name('movimientos.historial');
        Route::get('/cambio_email_view', [MovimientosController::class, 'cambioEmailView'])->name('movimientos.cambio_email_view');
        Route::get('/cambio_clave_view', [MovimientosController::class, 'cambioClaveView'])->name('movimientos.cambio_clave_view');
    });

    Route::get('/mercurio/certificados/index', [CertificadosController::class, 'index']);
    Route::post('/mercurio/certificados/guardar', [CertificadosController::class, 'guardar']);
});
