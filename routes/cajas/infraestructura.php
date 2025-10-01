<?php

// Importar facades y controlador necesarios
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cajas\Mercurio56Controller;

Route::prefix('infraestructura')->group(function () {
    // Definir rutas para el controlador Mercurio56
    Route::get('/', [Mercurio56Controller::class, 'indexAction'])->name('infraestructura.index'); // Mostrar lista de infraestructura
    Route::post('/buscar', [Mercurio56Controller::class, 'buscarAction'])->name('infraestructura.buscar'); // Buscar infraestructura
    Route::post('/editar', [Mercurio56Controller::class, 'editarAction'])->name('infraestructura.editar'); // Editar infraestructura
    Route::post('/borrar', [Mercurio56Controller::class, 'borrarAction'])->name('infraestructura.borrar'); // Borrar infraestructura
    Route::post('/guardar', [Mercurio56Controller::class, 'guardarAction'])->name('infraestructura.guardar'); // Guardar infraestructura
    Route::post('/valide-pk', [Mercurio56Controller::class, 'validePkAction'])->name('infraestructura.valide.pk'); // Validar clave primaria
    Route::get('/reporte/{format?}', [Mercurio56Controller::class, 'reporteAction'])->name('infraestructura.reporte'); // Generar reporte
});
