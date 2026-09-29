<?php

namespace Tests\Unit\Services;

use App\Services\Carnet\CarnetDigitalService;
use PHPUnit\Framework\TestCase;

class CarnetDigitalServiceTest extends TestCase
{
    public function test_enmascara_nombre_conservando_primer_nombre(): void
    {
        $this->assertSame('JUAN C*** P*** G***', CarnetDigitalService::enmascararNombre('Juan Carlos Pérez Gómez'));
    }

    public function test_enmascara_nombre_con_tildes_y_espacios_extra(): void
    {
        $this->assertSame('ÁNGELA Ñ***', CarnetDigitalService::enmascararNombre('  ángela   ñuñez '));
    }

    public function test_enmascara_nombre_vacio(): void
    {
        $this->assertSame('', CarnetDigitalService::enmascararNombre('   '));
    }
}
