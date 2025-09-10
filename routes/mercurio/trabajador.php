<?php

use App\Http\Controllers\Mercurio\ConsultasTrabajadorController;
use App\Http\Controllers\Mercurio\TrabajadorController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

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

    Route::post('mercurio/trabajador/borrar', [TrabajadorController::class, 'borrarAction']);
    Route::post('mercurio/trabajador/borrar/{id}', [TrabajadorController::class, 'borrarAction']);

    Route::post('mercurio/trabajador/valide_nit', [TrabajadorController::class, 'valideNitAction']);
    Route::post('mercurio/trabajador/traer_trabajador', [TrabajadorController::class, 'traerTrabajadorAction']);
});

# Subsidio consultas de empresas
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('mercurio/subsidio/consulta_giro_view', [ConsultasTrabajadorController::class, 'consultaGiroViewAction']);
    Route::post('mercurio/subsidio/consulta_giro', [ConsultasTrabajadorController::class, 'consultaGiroAction']);
    Route::get('mercurio/subsidio/consulta_no_giro_view', [ConsultasTrabajadorController::class, 'consultaNoGiroViewAction']);
    Route::post('mercurio/subsidio/consulta_no_giro', [ConsultasTrabajadorController::class, 'consultaNoGiroAction']);
    Route::get('mercurio/subsidio/consulta_planilla_trabajador_view', [ConsultasTrabajadorController::class, 'consultaPlanillaTrabajadorViewAction']);
    Route::post('mercurio/subsidio/consulta_planilla_trabajador', [ConsultasTrabajadorController::class, 'consultaPlanillaTrabajadorAction']);
    Route::post('mercurio/subsidio/consulta_tarjeta', [ConsultasTrabajadorController::class, 'consultaTarjetaAction']);
    Route::get('mercurio/subsidio/certificado_afiliacion_view', [ConsultasTrabajadorController::class, 'certificadoAfiliacionViewAction']);
    Route::post('mercurio/subsidio/certificado_afiliacion', [ConsultasTrabajadorController::class, 'certificadoAfiliacionAction']);
    Route::get('mercurio/subsidio/consulta_nucleo_view', [ConsultasTrabajadorController::class, 'consultaNucleoViewAction']);
    Route::post('mercurio/subsidio/consulta_nucleo', [ConsultasTrabajadorController::class, 'consultaNucleoAction']);
});
