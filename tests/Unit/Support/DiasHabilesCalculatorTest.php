<?php

namespace Tests\Unit\Support;

use App\Support\DiasHabilesCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DiasHabilesCalculatorTest extends TestCase
{
    public function test_devuelve_null_si_fecha_inicial_vacia(): void
    {
        $this->assertNull(DiasHabilesCalculator::between(null));
        $this->assertNull(DiasHabilesCalculator::between(''));
    }

    #[DataProvider('fechasProvider')]
    public function test_calcula_dias_habiles_entre_fechas(?string $ini, ?string $fin, ?int $esperado): void
    {
        $this->assertSame($esperado, DiasHabilesCalculator::between($ini, $fin));
    }

    public static function fechasProvider(): array
    {
        return [
            'mismo dia habil' => ['2026-06-01', '2026-06-01', 0],
            'lunes a viernes' => ['2026-06-01', '2026-06-05', 4],
            'fecha fin anterior a inicio' => ['2026-06-05', '2026-06-01', 0],
            'incluye fin de semana' => ['2026-06-05', '2026-06-08', 1],
        ];
    }
}
