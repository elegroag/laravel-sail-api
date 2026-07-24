<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\Reports\OportunidadAfiliacionService;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class ReporteOportunidadAfiliacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        $this->withoutMiddleware(CajasAuthenticated::class);
    }

    public function test_previsualizar_devuelve_resumen_json(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildResumen')
                ->once()
                ->andReturn([
                    'total' => 3,
                    'en_termino' => 1,
                    'vencido' => 1,
                    'en_tramite' => 1,
                ]);
        });

        $response = $this->getJson(route('cajas.reporte-oportunidad.previsualizar', [
            'fecini' => '2026-03-01',
            'fecfin' => '2026-03-31',
        ]));

        $response->assertOk();
        $response->assertJsonPath('resumen.total', 3);
        $response->assertJsonPath('resumen.vencido', 1);
        $response->assertJsonPath('umbral_dias', 3);
    }

    public function test_exportar_devuelve_xlsx_con_columnas_del_nuevo_formato(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->andReturn($this->sampleDataset());
        });

        $response = $this->post(route('cajas.reporte-oportunidad.exportar'), [
            'fecini' => '2026-03-01',
            'fecfin' => '2026-03-31',
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $sheet = $this->readSpreadsheetFromResponse($response->streamedContent());
        $rows = $sheet->toArray();

        $this->assertSame('RUUID', $rows[0][0]);
        $this->assertSame('Estado', $rows[0][1]);
        $this->assertSame('Fecha envio a caja', $rows[0][2]);
        $this->assertSame('Fecha de aprobacion', $rows[0][3]);
        $this->assertSame('Dias habiles tramite', $rows[0][4]);
        $this->assertSame('Tipo identificacion', $rows[0][7]);
        $this->assertSame('No. identificacion', $rows[0][8]);
        $this->assertSame('Nombres y apellidos', $rows[0][9]);
        $this->assertSame('ruuid-trabajador-10', $rows[1][0]);
        $this->assertSame('Aprobado', $rows[1][1]);
        $this->assertEquals(4, $rows[1][4]);
        $this->assertSame('CC', $rows[1][7]);
        $this->assertSame('1010101010', $rows[1][8]);
        $this->assertSame('Vega Mateo', $rows[2][9]);
    }

    public function test_exportar_conyuge_omite_nit_y_razon_social(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->andReturn([[
                    'tipopc' => 3,
                    'label' => 'CONYUGE',
                    'id' => 33,
                    'ruuid' => 'ruuid-conyuge-33',
                    'fecsol' => '2026-03-10',
                    'fecapr' => '2026-03-12',
                    'fecha_cierre' => '2026-03-12',
                    'estado' => 'Aprobado',
                    'tipo_documento' => 'CC',
                    'numero_identificacion' => '5555555',
                    'nombre' => 'Ana Gomez',
                    'nit' => '',
                    'razsoc' => '',
                    'dias_habiles' => 2,
                ]]);
        });

        $response = $this->post(route('cajas.reporte-oportunidad.exportar'), [
            'fecini' => '2026-03-01',
            'fecfin' => '2026-03-31',
            'tipafis' => [3],
        ]);

        $response->assertOk();

        $sheet = $this->readSpreadsheetFromResponse($response->streamedContent());
        $rows = $sheet->toArray();

        $this->assertSame('RUUID', $rows[0][0]);
        $this->assertSame('Dias habiles tramite', $rows[0][4]);
        $this->assertSame('Tipo identificacion', $rows[0][5]);
        $this->assertSame('No. identificacion', $rows[0][6]);
        $this->assertSame('Nombres y apellidos', $rows[0][7]);
        $this->assertNotContains('NIT aportante', $rows[0]);
        $this->assertNotContains('Razon social aportante', $rows[0]);
        $this->assertSame('CC', $rows[1][5]);
        $this->assertSame('5555555', $rows[1][6]);
        $this->assertSame('Ana Gomez', $rows[1][7]);
    }

    public function test_exportar_beneficiario_omite_nit_y_razon_social(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->andReturn([[
                    'tipopc' => 4,
                    'label' => 'BENEFICIARIO',
                    'id' => 44,
                    'ruuid' => 'ruuid-beneficiario-44',
                    'fecsol' => '2026-04-01',
                    'fecapr' => '2026-04-03',
                    'fecha_cierre' => '2026-04-03',
                    'estado' => 'Aprobado',
                    'tipo_documento' => 'TI',
                    'numero_identificacion' => '1098765432',
                    'nombre' => 'Mateo Vega',
                    'nit' => '',
                    'razsoc' => '',
                    'dias_habiles' => 2,
                ]]);
        });

        $response = $this->post(route('cajas.reporte-oportunidad.exportar'), [
            'fecini' => '2026-04-01',
            'fecfin' => '2026-04-30',
            'tipafis' => [4],
        ]);

        $response->assertOk();

        $sheet = $this->readSpreadsheetFromResponse($response->streamedContent());
        $rows = $sheet->toArray();

        $this->assertNotContains('NIT aportante', $rows[0]);
        $this->assertNotContains('Razon social aportante', $rows[0]);
        $this->assertSame('Tipo identificacion', $rows[0][5]);
        $this->assertSame('TI', $rows[1][5]);
        $this->assertSame('1098765432', $rows[1][6]);
        $this->assertSame('Mateo Vega', $rows[1][7]);
    }

    public function test_filtro_por_tipo_solo_trabajador(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->withArgs(function (array $filtros): bool {
                    return ($filtros['tipafis'] ?? null) === [1];
                })
                ->andReturn([$this->sampleDataset()[0]]);
        });

        $response = $this->post(route('cajas.reporte-oportunidad.exportar'), [
            'fecini' => '2026-05-01',
            'fecfin' => '2026-05-31',
            'tipafis' => [1],
        ]);

        $response->assertOk();
    }

    public function test_validacion_fechas_invalidas(): void
    {
        $response = $this->getJson(route('cajas.reporte-oportunidad.previsualizar', [
            'fecini' => '2026-99-99',
            'fecfin' => '2026-05-31',
        ]));

        $response->assertStatus(422);
    }

    public function test_rutas_registradas_y_protegidas_por_cajas_auth(): void
    {
        $this->assertTrue(Route::has('cajas.reporte-oportunidad.index'));
        $this->assertTrue(Route::has('cajas.reporte-oportunidad.previsualizar'));
        $this->assertTrue(Route::has('cajas.reporte-oportunidad.exportar'));

        $route = Route::getRoutes()->getByName('cajas.reporte-oportunidad.exportar');
        $this->assertContains('cajas.auth', $route->gatherMiddleware());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sampleDataset(): array
    {
        return [
            [
                'tipopc' => 1,
                'label' => 'TRABAJADOR',
                'id' => 10,
                'ruuid' => 'ruuid-trabajador-10',
                'fecsol' => '2026-03-02',
                'fecapr' => '2026-03-06',
                'fecest' => null,
                'fecha_cierre' => '2026-03-06',
                'estado' => 'Aprobado',
                'tipdoc' => '1',
                'documento' => '1010101010',
                'tipo_documento' => 'CC',
                'numero_identificacion' => '1010101010',
                'tipo_identificacion' => 'CC 1010101010',
                'nombre' => 'Diaz Luis',
                'nit' => '800100200',
                'razsoc' => 'Empresa Alpha',
                'cedtra_titular' => '',
                'nombre_titular' => '',
                'dias_habiles' => 4,
                'estado_oportunidad' => 'EN_TERMINO',
            ],
            [
                'tipopc' => 4,
                'label' => 'BENEFICIARIO',
                'id' => 22,
                'ruuid' => 'ruuid-beneficiario-22',
                'fecsol' => '2026-04-03',
                'fecapr' => '2026-04-12',
                'fecest' => null,
                'fecha_cierre' => '2026-04-12',
                'estado' => 'Pendiente',
                'tipdoc' => '1',
                'documento' => '5050505050',
                'tipo_documento' => 'CC',
                'numero_identificacion' => '5050505050',
                'tipo_identificacion' => 'CC 5050505050',
                'nombre' => 'Vega Mateo',
                'nit' => '',
                'razsoc' => '',
                'cedtra_titular' => '3030303030',
                'nombre_titular' => 'Maria Lopez',
                'dias_habiles' => 7,
                'estado_oportunidad' => 'VENCIDO',
            ],
        ];
    }

    private function readSpreadsheetFromResponse(string $content): Worksheet
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'oportunidad_test_');
        file_put_contents($tempFile, $content);
        $spreadsheet = IOFactory::load($tempFile);
        @unlink($tempFile);

        return $spreadsheet->getActiveSheet();
    }
}
