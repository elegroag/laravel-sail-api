<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mercurio\AuthController as MercurioAuthController;
use App\Http\Controllers\Cajas\AuthController as CajasAuthController;

Route::fallback(function (Request $request) {
    $ruta = $request->path();

    if (str_starts_with($ruta, 'cajas')) {
        return redirect('cajas/login');
    }

    if (str_starts_with($ruta, 'web') || str_starts_with($ruta, 'mercurio')) {
        return redirect('web/login');
    }

    return response()->view('errors.web-unavailable', ['ruta' => $ruta], 404);
});

Route::prefix('/web')->group(function () {
    Route::get('/', function () {
        return redirect('web/login');
    });

    Route::get('/login', [MercurioAuthController::class, 'index'])->name('login');
    Route::post('/login', [MercurioAuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/register', [MercurioAuthController::class, 'register'])->name('register');
    Route::get('/register/company', [MercurioAuthController::class, 'registerCompany'])->name('register.company');
    Route::get('/register/worker', [MercurioAuthController::class, 'registerWorker'])->name('register.worker');
    Route::get('/password/request', [MercurioAuthController::class, 'resetPassword'])->name('password.request');
    Route::post('/recovery_send', [MercurioAuthController::class, 'recoverySend'])->name('api.recovery_send');
    Route::post('/load_session', [MercurioAuthController::class, 'loadSession'])->name('load.session');
    Route::post('/salir', [MercurioAuthController::class, 'logout'])->name('login.salir');
    Route::get('/salir', [MercurioAuthController::class, 'logout'])->name('logout');
    Route::get('/params-login', [MercurioAuthController::class, 'paramsLogin'])->name('login.params');
    Route::get('/noty_cambio_correo', [MercurioAuthController::class, 'notyCambioCorreo'])->name('web.noty_cambio_correo');
    Route::post('/cambio_correo', [MercurioAuthController::class, 'cambioCorreo'])->name('web.cambio_correo');

    Route::get('/verify/{tipo}/{coddoc}/{documento}/{option_request?}', [MercurioAuthController::class, 'verifyShow'])->name('verify.show');
    Route::post('/verify', [MercurioAuthController::class, 'verify'])->name('verify.request');
    Route::post('/verify_action', [MercurioAuthController::class, 'verify'])->name('verify.action');
});

Route::prefix('/cajas')->group(function () {
    Route::get('/', function () {
        return redirect('cajas/login');
    });
    Route::get('/salir', [CajasAuthController::class, 'logout'])->name('cajas.salir');
    Route::get('/login', [CajasAuthController::class, 'index'])->name('cajas.login');
    Route::post('/autenticar', [CajasAuthController::class, 'authenticate'])->name('cajas.autenticar');
    Route::post('/cambio_correo', [CajasAuthController::class, 'cambioCorreo'])->name('cajas.cambio_correo');
});

require __DIR__ . '/mercurio/mercurio.php';
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
require __DIR__ . '/mercurio/consultas_empresa.php';
require __DIR__ . '/mercurio/consultas_trabajador.php';
require __DIR__ . '/mercurio/usuario.php';

require __DIR__ . '/cajas/menu.php';
require __DIR__ . '/cajas/menu_permission.php';
require __DIR__ . '/cajas/principal.php';

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

require __DIR__ . '/cajas/comandos.php';
require __DIR__ . '/cajas/config_basica.php';
require __DIR__ . '/cajas/config_caja.php';
require __DIR__ . '/cajas/config_documentos.php';
require __DIR__ . '/cajas/config_firmas.php';
require __DIR__ . '/cajas/config_oficina.php';
require __DIR__ . '/cajas/config_tipo_acceso.php';
require __DIR__ . '/cajas/config_tipo_opciones.php';
require __DIR__ . '/cajas/documento_empleador.php';
require __DIR__ . '/cajas/documento_trabajador.php';
require __DIR__ . '/cajas/estados_rechazo.php';
require __DIR__ . '/cajas/movile_menu.php';
require __DIR__ . '/cajas/permisos_user.php';
require __DIR__ . '/cajas/servicios.php';
require __DIR__ . '/cajas/notificaciones.php';
require __DIR__ . '/cajas/archivo_areas.php';
require __DIR__ . '/cajas/galeria.php';
require __DIR__ . '/cajas/usuario_externo.php';
require __DIR__ . '/cajas/auditoria.php';
require __DIR__ . '/cajas/consulta.php';
require __DIR__ . '/cajas/movile_basica.php';
require __DIR__ . '/cajas/movile_promociones.php';
require __DIR__ . '/cajas/movile_turismo.php';
require __DIR__ . '/cajas/movile_recreacion.php';
require __DIR__ . '/cajas/movile_educacion.php';
require __DIR__ . '/cajas/movile_destacadas.php';
require __DIR__ . '/cajas/movile_categorias.php';
require __DIR__ . '/cajas/movile_areas.php';
require __DIR__ . '/cajas/movile_infraestructura.php';
require __DIR__ . '/cajas/movile_comercios.php';
require __DIR__ . '/cajas/movile_clasificaciones.php';
require __DIR__ . '/cajas/reasigna.php';
require __DIR__ . '/cajas/formularios_dinamicos.php';
require __DIR__ . '/cajas/componentes_dinamicos.php';
