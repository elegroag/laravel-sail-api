<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReporteComprasServiciosTest extends TestCase
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
        foreach (['cajas.reporte-compras-servicios.index', 'cajas.reporte-compras-servicios.exportar'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_y_exportar_son_get(): void
    {
        $index = Route::getRoutes()->getByName('cajas.reporte-compras-servicios.index');
        $exportar = Route::getRoutes()->getByName('cajas.reporte-compras-servicios.exportar');

        $this->assertNotNull($index);
        $this->assertNotNull($exportar);
        $this->assertContains('GET', $index->methods());
        $this->assertContains('GET', $exportar->methods());
        $this->assertSame('index', $index->getActionMethod());
        $this->assertSame('exportar', $exportar->getActionMethod());
    }

    public function test_consultar_legacy_ya_no_existe(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('cajas.reporte-compras-servicios.consultar'));
    }
}
