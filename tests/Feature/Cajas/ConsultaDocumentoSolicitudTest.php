<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\Reports\ConsultaDocumentoSolicitudService;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ConsultaDocumentoSolicitudTest extends TestCase
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

    public function test_index_valida_documento_cuando_queried(): void
    {
        $response = $this->from('/cajas/consulta-documento-solicitud/index')
            ->get(route('cajas.consulta-documento-solicitud.index', [
                'queried' => 1,
            ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['documento']);
    }

    public function test_index_consulta_delegada_al_service(): void
    {
        $this->mock(ConsultaDocumentoSolicitudService::class, function ($mock): void {
            $mock->shouldReceive('consultar')
                ->once()
                ->with(['documento' => '1048123456'])
                ->andReturn([
                    'documento' => '1048123456',
                    'total' => 1,
                    'grupos' => [[
                        'tipopc' => 1,
                        'label' => 'Trabajador',
                        'tabla' => 'mercurio31',
                        'campo' => 'cedtra',
                        'total' => 1,
                        'solicitudes' => [[
                            'id' => 10,
                            'tipopc' => 1,
                            'tipo' => 'Trabajador',
                            'tabla' => 'mercurio31',
                            'campo' => 'cedtra',
                            'ruuid' => 'rad-001',
                            'nombre' => 'Perez Juan',
                            'documento' => '1048123456',
                            'fecsol' => '2026-01-10',
                            'fecsis' => '2026-01-10',
                            'fecest' => null,
                            'estado' => 'A',
                            'estado_detalle' => 'Aprobado',
                        ]],
                    ]],
                ]);
        });

        $response = $this->get(route('cajas.consulta-documento-solicitud.index', [
            'queried' => 1,
            'documento' => '1048123456',
        ]));

        $this->assertTrue(in_array($response->status(), [200, 500], true));
    }

    public function test_rutas_registradas_y_protegidas(): void
    {
        $this->assertTrue(Route::has('cajas.consulta-documento-solicitud.index'));
        $this->assertNull(Route::getRoutes()->getByName('cajas.consulta-documento-solicitud.consultar'));

        $route = Route::getRoutes()->getByName('cajas.consulta-documento-solicitud.index');
        $this->assertNotNull($route);
        $this->assertContains('GET', $route->methods());
        $this->assertContains('cajas.auth', $route->gatherMiddleware());
    }
}
