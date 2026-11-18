<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ConsultaIndicadoresTest extends TestCase
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
            'consulta.indicadores',
            'consulta.consultaIndicadores',
            'consulta.reporteExcelIndicadores',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_indicadores_es_get_y_consulta_excel_son_post(): void
    {
        $index = Route::getRoutes()->getByName('consulta.indicadores');
        $consulta = Route::getRoutes()->getByName('consulta.consultaIndicadores');
        $excel = Route::getRoutes()->getByName('consulta.reporteExcelIndicadores');

        $this->assertNotNull($index);
        $this->assertNotNull($consulta);
        $this->assertNotNull($excel);
        $this->assertContains('GET', $index->methods());
        $this->assertContains('POST', $consulta->methods());
        $this->assertContains('POST', $excel->methods());
        $this->assertSame('indicadores', $index->getActionMethod());
    }
}
