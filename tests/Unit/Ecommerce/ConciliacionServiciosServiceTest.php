<?php

namespace Tests\Unit\Ecommerce;

use App\Models\PrecompraServicio;
use App\Services\Ecommerce\ConciliacionServiciosService;
use App\Services\Ecommerce\EstadoPrecompra;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ConciliacionServiciosServiceTest extends TestCase
{
    private ConciliacionServiciosService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConciliacionServiciosService;
    }

    private function precompra(array $attrs): PrecompraServicio
    {
        $p = new PrecompraServicio;
        $p->id = $attrs['id'] ?? 1;
        $p->documento = $attrs['documento'] ?? '100';
        $p->codser = $attrs['codser'] ?? 'S1';
        $p->numero = $attrs['numero'] ?? 1;
        $p->codben = $attrs['codben'] ?? '200';
        $p->ref_payco = $attrs['ref_payco'] ?? 'REF1';
        $p->valor = $attrs['valor'] ?? 10000;
        $p->estado = $attrs['estado'] ?? EstadoPrecompra::PAGADO;
        $p->fecha_pago = $attrs['fecha_pago'] ?? Carbon::parse('2026-01-15 10:00:00');

        return $p;
    }

    private function venta(array $attrs): array
    {
        return [
            'marca' => $attrs['marca'] ?? 'FV',
            'documento' => $attrs['documento'] ?? '0000001',
            'fecha' => $attrs['fecha'] ?? '2026-01-15',
            'hora' => $attrs['hora'] ?? '10:00:00',
            'estado' => $attrs['estado'] ?? 'A',
            'valpago' => $attrs['valpago'] ?? 10000,
            'cedtra_titular' => $attrs['cedtra_titular'] ?? '100',
            'refpago' => $attrs['refpago'] ?? 'REF1',
            'valor_forma_pago' => $attrs['valor_forma_pago'] ?? 10000,
            'items_count' => $attrs['items_count'] ?? 1,
        ];
    }

    /** @return array<int, list<string>> */
    private function refsEpayco(PrecompraServicio $pre, string ...$refs): array
    {
        return [(int) $pre->id => array_values($refs)];
    }

    public function test_clasifica_conciliada(): void
    {
        $pre = $this->precompra(['ref_payco' => 'R1', 'valor' => 15000]);
        $venta = $this->venta(['refpago' => 'R1', 'valor_forma_pago' => 15000, 'valpago' => 15000]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['R1' => $venta],
            new Collection(['R1' => $pre]),
            null,
            $this->refsEpayco($pre, 'R1')
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::CONCILIADA]);
        $this->assertSame(ConciliacionServiciosService::CONCILIADA, $result['filas'][0]['estado']);
        $this->assertFalse($result['filas'][0]['puede_registrar']);
    }

    public function test_clasifica_pagada_sin_venta(): void
    {
        $pre = $this->precompra(['ref_payco' => 'R2']);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            [],
            new Collection(['R2' => $pre]),
            null,
            $this->refsEpayco($pre, 'R2')
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::PAGADA_SIN_VENTA]);
        $this->assertTrue($result['filas'][0]['puede_registrar']);
    }

    public function test_clasifica_valor_diferente(): void
    {
        $pre = $this->precompra(['ref_payco' => 'R3', 'valor' => 10000]);
        $venta = $this->venta(['refpago' => 'R3', 'valor_forma_pago' => 12000, 'valpago' => 12000]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['R3' => $venta],
            new Collection(['R3' => $pre]),
            null,
            $this->refsEpayco($pre, 'R3')
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::VALOR_DIFERENTE]);
    }

    public function test_clasifica_venta_sin_precompra_pa(): void
    {
        $venta = $this->venta(['refpago' => 'R4']);

        $result = $this->service->clasificar(
            new Collection,
            ['R4' => $venta],
            new Collection
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA]);
        $this->assertNull($result['filas'][0]['precompra_id']);
        $this->assertFalse($result['filas'][0]['puede_registrar']);
    }

    public function test_venta_con_precompra_no_pa_se_reporta(): void
    {
        $pre = $this->precompra([
            'ref_payco' => 'R5',
            'estado' => EstadoPrecompra::ABANDONADA,
        ]);
        $venta = $this->venta(['refpago' => 'R5']);

        $result = $this->service->clasificar(
            new Collection,
            ['R5' => $venta],
            new Collection(['R5' => $pre])
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA]);
        $this->assertSame($pre->id, $result['filas'][0]['precompra_id']);
        $this->assertSame(EstadoPrecompra::ABANDONADA, $result['filas'][0]['estado_precompra']);
    }

    public function test_tolerancia_de_centavos_sigue_conciliada(): void
    {
        $pre = $this->precompra(['ref_payco' => 'R6', 'valor' => 10000.004]);
        $venta = $this->venta(['refpago' => 'R6', 'valor_forma_pago' => 10000.0]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['R6' => $venta],
            new Collection(['R6' => $pre]),
            null,
            $this->refsEpayco($pre, 'R6')
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::CONCILIADA]);
    }

    public function test_clasifica_aceptada_epayco_no_pa(): void
    {
        $pre = $this->precompra([
            'id' => 77,
            'ref_payco' => 'R7',
            'estado' => EstadoPrecompra::ABANDONADA,
        ]);

        $result = $this->service->clasificar(
            new Collection,
            [],
            new Collection(['R7' => $pre]),
            new Collection([$pre])
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::ACEPTADA_EPAYCO_NO_PA]);
        $this->assertSame(ConciliacionServiciosService::ACEPTADA_EPAYCO_NO_PA, $result['filas'][0]['estado']);
        $this->assertSame(EstadoPrecompra::ABANDONADA, $result['filas'][0]['estado_precompra']);
        $this->assertTrue($result['filas'][0]['puede_registrar']);
    }

    public function test_aceptada_epayco_no_pa_no_duplica_si_ya_es_pa(): void
    {
        $pre = $this->precompra(['ref_payco' => 'R8', 'estado' => EstadoPrecompra::PAGADO]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            [],
            new Collection(['R8' => $pre]),
            new Collection([$pre])
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::PAGADA_SIN_VENTA]);
        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::ACEPTADA_EPAYCO_NO_PA]);
    }

    public function test_venta_ve_y_estado_no_a_se_omite(): void
    {
        $pre = $this->precompra([
            'ref_payco' => 'R9',
            'estado' => EstadoPrecompra::VENCIDA,
        ]);
        $venta = $this->venta(['refpago' => 'R9', 'estado' => 'X']);

        $result = $this->service->clasificar(
            new Collection,
            ['R9' => $venta],
            new Collection(['R9' => $pre])
        );

        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA]);
        $this->assertCount(0, $result['filas']);
    }

    public function test_venta_dv_y_estado_no_a_se_omite(): void
    {
        $pre = $this->precompra([
            'ref_payco' => 'R9B',
            'estado' => EstadoPrecompra::DEVOLUCION,
        ]);
        $venta = $this->venta(['refpago' => 'R9B', 'estado' => 'N']);

        $result = $this->service->clasificar(
            new Collection,
            ['R9B' => $venta],
            new Collection(['R9B' => $pre])
        );

        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA]);
        $this->assertCount(0, $result['filas']);
    }

    public function test_venta_ve_con_estado_a_sigue_reportando_huerfana(): void
    {
        $pre = $this->precompra([
            'ref_payco' => 'R9C',
            'estado' => EstadoPrecompra::VENCIDA,
        ]);
        $venta = $this->venta(['refpago' => 'R9C', 'estado' => 'A']);

        $result = $this->service->clasificar(
            new Collection,
            ['R9C' => $venta],
            new Collection(['R9C' => $pre])
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA]);
        $this->assertNull($result['filas'][0]['precompra_id']);
    }

    public function test_no_concilia_si_solo_coincide_ref_de_epayco(): void
    {
        $pre = $this->precompra([
            'id' => 50,
            'ref_payco' => 'REF_VIEJA',
            'valor' => 10000,
        ]);
        $venta = $this->venta([
            'refpago' => 'REF_EPAYCO',
            'valor_forma_pago' => 10000,
            'valpago' => 10000,
        ]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['REF_EPAYCO' => $venta],
            new Collection(['REF_VIEJA' => $pre]),
            null,
            [50 => ['REF_EPAYCO']]
        );

        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::CONCILIADA]);
        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::PAGADA_SIN_VENTA]);
        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA]);
    }

    public function test_venta_y_precompra_sin_tx_epayco_no_concilia(): void
    {
        $pre = $this->precompra(['id' => 60, 'ref_payco' => 'R10', 'valor' => 5000]);
        $venta = $this->venta(['refpago' => 'R10', 'valor_forma_pago' => 5000, 'valpago' => 5000]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['R10' => $venta],
            new Collection(['R10' => $pre]),
            null,
            []
        );

        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::CONCILIADA]);
        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::SIN_TX_EPAYCO]);
    }

    public function test_concilia_solo_con_ambas_condiciones(): void
    {
        $pre = $this->precompra(['id' => 70, 'ref_payco' => 'R11', 'valor' => 8000]);
        $venta = $this->venta(['refpago' => 'R11', 'valor_forma_pago' => 8000, 'valpago' => 8000]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['R11' => $venta],
            new Collection(['R11' => $pre]),
            null,
            $this->refsEpayco($pre, 'R11', 'OTRA_REF')
        );

        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::CONCILIADA]);
        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::SIN_TX_EPAYCO]);
    }

    public function test_venta_estado_no_aprobada_no_concilia(): void
    {
        $pre = $this->precompra(['id' => 80, 'ref_payco' => 'R12', 'valor' => 9000]);
        $venta = $this->venta([
            'refpago' => 'R12',
            'valor_forma_pago' => 9000,
            'valpago' => 9000,
            'estado' => 'X',
        ]);

        $result = $this->service->clasificar(
            new Collection([$pre]),
            ['R12' => $venta],
            new Collection(['R12' => $pre]),
            null,
            $this->refsEpayco($pre, 'R12')
        );

        $this->assertSame(0, $result['resumen'][ConciliacionServiciosService::CONCILIADA]);
        $this->assertSame(1, $result['resumen'][ConciliacionServiciosService::VENTA_NO_APROBADA]);
        $this->assertSame('X', $result['filas'][0]['venta_estado']);
    }
}
