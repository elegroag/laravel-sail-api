<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DevolucionServiciosTest extends TestCase
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

    public function test_rutas_requieren_middleware_cajas_auth(): void
    {
        foreach ([
            'devolucion-servicios.index',
            'devolucion-servicios.en-tramite',
            'devolucion-servicios.aprobar',
            'devolucion-servicios.rechazar',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_es_get_y_acciones_son_post(): void
    {
        $index = Route::getRoutes()->getByName('devolucion-servicios.index');
        $enTramite = Route::getRoutes()->getByName('devolucion-servicios.en-tramite');
        $aprobar = Route::getRoutes()->getByName('devolucion-servicios.aprobar');
        $rechazar = Route::getRoutes()->getByName('devolucion-servicios.rechazar');

        $this->assertNotNull($index);
        $this->assertContains('GET', $index->methods());
        $this->assertSame('index', $index->getActionMethod());

        foreach ([$enTramite, $aprobar, $rechazar] as $route) {
            $this->assertNotNull($route);
            $this->assertContains('POST', $route->methods());
        }
    }

    public function test_endpoints_ajax_legacy_ya_no_existen(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('devolucion-servicios.buscar'));
        $this->assertNull(Route::getRoutes()->getByName('devolucion-servicios.change-cantidad-pagina'));
    }
}
