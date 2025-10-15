<?php

use App\Http\Controllers\Mercurio\FacultativoController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Facultativo (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/facultativo')->group(function () {
        Route::get('/', function () {
            return redirect()->route('facultativo.index');
        });
        Route::get('/index', [FacultativoController::class, 'indexAction'])->name('facultativo.index');
        Route::post('/buscar_empresa', [FacultativoController::class, 'buscarEmpresaAction']);
        Route::post('/guardar', [FacultativoController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [FacultativoController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [FacultativoController::class, 'guardarArchivoAction']);
        Route::post('/archivos_requeridos/{id}', [FacultativoController::class, 'archivosRequeridosAction']);
        Route::post('/enviar_caja', [FacultativoController::class, 'enviarCajaAction']);
        Route::post('/seguimiento/{id}', [FacultativoController::class, 'seguimientoAction']);
        Route::post('/params', [FacultativoController::class, 'paramsAction']);
        Route::get('/download_temp/{archivo}', [FacultativoController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [FacultativoController::class, 'downloadDocsAction']);

        Route::post('/search_request/{id}', [FacultativoController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [FacultativoController::class, 'consultaDocumentosAction']);
        Route::post('/borrar', [FacultativoController::class, 'borrarAction']);
        Route::post('/borrar/{id}', [FacultativoController::class, 'borrarAction']);

        Route::post('/params', [FacultativoController::class, 'paramsAction']);
        Route::post('/render_table', [FacultativoController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [FacultativoController::class, 'renderTableAction']);

        Route::post('/valida', [FacultativoController::class, 'validaAction']);
        Route::post('/digito_verification', [FacultativoController::class, 'digitoVerificationAction']);
    });
});
