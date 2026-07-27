<?php

namespace Tests\Unit\Services\Reports;

use App\Models\AuditoriaMercurio31;
use App\Models\Mercurio10;
use App\Models\Mercurio31;
use App\Services\Reports\InformeSolicitudPdfService;
use App\Support\AuditoriaSolicitudFieldsBuilder;
use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InformeSolicitudPdfServiceTest extends TestCase
{
    public function test_resolver_expone_tipopcs_y_labels_del_informe(): void
    {
        $tipopcs = AuditoriaSolicitudResolver::tipopcsInforme();

        $this->assertContains('1', $tipopcs);
        $this->assertContains('2', $tipopcs);
        $this->assertContains('3', $tipopcs);
        $this->assertContains('4', $tipopcs);
        $this->assertContains('5', $tipopcs);
        $this->assertContains('8', $tipopcs);
        $this->assertContains('9', $tipopcs);
        $this->assertContains('10', $tipopcs);
        $this->assertContains('11', $tipopcs);
        $this->assertContains('13', $tipopcs);
        $this->assertContains('14', $tipopcs);
        $this->assertSame('Independiente', AuditoriaSolicitudResolver::labelsInforme()['13']);
        $this->assertSame('Comunitaria', AuditoriaSolicitudResolver::labelsInforme()['11']);
        $this->assertSame('Certificado', AuditoriaSolicitudResolver::labelsInforme()['8']);
    }

    public function test_is_archivado_distingue_auditoria_de_solicitud_viva(): void
    {
        $this->assertTrue(AuditoriaSolicitudResolver::isArchivado(new AuditoriaMercurio31));
        $this->assertFalse(AuditoriaSolicitudResolver::isArchivado(new Mercurio31));
    }

    public function test_fields_builder_lee_atributos_sin_getters_como_auditoria(): void
    {
        $builder = app(AuditoriaSolicitudFieldsBuilder::class);
        $reflection = new \ReflectionClass($builder);
        $method = $reflection->getMethod('get');
        $method->setAccessible(true);

        $snapshot = (object) [
            'nit' => '900123456',
            'razsoc' => 'Empresa Auditada SAS',
            'cedtra' => '1098765432',
        ];

        $this->assertSame('900123456', $method->invoke($builder, $snapshot, 'getNit'));
        $this->assertSame('Empresa Auditada SAS', $method->invoke($builder, $snapshot, 'getRazsoc'));
        $this->assertSame('1098765432', $method->invoke($builder, $snapshot, 'getCedtra'));
        $this->assertNull($method->invoke($builder, $snapshot, 'getCampoInexistente'));
    }

    public function test_build_eventos_sintetiza_radicacion_cuando_no_hay_historial(): void
    {
        $service = app(InformeSolicitudPdfService::class);
        $numeroInexistente = 1_888_001;

        Mercurio10::query()
            ->where('tipopc', '1')
            ->where('numero', $numeroInexistente)
            ->delete();

        $eventos = $service->buildEventos('1', $numeroInexistente, '2026-03-15');

        $this->assertCount(1, $eventos);
        $this->assertSame('2026-03-15', $eventos[0]['fecha']);
        $this->assertSame('Radicado/enviado', $eventos[0]['estado']);
        $this->assertStringContainsString('Sin eventos', $eventos[0]['nota']);
    }

    public function test_build_eventos_mapea_historial_mercurio10(): void
    {
        $service = app(InformeSolicitudPdfService::class);
        $numero = 1_888_002;

        DB::table('mercurio10')
            ->where('tipopc', '1')
            ->where('numero', $numero)
            ->delete();

        DB::table('mercurio10')->insert([
            'tipopc' => '1',
            'numero' => $numero,
            'item' => 1,
            'estado' => 'A',
            'nota' => 'Solicitud aprobada en prueba',
            'fecsis' => '2026-03-20',
            'ruuid' => 'EVT-TEST-RUUID-001',
            'codest' => null,
            'campos_corregir' => null,
        ]);

        try {
            $eventos = $service->buildEventos('1', $numero, '2026-03-15');

            $this->assertCount(1, $eventos);
            $this->assertSame('EVT-TEST-RUUID-001', $eventos[0]['ruuid']);
            $this->assertStringContainsString('2026-03-20', (string) $eventos[0]['fecha']);
            $this->assertSame('Aprobado', $eventos[0]['estado']);
            $this->assertStringContainsString('aprobada', strtolower($eventos[0]['nota']));
        } finally {
            DB::table('mercurio10')
                ->where('tipopc', '1')
                ->where('numero', $numero)
                ->delete();
        }
    }

    public function test_fields_builder_es_inyectable(): void
    {
        $service = app(InformeSolicitudPdfService::class);

        $this->assertInstanceOf(InformeSolicitudPdfService::class, $service);
        $this->assertInstanceOf(AuditoriaSolicitudFieldsBuilder::class, app(AuditoriaSolicitudFieldsBuilder::class));
    }
}
