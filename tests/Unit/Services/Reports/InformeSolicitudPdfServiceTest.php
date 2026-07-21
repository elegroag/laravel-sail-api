<?php

namespace Tests\Unit\Services\Reports;

use App\Models\Mercurio10;
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
        $this->assertContains('9', $tipopcs);
        $this->assertContains('10', $tipopcs);
        $this->assertContains('11', $tipopcs);
        $this->assertContains('13', $tipopcs);
        $this->assertSame('Independiente', AuditoriaSolicitudResolver::labelsInforme()['13']);
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
            'codest' => null,
            'campos_corregir' => null,
        ]);

        try {
            $eventos = $service->buildEventos('1', $numero, '2026-03-15');

            $this->assertCount(1, $eventos);
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
