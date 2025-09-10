<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mercurio\EmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;

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
