<?php

use App\Http\Controllers\Mercurio\PensionadoController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Pensionado (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::prefix('/mercurio/pensionado')->group(function () {
        Route::get('/', function () {
            return redirect()->route('pensionado.index');
        });

        Route::get('/index', [PensionadoController::class, 'indexAction'])->name('pensionado.index');
        Route::post('/buscar_empresa', [PensionadoController::class, 'buscarEmpresaAction']);
        Route::post('/guardar', [PensionadoController::class, 'guardarAction']);
        Route::post('/borrar_archivo', [PensionadoController::class, 'borrarArchivoAction']);
        Route::post('/guardar_archivo', [PensionadoController::class, 'guardarArchivoAction']);
        Route::post('/enviar_caja', [PensionadoController::class, 'enviarCajaAction']);
        Route::post('/seguimiento/{id}', [PensionadoController::class, 'seguimientoAction']);
        Route::post('/params', [PensionadoController::class, 'paramsAction']);
        Route::get('/download_temp/{archivo}', [PensionadoController::class, 'downloadFileAction']);
        Route::get('/download_docs/{archivo}', [PensionadoController::class, 'downloadDocsAction']);

        Route::post('/search_request/{id}', [PensionadoController::class, 'searchRequestAction']);
        Route::post('/consulta_documentos/{id}', [PensionadoController::class, 'consultaDocumentosAction']);

        Route::post('/borrar', [PensionadoController::class, 'borrarAction']);
        Route::post('/borrar/{id}', [PensionadoController::class, 'borrarAction']);
        Route::post('/params', [PensionadoController::class, 'paramsAction']);

        Route::post('/render_table', [PensionadoController::class, 'renderTableAction']);
        Route::post('/render_table/{estado}', [PensionadoController::class, 'renderTableAction']);

        Route::post('/valida', [PensionadoController::class, 'validaAction']);
        Route::post('/digito_verification', [PensionadoController::class, 'digitoVerificationAction']);
    });
});
