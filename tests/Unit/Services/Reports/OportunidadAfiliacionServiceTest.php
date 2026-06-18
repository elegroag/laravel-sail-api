<?php

namespace Tests\Unit\Services\Reports;

use App\Models\Mercurio32;
use App\Services\Reports\OportunidadAfiliacionService;
use App\Support\AfiliacionNormalizer;
use App\Support\TrabajadorTitularResolver;
use Tests\TestCase;

class OportunidadAfiliacionServiceTest extends TestCase
{
    public function test_trabajador_titular_resolver_returns_worker_name(): void
    {
        $index = TrabajadorTitularResolver::buildIndex([
            ['cedtra' => '999', 'nombre' => 'Maria Lopez'],
        ]);

        $this->assertSame('Maria Lopez', TrabajadorTitularResolver::resolve('999', $index));
        $this->assertSame('NO ENCONTRADO', TrabajadorTitularResolver::resolve('000', $index));
    }

    public function test_afiliacion_normalizer_formats_identification(): void
    {
        $model = Mercurio32::factory()->make([
            'tipdoc' => 'CC',
            'cedcon' => '5555555',
            'priape' => 'Gomez',
            'prinom' => 'Ana',
        ]);

        $normalized = AfiliacionNormalizer::normalize($model, 3, config('reportes.oportunidad_tipos')[3]);

        $this->assertSame('CC 5555555', $normalized['tipo_identificacion']);
        $this->assertStringContainsString('Gomez', $normalized['nombre']);
    }

    public function test_service_exposes_grouped_dataset_method(): void
    {
        $service = new OportunidadAfiliacionService;

        $this->assertTrue(method_exists($service, 'buildDataset'));
        $this->assertTrue(method_exists($service, 'buildDatasetGroupedByAportante'));
    }

    public function test_config_includes_required_affiliation_types(): void
    {
        $tipos = config('reportes.oportunidad_tipos');

        $this->assertArrayHasKey(1, $tipos);
        $this->assertArrayHasKey(2, $tipos);
        $this->assertArrayHasKey(3, $tipos);
        $this->assertArrayHasKey(4, $tipos);
        $this->assertArrayHasKey(9, $tipos);
        $this->assertArrayHasKey(10, $tipos);
        $this->assertArrayHasKey(11, $tipos);
    }
}
