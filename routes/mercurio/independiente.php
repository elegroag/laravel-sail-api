<?php

use App\Http\Controllers\Mercurio\IndependienteController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

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
