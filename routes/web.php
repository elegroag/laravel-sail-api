<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/web', function () {
    return redirect()->route('web.login');
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
require __DIR__ . '/cajas/servicios.php';
require __DIR__ . '/cajas/notificaciones.php';
require __DIR__ . '/cajas/archivo_areas.php';
require __DIR__ . '/cajas/galeria.php';
require __DIR__ . '/cajas/usuario_externo.php';
require __DIR__ . '/cajas/auditoria.php';

Route::fallback(function (Request $request) {
    $ruta = $request->url();
    if (! $ruta) {
        return redirect()->route('web.login');
    }

    // Mostrar vista personalizada para rutas no disponibles en la web
    return response()->view('errors.web-unavailable', ['ruta' => $ruta], 404);
});
