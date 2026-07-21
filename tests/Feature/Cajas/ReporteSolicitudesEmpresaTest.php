<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\Reports\ReporteSolicitudesEmpresaService;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReporteSolicitudesEmpresaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        $this->withoutMiddleware(CajasAuthenticated::class);
    }

    public function test_consultar_valida_campos_requeridos(): void
    {
        $response = $this->postJson(route('cajas.reporte-solicitudes-empresa.consultar'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['documento', 'coddoc', 'fecini', 'fecfin', 'tipopcs']);
    }

    public function test_consultar_valida_tipopc_invalido(): void
    {
        $response = $this->postJson(route('cajas.reporte-solicitudes-empresa.consultar'), [
            'documento' => '900123456',
            'coddoc' => '3',
            'fecini' => '2026-01-01',
            'fecfin' => '2026-01-31',
            'tipopcs' => [2],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tipopcs.0']);
    }

    public function test_consultar_devuelve_json_con_filas(): void
    {
        $this->mock(ReporteSolicitudesEmpresaService::class, function ($mock): void {
            $mock->shouldReceive('consultar')
                ->once()
                ->andReturn([
                    'empresa' => [
                        'documento' => '900123456',
                        'coddoc' => '3',
                        'tipo' => 'E',
                    ],
                    'total' => 1,
                    'from_cache' => false,
                    'rows' => [[
                        'tipopc' => 1,
                        'tipo_label' => 'Trabajador',
                        'id' => 10,
                        'ruuid' => 'rad-001',
                        'documento_afiliado' => '10101010',
                        'nombre' => 'Perez Juan',
                        'nit' => '900123456',
                        'fecsol' => '2026-01-10',
                        'estado' => 'Aprobado',
                        'fecha_cierre' => '2026-01-12',
                    ]],
                    'pagination' => [
                        'page' => 1,
                        'per_page' => 25,
                        'last_page' => 1,
                        'from' => 1,
                        'to' => 1,
                        'total' => 1,
                        'cache_key' => 'rse:test:abc',
                    ],
                ]);
        });

        $response = $this->postJson(route('cajas.reporte-solicitudes-empresa.consultar'), [
            'documento' => '900123456',
            'coddoc' => '3',
            'fecini' => '2026-01-01',
            'fecfin' => '2026-01-31',
            'tipopcs' => [1, 3],
            'estado' => 'A',
            'page' => 1,
            'refresh' => true,
        ]);

        $response->assertOk();
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('rows.0.ruuid', 'rad-001');
        $response->assertJsonPath('empresa.documento', '900123456');
        $response->assertJsonPath('pagination.page', 1);
    }

    public function test_rutas_registradas_y_protegidas(): void
    {
        $this->assertTrue(Route::has('cajas.reporte-solicitudes-empresa.index'));
        $this->assertTrue(Route::has('cajas.reporte-solicitudes-empresa.consultar'));

        $route = Route::getRoutes()->getByName('cajas.reporte-solicitudes-empresa.consultar');
        $this->assertContains('cajas.auth', $route->gatherMiddleware());
    }
}
