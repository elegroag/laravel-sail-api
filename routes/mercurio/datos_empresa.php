<?php

use App\Http\Controllers\Mercurio\ActualizaEmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Actualiza datos empresa  (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/actualizadatos')->group(function () {
        Route::get('/', function () {
            return redirect()->route('actualiza_empresa.index');
        });
        Route::get('/index', [ActualizaEmpresaController::class, 'indexAction'])->name('actualiza_empresa.index');
        Route::post('/buscar_empresa', [ActualizaEmpresaController::class, 'buscarEmpresaAction']);
        Route::post('/guardar', [ActualizaEmpresaController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [ActualizaEmpresaController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [ActualizaEmpresaController::class, 'guardarArchivoAction']);
        Route::post('/archivos_requeridos/{id}', [ActualizaEmpresaController::class, 'archivosRequeridosAction']);
        Route::post('/enviar_caja', [ActualizaEmpresaController::class, 'enviarCajaAction']);
        Route::post('/seguimiento/{id}', [ActualizaEmpresaController::class, 'seguimientoAction']);
        Route::post('/params', [ActualizaEmpresaController::class, 'paramsAction']);
        Route::get('/download_temp/{archivo}', [ActualizaEmpresaController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [ActualizaEmpresaController::class, 'downloadDocsAction']);

        Route::post('/search_request/{id}', [ActualizaEmpresaController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [ActualizaEmpresaController::class, 'consultaDocumentosAction']);
        Route::post('/borrar', [ActualizaEmpresaController::class, 'borrarAction']);
        Route::post('/params', [ActualizaEmpresaController::class, 'paramsAction']);
        Route::post('/render_table', [ActualizaEmpresaController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [ActualizaEmpresaController::class, 'renderTableAction']);

        Route::post('/valida', [ActualizaEmpresaController::class, 'validaAction']);
        Route::post('/digito_verification', [ActualizaEmpresaController::class, 'digitoVerificationAction']);
        Route::post('/empresa_sisu', [ActualizaEmpresaController::class, 'empresaSisuAction']);
    });
});
