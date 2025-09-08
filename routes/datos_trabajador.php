<?php

use App\Http\Controllers\Mercurio\ActualizaTrabajadorController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Actualiza datos trabajador  (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/actualizadatostra/index', [ActualizaTrabajadorController::class, 'indexAction']);
    Route::post('/mercurio/actualizadatostra/buscar_empresa', [ActualizaTrabajadorController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/actualizadatostra/guardar', [ActualizaTrabajadorController::class, 'guardarAction']);
    Route::post('/mercurio/actualizadatostra/borrar_archivo', [ActualizaTrabajadorController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/actualizadatostra/guardar_archivo', [ActualizaTrabajadorController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/actualizadatostra/archivos_requeridos/{id}', [ActualizaTrabajadorController::class, 'archivosRequeridosAction']);
    Route::post('/mercurio/actualizadatostra/enviar_caja', [ActualizaTrabajadorController::class, 'enviarCajaAction']);
    Route::post('/mercurio/actualizadatostra/seguimiento/{id}', [ActualizaTrabajadorController::class, 'seguimientoAction']);
    Route::post('/mercurio/actualizadatostra/params', [ActualizaTrabajadorController::class, 'paramsAction']);
    Route::get('/mercurio/actualizadatostra/download_temp/{archivo}', [ActualizaTrabajadorController::class, 'downloadFileAction']);
    Route::get('/mercurio/actualizadatostra/download_docs/{archivo}', [ActualizaTrabajadorController::class, 'downloadDocsAction']);

    Route::post('/mercurio/actualizadatostra/search_request/{id}', [ActualizaTrabajadorController::class, 'searchRequestAction']);
    Route::post('/mercurio/actualizadatostra/consulta_documentos/{id}', [ActualizaTrabajadorController::class, 'consultaDocumentosAction']);
    Route::post('/mercurio/actualizadatostra/borrar', [ActualizaTrabajadorController::class, 'borrarAction']);
    Route::post('/mercurio/actualizadatostra/params', [ActualizaTrabajadorController::class, 'paramsAction']);
    Route::post('/mercurio/actualizadatostra/render_table', [ActualizaTrabajadorController::class, 'renderTableAction']);
    Route::post('/mercurio/actualizadatostra/render_table/{estado}', [ActualizaTrabajadorController::class, 'renderTableAction']);

    Route::post('/mercurio/actualizadatostra/valida', [ActualizaTrabajadorController::class, 'validaAction']);
    Route::post('/mercurio/actualizadatostra/digito_verification', [ActualizaTrabajadorController::class, 'digitoVerificationAction']);
});
