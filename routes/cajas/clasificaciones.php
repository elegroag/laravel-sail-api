<?php

// Importar facades y controlador necesarios
use App\Http\Controllers\Cajas\Mercurio67Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('clasificaciones')->group(function () {
    // Definir rutas para Mercurio67Controller
    Route::get('/', [Mercurio67Controller::class, 'indexAction'])->name('clasificaciones.index');
    Route::post('buscar', [Mercurio67Controller::class, 'buscarAction'])->name('clasificaciones.buscar');
    Route::get('nuevo', [Mercurio67Controller::class, 'nuevoAction'])->name('clasificaciones.nuevo');
    Route::post('editar', [Mercurio67Controller::class, 'editarAction'])->name('clasificaciones.editar');
    Route::post('borrar', [Mercurio67Controller::class, 'borrarAction'])->name('clasificaciones.borrar');
    Route::post('guardar', [Mercurio67Controller::class, 'guardarAction'])->name('clasificaciones.guardar');
    Route::post('valide-pk', [Mercurio67Controller::class, 'validePkAction'])->name('clasificaciones.validePk');
    Route::get('reporte/{format?}', [Mercurio67Controller::class, 'reporteAction'])->name('clasificaciones.reporte');
    Route::post('aplicar-filtro', [Mercurio67Controller::class, 'aplicarFiltroAction'])->name('clasificaciones.aplicarFiltro');
    Route::post('change-cantidad-pagina', [Mercurio67Controller::class, 'changeCantidadPaginaAction'])->name('clasificaciones.changeCantidadPagina');
});
