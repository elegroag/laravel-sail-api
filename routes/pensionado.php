<?php

use App\Http\Controllers\Mercurio\PensionadoController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Pensionado (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/pensionado/index', [PensionadoController::class, 'indexAction']);
    Route::post('/mercurio/pensionado/buscar_empresa', [PensionadoController::class, 'buscarEmpresaAction']);
    Route::post('/mercurio/pensionado/guardar', [PensionadoController::class, 'guardarAction']);
    Route::post('/mercurio/pensionado/borrar_archivo', [PensionadoController::class, 'borrarArchivoAction']);
    Route::post('/mercurio/pensionado/guardar_archivo', [PensionadoController::class, 'guardarArchivoAction']);
    Route::post('/mercurio/pensionado/enviar_caja', [PensionadoController::class, 'enviarCajaAction']);
    Route::post('/mercurio/pensionado/seguimiento/{id}', [PensionadoController::class, 'seguimientoAction']);
    Route::post('/mercurio/pensionado/params', [PensionadoController::class, 'paramsAction']);
    Route::get('/mercurio/pensionado/download_temp/{archivo}', [PensionadoController::class, 'downloadFileAction']);
    Route::get('/mercurio/pensionado/download_docs/{archivo}', [PensionadoController::class, 'downloadDocsAction']);

    Route::post('/mercurio/pensionado/search_request/{id}', [PensionadoController::class, 'searchRequestAction']);
    Route::post('/mercurio/pensionado/consulta_documentos/{id}', [PensionadoController::class, 'consultaDocumentosAction']);

    Route::post('/mercurio/pensionado/borrar', [PensionadoController::class, 'borrarAction']);
    Route::post('/mercurio/pensionado/borrar/{id}', [PensionadoController::class, 'borrarAction']);
    Route::post('/mercurio/pensionado/params', [PensionadoController::class, 'paramsAction']);

    Route::post('/mercurio/pensionado/render_table', [PensionadoController::class, 'renderTableAction']);
    Route::post('/mercurio/pensionado/render_table/{estado}', [PensionadoController::class, 'renderTableAction']);

    Route::post('/mercurio/pensionado/valida', [PensionadoController::class, 'validaAction']);
    Route::post('/mercurio/pensionado/digito_verification', [PensionadoController::class, 'digitoVerificationAction']);
});
