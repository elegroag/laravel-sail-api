<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ConciliacionServiciosTest extends TestCase
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
        foreach (['conciliacion-servicios.index', 'conciliacion-servicios.reporte'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_es_get_y_reporte_es_get(): void
    {
        $index = Route::getRoutes()->getByName('conciliacion-servicios.index');
        $reporte = Route::getRoutes()->getByName('conciliacion-servicios.reporte');

        $this->assertNotNull($index);
        $this->assertNotNull($reporte);
        $this->assertContains('GET', $index->methods());
        $this->assertContains('GET', $reporte->methods());
        $this->assertSame('index', $index->getActionMethod());
        $this->assertSame('reporte', $reporte->getActionMethod());
    }

    public function test_buscar_legacy_ya_no_existe(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('conciliacion-servicios.buscar'));
    }
}
