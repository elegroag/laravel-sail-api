<?php

namespace Tests\Unit\Services\Utils;

use App\Exceptions\DebugException;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\FacultativoService;
use App\Services\Entidades\IndependienteService;
use App\Services\Entidades\MadresComuniService;
use App\Services\Entidades\ServicioDomesticoService;
use App\Services\Utils\GeneralService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GeneralServiceConsultaTipopcRoutingTest extends TestCase
{
    #[DataProvider('tipopcServiceProvider')]
    public function test_entity_service_class_usa_fuente_correcta(string $tipopc, string $expectedClass): void
    {
        $this->assertSame(
            $expectedClass,
            (new GeneralService)->entityServiceClass($tipopc)
        );
    }

    public static function tipopcServiceProvider(): array
    {
        return [
            'empresa' => ['2', EmpresaService::class],
            'facultativo' => ['10', FacultativoService::class],
            'madres_comunitarias' => ['11', MadresComuniService::class],
            'servicio_domestico' => ['12', ServicioDomesticoService::class],
            'independiente' => ['13', IndependienteService::class],
        ];
    }

    public function test_tipopc_invalido_lanza_excepcion(): void
    {
        $this->expectException(DebugException::class);

        (new GeneralService)->entityServiceClass('99');
    }
}
