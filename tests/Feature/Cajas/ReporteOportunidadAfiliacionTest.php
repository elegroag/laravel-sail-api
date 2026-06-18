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

    public function test_por_aportante_devuelve_xlsx_con_grupos(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDatasetGroupedByAportante')
                ->once()
                ->andReturn($this->sampleDatasetGrouped());
        });

        $response = $this->post(route('cajas.reportes.por-aportante'), [
            'fecini' => '2026-03-01',
            'fecfin' => '2026-03-31',
            'campo_fecha' => 'fecsol',
            'modalidad' => 'aportante',
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $sheet = $this->readSpreadsheetFromResponse($response->streamedContent());
        $rows = $sheet->toArray();

        $this->assertSame('FECHA DE LA SOLICITUD DE AFILIACIÓN', $rows[0][0]);
        $this->assertSame('Empresa Alpha', $rows[1][5]);
        $this->assertSame('Empresa Beta', $rows[2][5]);
        $this->assertSame('Empresa Alpha', $rows[3][5]);
    }

    public function test_por_trabajador_desglosa_beneficiarios(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->andReturn($this->sampleDatasetDetailed());
        });

        $response = $this->post(route('cajas.reportes.por-trabajador'), [
            'fecini' => '2026-04-01',
            'fecfin' => '2026-04-30',
            'campo_fecha' => 'fecsol',
            'modalidad' => 'trabajador',
        ]);

        $response->assertOk();
        $sheet = $this->readSpreadsheetFromResponse($response->streamedContent());
        $rows = array_slice($sheet->toArray(), 1);

        $this->assertCount(3, $rows);
        $this->assertSame('TRABAJADOR', $rows[0][7]);
        $this->assertSame('CONYUGE', $rows[1][7]);
        $this->assertSame('BENEFICIARIO', $rows[2][7]);
        $this->assertSame('Vega Oscar', $rows[1][8]);
    }

    public function test_filtro_por_tipo_solo_trabajador(): void
    {
        $this->mock(OportunidadAfiliacionService::class, function ($mock): void {
            $mock->shouldReceive('buildDataset')
                ->once()
                ->withArgs(function (array $filtros): bool {
                    return ($filtros['tipafis'] ?? null) === [1];
                })
                ->andReturn([$this->sampleDatasetDetailed()[0]]);
        });

        $response = $this->post(route('cajas.reportes.por-trabajador'), [
            'fecini' => '2026-05-01',
            'fecfin' => '2026-05-31',
            'campo_fecha' => 'fecsol',
            'tipafis' => [1],
            'modalidad' => 'trabajador',
        ]);

        $response->assertOk();
        $sheet = $this->readSpreadsheetFromResponse($response->streamedContent());
        $rows = array_slice($sheet->toArray(), 1);

        $this->assertCount(1, $rows);
        $this->assertSame('TRABAJADOR', $rows[0][7]);
    }

    public function test_validacion_fechas_invalidas(): void
    {
        $response = $this->postJson(route('cajas.reportes.por-aportante'), [
            'fecini' => '2026-99-99',
            'fecfin' => '2026-05-31',
            'campo_fecha' => 'fecsol',
            'modalidad' => 'aportante',
        ]);

        $response->assertStatus(422);
    }

    public function test_rutas_registradas_y_protegidas_por_cajas_auth(): void
    {
        $this->assertTrue(Route::has('cajas.reportes.index'));
        $this->assertTrue(Route::has('cajas.reportes.por-aportante'));
        $this->assertTrue(Route::has('cajas.reportes.por-trabajador'));

        $route = Route::getRoutes()->getByName('cajas.reportes.por-aportante');
        $this->assertContains('cajas.auth', $route->gatherMiddleware());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sampleDatasetGrouped(): array
    {
        return [
            [
                'tipopc' => 1,
                'label' => 'TRABAJADOR',
                'id' => 10,
                'fecsol' => '2026-03-02',
                'fecapr' => '2026-03-10',
                'sat_fecapr' => '2026-03-09',
                'estado' => 'Pendiente',
                'tipo_identificacion' => 'CC 1010101010',
                'nombre' => 'Diaz Luis',
                'nit' => '800100200',
                'razsoc' => 'Empresa Alpha',
                'dias_vencidos' => 3,
            ],
            [
                'tipopc' => 1,
                'label' => 'TRABAJADOR',
                'id' => 11,
                'fecsol' => '2026-03-03',
                'fecapr' => '2026-03-11',
                'sat_fecapr' => '2026-03-10',
                'estado' => 'Pendiente',
                'tipo_identificacion' => 'CC 2020202020',
                'nombre' => 'Rios Ana',
                'nit' => '800300400',
                'razsoc' => 'Empresa Beta',
                'dias_vencidos' => 2,
            ],
            [
                'tipopc' => 2,
                'label' => 'EMPRESA',
                'id' => 12,
                'fecsol' => '2026-03-01',
                'fecapr' => '2026-03-08',
                'sat_fecapr' => '2026-03-07',
                'estado' => 'Pendiente',
                'tipo_identificacion' => 'NIT 800100200',
                'nombre' => 'Empresa Alpha',
                'nit' => '800100200',
                'razsoc' => 'Empresa Alpha',
                'dias_vencidos' => 4,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sampleDatasetDetailed(): array
    {
        return [
            [
                'tipopc' => 1,
                'label' => 'TRABAJADOR',
                'id' => 20,
                'fecsol' => '2026-04-01',
                'fecapr' => '2026-04-10',
                'sat_fecapr' => '2026-04-09',
                'estado' => 'Pendiente',
                'tipo_identificacion' => 'CC 3030303030',
                'nombre' => 'Vega Oscar',
                'nit' => '',
                'razsoc' => '',
                'cedtra' => '3030303030',
                'dias_vencidos' => 5,
            ],
            [
                'tipopc' => 3,
                'label' => 'CONYUGE',
                'id' => 21,
                'fecsol' => '2026-04-02',
                'fecapr' => '2026-04-11',
                'sat_fecapr' => '2026-04-10',
                'estado' => 'Pendiente',
                'tipo_identificacion' => 'CC 4040404040',
                'nombre' => 'Vega Laura',
                'nit' => '',
                'razsoc' => '',
                'cedtra' => '3030303030',
                'dias_vencidos' => 4,
            ],
            [
                'tipopc' => 4,
                'label' => 'BENEFICIARIO',
                'id' => 22,
                'fecsol' => '2026-04-03',
                'fecapr' => '2026-04-12',
                'sat_fecapr' => '2026-04-11',
                'estado' => 'Pendiente',
                'tipo_identificacion' => 'CC 5050505050',
                'nombre' => 'Vega Mateo',
                'nit' => '',
                'razsoc' => '',
                'cedtra' => '3030303030',
                'dias_vencidos' => 3,
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
