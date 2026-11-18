<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\Reportes\ReporteSolicitudes;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReportesolTest extends TestCase
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
        foreach (['cajas.reportesol.index', 'cajas.reportesol.procesar'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
            $this->assertContains('GET', $route->methods(), $name);
        }
    }

    public function test_procesar_valida_tipo_requerido(): void
    {
        $response = $this->from('/cajas/reportesol/index')
            ->get(route('cajas.reportesol.procesar'));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tipo']);
    }

    public function test_procesar_valida_tipo_fuera_de_rango(): void
    {
        $response = $this->from('/cajas/reportesol/index')
            ->get(route('cajas.reportesol.procesar', [
                'tipo' => '9',
            ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tipo']);
    }

    public function test_procesar_delega_al_service(): void
    {
        $this->mock(ReporteSolicitudes::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->andReturn([
                    'title' => 'Listado De Solicitudes Trabajadores',
                    'headers' => ['Columna'],
                    'rows' => [['valor']],
                ]);
        });

        $response = $this->get(route('cajas.reportesol.procesar', [
            'tipo' => '1',
            'estado' => 'A',
        ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'spreadsheet',
            strtolower((string) $response->headers->get('Content-Type'))
        );
    }
}
