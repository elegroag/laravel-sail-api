<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mercurio\EmpresaController;
use App\Http\Middleware\EnsureCookieAuthenticated;

// Empresa (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/empresa')->group(function () {
        Route::get('/index', [EmpresaController::class, 'indexAction'])->name('empresa.index');
        Route::get('/download_temp/{archivo}', [EmpresaController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [EmpresaController::class, 'downloadDocsAction']);
        Route::get('/miempresa', [EmpresaController::class, 'miEmpresaAction'])->name('empresa.miempresa');

        Route::post('/buscar_empresa', [EmpresaController::class, 'buscarEmpresaAction']);
        Route::post('/guardar', [EmpresaController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [EmpresaController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [EmpresaController::class, 'guardarArchivoAction']);
        Route::post('/archivos_requeridos/{id}', [EmpresaController::class, 'archivosRequeridosAction']);
        Route::post('/enviar_caja', [EmpresaController::class, 'enviarCajaAction']);
        Route::post('/seguimiento/{id}', [EmpresaController::class, 'seguimientoAction']);
        Route::post('/params', [EmpresaController::class, 'paramsAction'])->name('empresa.params');
        Route::post('/search_request/{id}', [EmpresaController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [EmpresaController::class, 'consultaDocumentosAction']);
        Route::post('/borrar', [EmpresaController::class, 'borrarAction']);
        Route::post('/render_table', [EmpresaController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [EmpresaController::class, 'renderTableAction']);
        Route::post('/valida', [EmpresaController::class, 'validaAction']);
        Route::post('/digito_verification', [EmpresaController::class, 'digitoVerificationAction']);
    });
});
