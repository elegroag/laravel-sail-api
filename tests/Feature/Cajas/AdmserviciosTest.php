<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdmserviciosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        config(['logging.default' => 'stderr']);

        $this->withoutMiddleware(CajasAuthenticated::class);
    }

    public function test_rutas_principales_requieren_middleware_cajas_auth(): void
    {
        foreach (['admservicios.index', 'admservicios.reporte', 'admservicios.ver'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_y_reporte_son_get(): void
    {
        $index = Route::getRoutes()->getByName('admservicios.index');
        $reporte = Route::getRoutes()->getByName('admservicios.reporte');

        $this->assertNotNull($index);
        $this->assertNotNull($reporte);
        $this->assertContains('GET', $index->methods());
        $this->assertContains('GET', $reporte->methods());
        $this->assertSame('index', $index->getActionMethod());
        $this->assertSame('reporte', $reporte->getActionMethod());
    }

    public function test_rutas_ajax_legacy_del_listado_ya_no_existen(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('admservicios.buscar'));
        $this->assertNull(Route::getRoutes()->getByName('admservicios.aplicar-filtro'));
        $this->assertNull(Route::getRoutes()->getByName('admservicios.change-cantidad-pagina'));
    }

    public function test_acciones_de_detalle_siguen_disponibles(): void
    {
        foreach ([
            'admservicios.detalle',
            'admservicios.registrar-subsidio',
            'admservicios.marcar-vencida',
            'admservicios.devolucion-en-tramite',
            'admservicios.devolucion-aprobar',
            'admservicios.devolucion-rechazar',
        ] as $name) {
            $this->assertNotNull(Route::getRoutes()->getByName($name), $name);
        }
    }
}
