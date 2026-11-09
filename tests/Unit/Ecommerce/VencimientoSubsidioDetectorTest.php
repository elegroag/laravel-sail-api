<?php

namespace Tests\Unit\Ecommerce;

use App\Services\Ecommerce\EstadoPrecompra;
use App\Services\Ecommerce\VencimientoSubsidioDetector;
use PHPUnit\Framework\TestCase;

class VencimientoSubsidioDetectorTest extends TestCase
{
    private VencimientoSubsidioDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new VencimientoSubsidioDetector;
    }

    public function test_detecta_sin_cupos(): void
    {
        $r = $this->detector->clasificar('No hay cupos disponibles para el beneficiario');

        $this->assertTrue($r['vencida']);
        $this->assertSame(EstadoPrecompra::MOTIVO_VENCIDA_SIN_CUPOS, $r['motivo']);
    }

    public function test_detecta_fuera_de_plazo(): void
    {
        $r = $this->detector->clasificar('La inscripción está fuera de plazo para este servicio');

        $this->assertTrue($r['vencida']);
        $this->assertSame(EstadoPrecompra::MOTIVO_VENCIDA_PLAZO, $r['motivo']);
    }

    public function test_no_marca_errores_genericos(): void
    {
        $r = $this->detector->clasificar('Error de conexión con la base de datos');

        $this->assertFalse($r['vencida']);
        $this->assertNull($r['motivo']);
    }

    public function test_mensaje_vacio_no_vencida(): void
    {
        $this->assertFalse($this->detector->esVencimiento(null));
        $this->assertFalse($this->detector->esVencimiento(''));
    }
}
