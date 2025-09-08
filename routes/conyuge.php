<?php

use App\Http\Controllers\Mercurio\ConyugeController;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Support\Facades\Route;

// Conyuge (migrado desde Kumbia)
Route::middleware([EnsureCookieAuthenticated::class])->group(function () {
    Route::get('/mercurio/conyuge/index', [ConyugeController::class, 'indexAction']);
    Route::post('mercurio/conyuge/guardar', [ConyugeController::class, 'guardarAction']);
    Route::post('mercurio/conyuge/borrar_archivo', [ConyugeController::class, 'borrarArchivoAction']);
    Route::post('mercurio/conyuge/guardar_archivo', [ConyugeController::class, 'guardarArchivoAction']);

    Route::post('mercurio/conyuge/enviar_caja', [ConyugeController::class, 'enviarCajaAction']);
    Route::get('mercurio/conyuge/seguimiento/{id}', [ConyugeController::class, 'seguimientoAction']);

    Route::post('mercurio/conyuge/params', [ConyugeController::class, 'paramsAction']);
    Route::post('mercurio/conyuge/render_table', [ConyugeController::class, 'renderTableAction']);
    Route::post('mercurio/conyuge/render_table/{estado}', [ConyugeController::class, 'renderTableAction']);
    Route::post('mercurio/conyuge/search_request/{id}', [ConyugeController::class, 'searchRequestAction']);
    Route::post('mercurio/conyuge/consulta_documentos/{id}', [ConyugeController::class, 'consultaDocumentosAction']);
    Route::post('mercurio/conyuge/valida', [ConyugeController::class, 'validaAction']);

    Route::post('mercurio/conyuge/download_docs/{archivo}', [ConyugeController::class, 'downloadDocumentosAction']);
    Route::post('mercurio/conyuge/borrar', [ConyugeController::class, 'borrarAction']);
    Route::post('mercurio/conyuge/borrar/{id}', [ConyugeController::class, 'borrarAction']);
    Route::post('mercurio/conyuge/buscar_trabajador', [ConyugeController::class, 'buscarTrabajadorAction']);
});
