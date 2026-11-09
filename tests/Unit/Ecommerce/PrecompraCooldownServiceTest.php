<?php

namespace Tests\Unit\Ecommerce;

use App\Models\PrecompraServicio;
use App\Services\Ecommerce\PrecompraCooldownService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PrecompraCooldownServiceTest extends TestCase
{
    private function precompra(array $items, int $minutosAtras = 5): PrecompraServicio
    {
        $p = new PrecompraServicio;
        $p->id = 10;
        $p->codser = 'S1';
        $p->numero = 1;
        $p->codben = $items[0];
        $p->items = array_map(fn ($c) => ['codben' => $c], $items);
        $p->fecha_precompra = now()->subMinutes($minutosAtras);

        return $p;
    }

    public function test_bloquea_mismo_beneficiario(): void
    {
        $service = new PrecompraCooldownService;
        $bloqueo = $service->resolverBloqueo(new Collection([$this->precompra(['A'])]), ['A']);

        $this->assertNotNull($bloqueo);
        $this->assertSame(['A'], $bloqueo['codbens']);
        $this->assertGreaterThan(0, $bloqueo['segundos_restantes']);
        $this->assertLessThanOrEqual(15 * 60, $bloqueo['segundos_restantes']);
    }

    public function test_bloquea_con_interseccion_parcial_multi_item(): void
    {
        $service = new PrecompraCooldownService;
        $bloqueo = $service->resolverBloqueo(new Collection([$this->precompra(['A', 'B'])]), ['B', 'C']);

        $this->assertNotNull($bloqueo);
        $this->assertSame(['B'], $bloqueo['codbens']);
    }

    public function test_no_bloquea_con_beneficiario_distinto(): void
    {
        $service = new PrecompraCooldownService;

        $this->assertNull($service->resolverBloqueo(new Collection([$this->precompra(['A'])]), ['Z']));
    }

    public function test_sin_precompras_vigentes_no_bloquea(): void
    {
        $service = new PrecompraCooldownService;

        $this->assertNull($service->resolverBloqueo(new Collection, ['A']));
    }

    public function test_ttl_es_veinte_minutos(): void
    {
        $this->assertSame(20, PrecompraCooldownService::TTL_MINUTES);
    }
}
