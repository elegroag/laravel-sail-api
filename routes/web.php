<?php


use App\Http\Controllers\TaskController;
use App\Http\Controllers\WebController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/* Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */

Route::get('/dashboard', function () {
    return Inertia::render('dashboard');
})->name('dash');

Route::resource('tasks', TaskController::class);

Route::get('/web', [WebController::class, 'dashboard'])->name('dashboard');
Route::get('/web/empresas', [WebController::class, 'empresas'])->name('empresas.index');
Route::get('/web/trabajadores', [WebController::class, 'trabajadores'])->name('trabajadores.index');
Route::get('/web/nucleos-familiares', [WebController::class, 'nucleosFamiliares'])->name('nucleos-familiares.index');
Route::get('/web/empresas/api', [WebController::class, 'pruebaApiEmpresas']);

Route::get('/web/empresas/create', [WebController::class, 'empresasCreate'])->name('empresas.create');
Route::get('/web/empresas/{id}/edit', [WebController::class, 'empresasEdit'])->name('empresas.edit');
Route::get('/web/empresas/{id}', [WebController::class, 'empresasShow'])->name('empresas.show');

Route::get('/web/trabajadores/create', [WebController::class, 'trabajadoresCreate'])->name('trabajadores.create');
Route::get('/web/trabajadores/{id}/edit', [WebController::class, 'trabajadoresEdit'])->name('trabajadores.edit');
Route::get('/web/trabajadores/{id}', [WebController::class, 'trabajadoresShow'])->name('trabajadores.show');

Route::get('/web/nucleos-familiares/create', [WebController::class, 'nucleosFamiliaresCreate'])->name('nucleos-familiares.create');
Route::get('/web/nucleos-familiares/{id}/edit', [WebController::class, 'nucleosFamiliaresEdit'])->name('nucleos-familiares.edit');
Route::get('/web/nucleos-familiares/{id}', [WebController::class, 'nucleosFamiliaresShow'])->name('nucleos-familiares.show');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/mercurio.php';
require __DIR__ . '/empresa.php';
require __DIR__ . '/trabajador.php';
require __DIR__ . '/conyuge.php';
require __DIR__ . '/beneficiario.php';
require __DIR__ . '/facultativo.php';
require __DIR__ . '/pensionado.php';
require __DIR__ . '/independiente.php';
require __DIR__ . '/datos_empresa.php';
require __DIR__ . '/datos_trabajador.php';
require __DIR__ . '/principal.php';
require __DIR__ . '/firma.php';
