<?php

use App\Http\Controllers\Cajas\AuditoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cajas.auth'])->group(function () {
    Route::prefix('/cajas/auditoria')->group(function () {
        Route::get('/', [AuditoriaController::class, 'index'])->name('auditoria');
        Route::get('/index', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/{tipopc}/{id}', [AuditoriaController::class, 'show'])
            ->whereNumber('id')
            ->name('auditoria.show');
        Route::post('/reporte', [AuditoriaController::class, 'reporteAuditoria'])->name('auditoria.reporte');
        Route::post('/exportar', [AuditoriaController::class, 'exportarExcel'])->name('auditoria.exportar');
    });
});
