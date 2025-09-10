<?php

use App\Http\Controllers\Mercurio\BeneficiarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

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
