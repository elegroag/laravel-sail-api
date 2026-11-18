<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ConsultaCargaLaboralTest extends TestCase
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
            'consulta.cargaLaboral',
            'consulta.solicitudesCargaLaboral',
            'consulta.reporteExcelCargaLaboral',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_carga_laboral_es_get_y_solicitudes_es_post(): void
    {
        $index = Route::getRoutes()->getByName('consulta.cargaLaboral');
        $detalle = Route::getRoutes()->getByName('consulta.solicitudesCargaLaboral');
        $excel = Route::getRoutes()->getByName('consulta.reporteExcelCargaLaboral');

        $this->assertNotNull($index);
        $this->assertNotNull($detalle);
        $this->assertNotNull($excel);
        $this->assertContains('GET', $index->methods());
        $this->assertContains('POST', $detalle->methods());
        $this->assertContains('POST', $excel->methods());
        $this->assertSame('cargaLaboral', $index->getActionMethod());
    }
}
