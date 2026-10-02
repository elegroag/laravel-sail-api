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

    public function test_enmascara_documento_dejando_ultimos_cuatro(): void
    {
        $this->assertSame('***0111', CarnetDigitalService::enmascararDocumento('1.117.000.111'));
        $this->assertSame('***5678', CarnetDigitalService::enmascararDocumento('AB-12345678'));
    }

    public function test_enmascara_documento_corto_o_vacio(): void
    {
        $this->assertSame('***', CarnetDigitalService::enmascararDocumento('1234'));
        $this->assertSame('', CarnetDigitalService::enmascararDocumento(''));
    }
}
