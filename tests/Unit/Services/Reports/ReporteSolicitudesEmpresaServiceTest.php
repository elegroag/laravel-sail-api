<?php

namespace Tests\Unit\Services\Reports;

use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Services\Reports\ReporteSolicitudesEmpresaService;
use App\Support\AfiliacionNormalizer;
use App\Support\DiasHabilesCalculator;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use Tests\TestCase;

class ReporteSolicitudesEmpresaServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }
    }

    public function test_expone_labels_de_tipos_y_estados(): void
    {
        $tipos = ReporteSolicitudesEmpresaService::tipopcLabels();
        $estados = ReporteSolicitudesEmpresaService::estadosLabels();

        $this->assertSame('Trabajador', $tipos[1]);
        $this->assertSame('Cónyuge', $tipos[3]);
        $this->assertSame('Beneficiario', $tipos[4]);
        $this->assertArrayHasKey('A', $estados);
        $this->assertArrayHasKey('X', $estados);
        $this->assertArrayHasKey('T', $estados);
    }

    public function test_normalize_row_mapea_campos_homogeneos(): void
    {
        $service = app(ReporteSolicitudesEmpresaService::class);
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('normalizeRow');
        $method->setAccessible(true);

        $model = Mercurio31::factory()->make([
            'id' => 55,
            'ruuid' => 'rad-trab-55',
            'nit' => '900111222',
            'razsoc' => 'Empresa Demo',
            'cedtra' => '123456789',
            'tipdoc' => '1',
            'priape' => 'Lopez',
            'prinom' => 'Ana',
            'fecsol' => '2026-02-01',
            'fecapr' => '2026-02-05',
            'estado' => 'A',
            'tipo' => 'E',
            'coddoc' => '3',
            'documento' => '900111222',
        ]);

        $row = $method->invoke($service, $model, 1, 'Trabajador');

        $this->assertSame(1, $row['tipopc']);
        $this->assertSame('Trabajador', $row['tipo_label']);
        $this->assertSame('rad-trab-55', $row['ruuid']);
        $this->assertSame('123456789', $row['documento_afiliado']);
        $this->assertSame('900111222', $row['nit']);
        $this->assertSame('2026-02-01', $row['fecsol']);
        $this->assertNotEmpty($row['nombre']);
    }

    public function test_normalize_row_conyuge_sin_nit(): void
    {
        $service = app(ReporteSolicitudesEmpresaService::class);
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('normalizeRow');
        $method->setAccessible(true);

        $model = Mercurio32::factory()->make([
            'id' => 77,
            'ruuid' => 'rad-con-77',
            'cedcon' => '5555555',
            'cedtra' => '999',
            'tipdoc' => '1',
            'priape' => 'Gomez',
            'prinom' => 'Lucia',
            'fecsol' => '2026-03-01',
            'fecapr' => null,
            'estado' => 'P',
            'tipo' => 'E',
            'coddoc' => '3',
            'documento' => '900111222',
        ]);

        $row = $method->invoke($service, $model, 3, 'Cónyuge');

        $this->assertSame(3, $row['tipopc']);
        $this->assertSame('5555555', $row['documento_afiliado']);
        $this->assertSame('', $row['nit']);
        $this->assertNull($row['fecha_cierre']);
    }

    public function test_afiliacion_normalizer_disponible_para_nombre(): void
    {
        $this->assertTrue(method_exists(AfiliacionNormalizer::class, 'nombre'));
        $this->assertTrue(class_exists(DiasHabilesCalculator::class));
    }

    public function test_pagina_desde_cache_sin_volver_a_consultar(): void
    {
        Cache::flush();

        $service = app(ReporteSolicitudesEmpresaService::class);

        $rows = [];
        for ($i = 1; $i <= 30; $i++) {
            $rows[] = [
                'tipopc' => 1,
                'tipo_label' => 'Trabajador',
                'id' => $i,
                'ruuid' => "rad-{$i}",
                'documento_afiliado' => (string) (1000 + $i),
                'nombre' => "Persona {$i}",
                'nit' => '900111222',
                'fecsol' => '2026-01-'.str_pad((string) min($i, 28), 2, '0', STR_PAD_LEFT),
                'estado' => 'Pendiente',
                'fecha_cierre' => null,
            ];
        }

        $filtros = [
            'documento' => '900111222',
            'coddoc' => '3',
            'fecini' => '2026-01-01',
            'fecfin' => '2026-01-31',
            'tipopcs' => [1],
            'page' => 1,
            'per_page' => 10,
            'refresh' => false,
        ];

        $cacheKey = $service->cacheKey($filtros);
        Cache::put($cacheKey, [
            'empresa' => [
                'documento' => '900111222',
                'coddoc' => '3',
                'tipo' => 'E',
            ],
            'rows' => $rows,
        ], now()->addMinutes(30));

        $page1 = $service->consultar($filtros);

        $this->assertTrue($page1['from_cache']);
        $this->assertSame(30, $page1['total']);
        $this->assertCount(10, $page1['rows']);
        $this->assertSame(1, $page1['pagination']['page']);
        $this->assertSame(3, $page1['pagination']['last_page']);
        $this->assertSame('rad-1', $page1['rows'][0]['ruuid']);

        $page2 = $service->consultar([
            ...$filtros,
            'page' => 2,
        ]);

        $this->assertTrue($page2['from_cache']);
        $this->assertSame('rad-11', $page2['rows'][0]['ruuid']);
        $this->assertSame(2, $page2['pagination']['page']);
        $this->assertSame(11, $page2['pagination']['from']);
        $this->assertSame(20, $page2['pagination']['to']);
    }
}
