<?php

use App\Http\Controllers\Mercurio\ActualizaEmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

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
