<?php

namespace Tests\Unit\Ecommerce;

use App\Models\PrecompraServicio;
use App\Services\Ecommerce\AdmserviciosService;
use App\Services\Ecommerce\EstadoPrecompra;
use Tests\TestCase;

class AdmserviciosServiceTest extends TestCase
{
    private AdmserviciosService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdmserviciosService;
    }

    public function test_motivo_texto_vacio_sin_motivo(): void
    {
        $precompra = new PrecompraServicio;
        $precompra->motivo_desestimacion = null;
        $precompra->detalle_desestimacion = null;

        $this->assertSame('', $this->service->motivoTexto($precompra));
    }

    public function test_motivo_texto_abandono_checkout(): void
    {
        $precompra = new PrecompraServicio;
        $precompra->motivo_desestimacion = EstadoPrecompra::MOTIVO_ABANDONO_CHECKOUT;
        $precompra->detalle_desestimacion = '';

        $this->assertSame('Abandono del checkout ePayco', $this->service->motivoTexto($precompra));
    }

    public function test_motivo_texto_con_detalle(): void
    {
        $precompra = new PrecompraServicio;
        $precompra->motivo_desestimacion = 'YA_NO_INTERESA';
        $precompra->detalle_desestimacion = 'Cambio de planes';

        $this->assertSame(
            'Ya no estoy interesado en el servicio - Cambio de planes',
            $this->service->motivoTexto($precompra)
        );
    }
}
