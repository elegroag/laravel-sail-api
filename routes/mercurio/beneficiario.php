<?php

use App\Http\Controllers\Mercurio\BeneficiarioController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

//  Beneficiario (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/beneficiario')->group(function () {
        Route::get('/', function () {
            return redirect()->route('beneficiario.index');
        });
        Route::get('/index', [BeneficiarioController::class, 'indexAction'])->name('beneficiario.index');
        Route::post('/buscar_trabajador', [BeneficiarioController::class, 'buscarTrabajadorAction']);
        Route::post('/guardar', [BeneficiarioController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [BeneficiarioController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [BeneficiarioController::class, 'guardarArchivoAction']);
        Route::get('/archivos_requeridos/{id}', [BeneficiarioController::class, 'archivosRequeridosAction']);
        Route::post('/enviar_caja', [BeneficiarioController::class, 'enviarCajaAction']);
        Route::get('/seguimiento/{id}', [BeneficiarioController::class, 'seguimientoAction']);

        Route::post('/params', [BeneficiarioController::class, 'paramsAction']);
        Route::post('/render_table', [BeneficiarioController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [BeneficiarioController::class, 'renderTableAction']);
        Route::post('/search_request/{id}', [BeneficiarioController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [BeneficiarioController::class, 'consultaDocumentosAction']);
        Route::post('/valida', [BeneficiarioController::class, 'validaAction']);

        Route::get('/download_temp/{archivo}', [BeneficiarioController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [BeneficiarioController::class, 'downloadDocsAction']);
        Route::post('/borrar', [BeneficiarioController::class, 'borrarAction']);
        Route::post('/borrar/{id}', [BeneficiarioController::class, 'borrarAction']);
    });
});
