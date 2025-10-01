<?php

use App\Http\Controllers\Api\AuthMercurioController;
use App\Http\Controllers\Mercurio\AuthController;
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

Route::get('/dashboard', function () {
    return Inertia::render('dashboard');
})->name('dash');

Route::resource('tasks', TaskController::class);

Route::get('/web/login', [AuthController::class, 'index'])->name('login');
Route::get('/web/register', [AuthController::class, 'register'])->name('register');
Route::get('/web/password/request', [AuthController::class, 'resetPassword'])->name('password.request');
Route::get('/web/verify/{tipo}/{coddoc}/{documento}', [AuthController::class, 'verify'])->name('verify.show');
Route::post('/web/verify', [AuthController::class, 'verify'])->name('verify.request');
Route::post('/web/verify_action', [AuthController::class, 'verifyAction'])->name('verify.action');
Route::get('/web/load_session', [AuthController::class, 'loadSession'])->name('load.session');


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
require __DIR__ . '/mercurio/mercurio.php';
require __DIR__ . '/mercurio/auth.php';
require __DIR__ . '/mercurio/trabajador.php';
require __DIR__ . '/mercurio/empresa.php';
require __DIR__ . '/mercurio/conyuge.php';
require __DIR__ . '/mercurio/beneficiario.php';
require __DIR__ . '/mercurio/facultativo.php';
require __DIR__ . '/mercurio/pensionado.php';
require __DIR__ . '/mercurio/independiente.php';
require __DIR__ . '/mercurio/datos_empresa.php';
require __DIR__ . '/mercurio/datos_trabajador.php';
require __DIR__ . '/mercurio/principal.php';
require __DIR__ . '/mercurio/firma.php';
require __DIR__ . '/mercurio/productos.php';

require __DIR__ . '/cajas/cajas.php';
require __DIR__ . '/cajas/aprueba_beneficiario.php';
require __DIR__ . '/cajas/aprueba_conyuge.php';
require __DIR__ . '/cajas/aprueba_trabajador.php';
require __DIR__ . '/cajas/aprueba_facultativo.php';
require __DIR__ . '/cajas/aprueba_pensionado.php';
require __DIR__ . '/cajas/aprueba_independiente.php';
require __DIR__ . '/cajas/aprueba_empresa.php';
require __DIR__ . '/cajas/aprueba_data_empresa.php';
require __DIR__ . '/cajas/aprueba_data_trabajador.php';
require __DIR__ . '/cajas/aprueba_comunitaria.php';
require __DIR__ . '/cajas/aprueba_certificado.php';

require __DIR__ . '/cajas/admin_productos.php';
require __DIR__ . '/cajas/areas.php';
require __DIR__ . '/cajas/categorias.php';
require __DIR__ . '/cajas/comandos.php';
require __DIR__ . '/cajas/comercios.php';
require __DIR__ . '/cajas/compartido.php';
require __DIR__ . '/cajas/config_basica.php';
require __DIR__ . '/cajas/config_caja.php';
require __DIR__ . '/cajas/config_documentos.php';
require __DIR__ . '/cajas/config_firmas.php';
require __DIR__ . '/cajas/config_oficina.php';
require __DIR__ . '/cajas/config_tipo_acceso.php';
require __DIR__ . '/cajas/config_tipo_opciones.php';
require __DIR__ . '/cajas/destacadas.php';
require __DIR__ . '/cajas/documento_empleador.php';
require __DIR__ . '/cajas/documento_trabajador.php';
require __DIR__ . '/cajas/estados_rechazo.php';
require __DIR__ . '/cajas/infraestructura.php';
require __DIR__ . '/cajas/movile_menu.php';
require __DIR__ . '/cajas/permisos_user.php';
require __DIR__ . '/cajas/promociones_educacion.php';
require __DIR__ . '/cajas/promociones_recreacion.php';
require __DIR__ . '/cajas/promociones_turismo.php';
require __DIR__ . '/cajas/servicios.php';
require __DIR__ . '/cajas/notificaciones.php';
require __DIR__ . '/cajas/archivo_areas.php';
require __DIR__ . '/cajas/galeria.php';
