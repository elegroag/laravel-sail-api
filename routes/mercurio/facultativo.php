<?php

use App\Http\Controllers\Mercurio\FacultativoController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

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
