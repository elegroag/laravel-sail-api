<?php

namespace Tests\Unit\Services\Reports;

use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Services\Reports\OportunidadAfiliacionService;
use App\Support\AfiliacionNormalizer;
use App\Support\DiasHabilesCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Tests\TestCase;

class OportunidadAfiliacionServiceTest extends TestCase
{
    public function test_afiliacion_normalizer_incluye_titular_desde_indice(): void
    {
        $model = Mercurio32::factory()->make([
            'tipdoc' => 'CC',
            'cedcon' => '5555555',
            'cedtra' => '999',
            'priape' => 'Gomez',
            'prinom' => 'Ana',
        ]);

        $normalized = AfiliacionNormalizer::normalize(
            $model,
            3,
            config('reportes.oportunidad_tipos')[3],
            ['999' => 'Maria Lopez']
        );

        $this->assertSame('CC', $normalized['tipdoc']);
        $this->assertSame('5555555', $normalized['documento']);
        $this->assertSame('CC 5555555', $normalized['tipo_identificacion']);
        $this->assertSame('999', $normalized['cedtra_titular']);
        $this->assertSame('Maria Lopez', $normalized['nombre_titular']);
        $this->assertArrayNotHasKey('sat_fecapr', $normalized);
    }

    public function test_nombre_empresa_no_duplica_repleg_ni_razsoc(): void
    {
        $model = Mercurio30::factory()->make([
            'nit' => '900123456',
            'razsoc' => 'MINI-MARKET JUANCHITO',
            'repleg' => 'GIOVANNI BOLAÑOS ARTUNDUAGA',
            'priape' => 'BOLAÑOS ARTUNDUAGA',
            'segape' => null,
            'prinom' => 'GIOVANNI',
            'segnom' => null,
        ]);

        $normalized = AfiliacionNormalizer::normalize(
            $model,
            2,
            config('reportes.oportunidad_tipos')[2],
            []
        );

        $this->assertSame('BOLAÑOS ARTUNDUAGA GIOVANNI', $normalized['nombre']);
        $this->assertSame('MINI-MARKET JUANCHITO', $normalized['razsoc']);
        $this->assertStringNotContainsString('MINI-MARKET', $normalized['nombre']);
        $this->assertSame(1, substr_count($normalized['nombre'], 'GIOVANNI'));
        $this->assertSame(1, substr_count($normalized['nombre'], 'BOLAÑOS'));
    }

    public function test_nombre_empresa_usa_repleg_si_no_hay_nombres(): void
    {
        $model = Mercurio30::factory()->make([
            'nit' => '900123456',
            'razsoc' => 'Empresa Demo',
            'repleg' => 'Ana Perez',
            'priape' => null,
            'segape' => null,
            'prinom' => null,
            'segnom' => null,
        ]);

        $normalized = AfiliacionNormalizer::normalize(
            $model,
            2,
            config('reportes.oportunidad_tipos')[2],
            []
        );

        $this->assertSame('Ana Perez', $normalized['nombre']);
        $this->assertSame('Empresa Demo', $normalized['razsoc']);
    }

    public function test_service_expone_metodos_de_dataset_y_resumen(): void
    {
        $service = new OportunidadAfiliacionService;

        $this->assertTrue(method_exists($service, 'buildDataset'));
        $this->assertTrue(method_exists($service, 'buildResumen'));
    }

    public function test_config_includes_required_affiliation_types_and_umbral(): void
    {
        $tipos = config('reportes.oportunidad_tipos');

        $this->assertArrayHasKey(1, $tipos);
        $this->assertArrayHasKey(2, $tipos);
        $this->assertArrayHasKey(3, $tipos);
        $this->assertArrayHasKey(4, $tipos);
        $this->assertArrayHasKey(9, $tipos);
        $this->assertArrayHasKey(10, $tipos);
        $this->assertArrayHasKey(11, $tipos);
        $this->assertArrayNotHasKey('has_sat_fecapr', $tipos[1]);
        $this->assertSame(3, config('reportes.oportunidad_umbral_dias'));
    }

    public function test_dias_habiles_usa_fecha_actual_cuando_no_hay_cierre(): void
    {
        $dias = DiasHabilesCalculator::between('2026-01-01', null);

        $this->assertIsInt($dias);
        $this->assertGreaterThanOrEqual(0, $dias);
    }

    public function test_evento_p_cerrado_por_a_fuera_del_umbral_es_vencido(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $solicitud = Mercurio31::factory()->make([
            'id' => 100,
            'nit' => '900123456',
            'razsoc' => 'Empresa Demo',
            'cedtra' => '1234567890',
            'tipdoc' => 'CC',
            'priape' => 'Perez',
            'prinom' => 'Juan',
            'fecsol' => '2026-05-01',
            'fecapr' => '2026-06-10',
            'estado' => 'A',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 100, 1, 'P', '2026-06-01', 'TRA-2026-00100-01'),
                'evento_cierre' => $this->evento(1, 100, 2, 'A', '2026-06-10'),
            ],
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('TRA-2026-00100-01', $registro['ruuid']);
        $this->assertSame('2026-06-01', $registro['fecsol']);
        $this->assertSame('2026-06-10', $registro['fecapr']);
        $this->assertSame(7, $registro['dias_habiles']);
        $this->assertSame('VENCIDO', $registro['estado_oportunidad']);

        Carbon::setTestNow();
    }

    public function test_evento_p_cerrado_dentro_del_umbral_es_en_termino(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $solicitud = Mercurio31::factory()->make([
            'id' => 101,
            'nit' => '900123456',
            'razsoc' => 'Empresa Demo',
            'cedtra' => '111',
            'tipdoc' => 'CC',
            'fecsol' => '2026-06-01',
            'estado' => 'A',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 101, 1, 'P', '2026-06-01', 'TRA-2026-00101-01'),
                'evento_cierre' => $this->evento(1, 101, 2, 'A', '2026-06-02'),
            ],
        ]);

        $this->assertCount(1, $dataset);
        $this->assertSame(1, $dataset[0]['dias_habiles']);
        $this->assertSame('EN_TERMINO', $dataset[0]['estado_oportunidad']);

        Carbon::setTestNow();
    }

    public function test_evento_p_sin_cierre_cuenta_hasta_hoy(): void
    {
        Carbon::setTestNow('2026-06-26 12:00:00');

        $solicitud = Mercurio31::factory()->make([
            'id' => 210,
            'nit' => '900111000',
            'razsoc' => 'Pendiente Historica',
            'cedtra' => '1500001',
            'tipdoc' => 'CC',
            'priape' => 'Diaz',
            'prinom' => 'Camilo',
            'fecsol' => '2026-06-01',
            'fecapr' => null,
            'estado' => 'P',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 210, 1, 'P', '2026-06-15', 'TRA-2026-00210-01'),
                'evento_cierre' => null,
            ],
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertNull($registro['fecapr']);
        $this->assertNull($registro['fecha_cierre']);
        $this->assertSame(9, $registro['dias_habiles']);
        $this->assertSame('VENCIDO', $registro['estado_oportunidad']);
        $this->assertSame('2026-06-15', $registro['fecsol']);

        Carbon::setTestNow();
    }

    public function test_varios_eventos_p_de_misma_solicitud_generan_varias_filas(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $solicitud = Mercurio31::factory()->make([
            'id' => 300,
            'nit' => '900999999',
            'razsoc' => 'Reenviada SA',
            'cedtra' => '555000111',
            'tipdoc' => 'CC',
            'estado' => 'A',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 300, 1, 'P', '2026-06-01', 'TRA-2026-00300-01'),
                'evento_cierre' => $this->evento(1, 300, 2, 'A', '2026-06-02'),
            ],
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 300, 3, 'P', '2026-06-10', 'TRA-2026-00300-03'),
                'evento_cierre' => $this->evento(1, 300, 4, 'A', '2026-06-15'),
            ],
        ]);

        $this->assertCount(2, $dataset);
        $this->assertSame('TRA-2026-00300-01', $dataset[0]['ruuid']);
        $this->assertSame(1, $dataset[0]['item']);
        $this->assertSame('TRA-2026-00300-03', $dataset[1]['ruuid']);
        $this->assertSame(3, $dataset[1]['item']);

        Carbon::setTestNow();
    }

    public function test_solicitud_inactiva_se_excluye(): void
    {
        $solicitud = Mercurio31::factory()->make([
            'id' => 400,
            'nit' => '900000400',
            'cedtra' => '400',
            'estado' => 'I',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 400, 1, 'P', '2026-06-01', 'TRA-2026-00400-01'),
                'evento_cierre' => null,
            ],
        ]);

        $this->assertCount(0, $dataset);
    }

    public function test_cierre_por_rechazo_x_no_muestra_fecha_aprobacion(): void
    {
        Carbon::setTestNow('2026-06-29');

        $solicitud = Mercurio30::factory()->make([
            'id' => 50,
            'nit' => '800111222',
            'razsoc' => 'Empresa Beta',
            'ruuid' => 'EMP-2026-00050',
            'estado' => 'X',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 2,
                'evento_p' => $this->evento(2, 50, 1, 'P', '2026-06-01', 'EMP-2026-00050-01'),
                'evento_cierre' => $this->evento(2, 50, 2, 'X', '2026-06-12'),
            ],
        ]);

        $this->assertCount(1, $dataset);
        $this->assertSame('2026-06-12', $dataset[0]['fecha_cierre']);
        $this->assertNull($dataset[0]['fecapr'], 'rechazo X no debe llenar fecha de aprobacion');
        $this->assertSame(9, $dataset[0]['dias_habiles']);
        $this->assertSame('VENCIDO', $dataset[0]['estado_oportunidad']);

        Carbon::setTestNow();
    }

    public function test_ruuid_usa_solo_mercurio10_sin_fallback_a_solicitud(): void
    {
        $solicitud = Mercurio31::factory()->make([
            'id' => 88,
            'nit' => '900000088',
            'cedtra' => '880088',
            'ruuid' => 'TRA-2026-00088',
            'estado' => 'P',
        ]);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 88, 1, 'P', '2026-06-01', null),
                'evento_cierre' => null,
            ],
        ]);

        $this->assertCount(1, $dataset);
        $this->assertSame('', $dataset[0]['ruuid']);
        $this->assertNotSame('TRA-2026-00088', $dataset[0]['ruuid']);
    }

    #[DataProvider('casosDiasHabilesProvider')]
    public function test_calculo_dias_habiles_entre_evento_p_y_cierre(string $fecsisP, string $fecsisCierre, int $esperado): void
    {
        $solicitud = Mercurio31::factory()->make([
            'id' => 500 + abs(crc32($fecsisP.$fecsisCierre)) % 1000,
            'nit' => '900000001',
            'razsoc' => 'Calculo SA',
            'cedtra' => '1010101010',
            'tipdoc' => 'CC',
            'estado' => 'A',
        ]);

        $id = (int) $solicitud->id;

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $solicitud,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, $id, 1, 'P', $fecsisP, "TRA-2026-{$id}-01"),
                'evento_cierre' => $this->evento(1, $id, 2, 'A', $fecsisCierre),
            ],
        ]);

        $this->assertSame($esperado, $dataset[0]['dias_habiles'], "{$fecsisP} -> {$fecsisCierre}");
    }

    public static function casosDiasHabilesProvider(): array
    {
        return [
            'mismo dia' => ['2026-06-01', '2026-06-01', 0],
            'lunes a viernes misma semana' => ['2026-06-01', '2026-06-05', 4],
            'cruza fin de semana' => ['2026-06-05', '2026-06-08', 1],
            'dos semanas' => ['2026-06-01', '2026-06-12', 9],
        ];
    }

    public function test_resumen_cuenta_estados_segun_dias_habiles_de_eventos(): void
    {
        Carbon::setTestNow('2026-06-23 12:00:00');

        $s1 = Mercurio31::factory()->make(['id' => 1, 'nit' => '900000001', 'cedtra' => '1', 'estado' => 'A']);
        $s2 = Mercurio31::factory()->make(['id' => 2, 'nit' => '900000002', 'cedtra' => '2', 'estado' => 'A']);
        $s3 = Mercurio31::factory()->make(['id' => 3, 'nit' => '900000003', 'cedtra' => '3', 'estado' => 'P']);

        $dataset = $this->ejecutarServicioConEventos([
            [
                'solicitud' => $s1,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 1, 1, 'P', '2026-06-01', 'TRA-2026-00001-01'),
                'evento_cierre' => $this->evento(1, 1, 2, 'A', '2026-06-02'),
            ],
            [
                'solicitud' => $s2,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 2, 1, 'P', '2026-06-01', 'TRA-2026-00002-01'),
                'evento_cierre' => $this->evento(1, 2, 2, 'A', '2026-06-15'),
            ],
            [
                'solicitud' => $s3,
                'tipopc' => 1,
                'evento_p' => $this->evento(1, 3, 1, 'P', '2026-06-22', 'TRA-2026-00003-01'),
                'evento_cierre' => null,
            ],
        ]);

        $this->assertCount(3, $dataset);

        $estados = array_count_values(array_column($dataset, 'estado_oportunidad'));
        $this->assertSame(1, $estados['EN_TERMINO'] ?? 0);
        $this->assertSame(1, $estados['VENCIDO'] ?? 0);
        $this->assertSame(1, $estados['EN_TRAMITE'] ?? 0);

        Carbon::setTestNow();
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function evento(int $tipopc, int $numero, int $item, string $estado, string $fecsis, ?string $ruuid = null): Mercurio10
    {
        $evento = new Mercurio10;
        $evento->forceFill([
            'tipopc' => (string) $tipopc,
            'numero' => $numero,
            'item' => $item,
            'estado' => $estado,
            'fecsis' => $fecsis,
            'nota' => 'test',
            'ruuid' => $ruuid,
        ]);

        return $evento;
    }

    /**
     * Simula buildDataset por eventos Mercurio10 sin tocar la BD.
     *
     * @param  array<int, array{solicitud: object, tipopc: int, evento_p: Mercurio10, evento_cierre: ?Mercurio10}>  $casos
     * @return array<int, array<string, mixed>>
     */
    private function ejecutarServicioConEventos(array $casos): array
    {
        $serviceMock = new class($casos) extends OportunidadAfiliacionService
        {
            /** @var array<int, array{solicitud: object, tipopc: int, evento_p: Mercurio10, evento_cierre: ?Mercurio10}> */
            private array $casos;

            public function __construct(array $casos)
            {
                $this->casos = $casos;
            }

            public function buildDataset(array $filtros = []): array
            {
                $reflection = new ReflectionClass(OportunidadAfiliacionService::class);
                $resolver = $reflection->getMethod('resolverEstadoOportunidad');
                $resolver->setAccessible(true);

                $umbral = (int) config('reportes.oportunidad_umbral_dias', 3);
                $dataset = [];

                foreach ($this->casos as $caso) {
                    $solicitud = $caso['solicitud'];
                    $estadoSolicitud = strtoupper(trim((string) ($solicitud->estado ?? '')));
                    if ($estadoSolicitud === 'I') {
                        continue;
                    }

                    $tipopc = (int) $caso['tipopc'];
                    $config = config("reportes.oportunidad_tipos.{$tipopc}");
                    $eventoP = $caso['evento_p'];
                    $cierre = $caso['evento_cierre'];

                    $record = AfiliacionNormalizer::normalize($solicitud, $tipopc, $config, []);

                    $tipdocCode = trim((string) ($record['tipdoc'] ?? ''));
                    $numero = trim((string) ($record['documento'] ?? ''));
                    $tipo = match ($tipdocCode) {
                        '1', 'CC' => 'CC',
                        '2', 'TI' => 'TI',
                        '3', 'NI', 'NIT' => 'NI',
                        default => $tipdocCode,
                    };
                    $record['tipo_documento'] = $tipo;
                    $record['numero_identificacion'] = $numero;
                    $record['tipo_identificacion'] = trim($tipo.' '.$numero);

                    $fechaInicio = Carbon::parse($eventoP->fecsis)->format('Y-m-d');
                    $fechaCierre = $cierre
                        ? Carbon::parse($cierre->fecsis)->format('Y-m-d')
                        : null;
                    $fechaAprobacion = ($cierre && strtoupper((string) $cierre->estado) === 'A')
                        ? $fechaCierre
                        : null;

                    $record['ruuid'] = $eventoP->ruuid ?: '';
                    $record['item'] = $eventoP->item;
                    $record['fecsol'] = $fechaInicio;
                    $record['fecapr'] = $fechaAprobacion;
                    $record['fecha_cierre'] = $fechaCierre;
                    $record['dias_habiles'] = DiasHabilesCalculator::between($fechaInicio, $fechaCierre);
                    $record['estado_oportunidad'] = $resolver->invoke(
                        $this,
                        $fechaCierre,
                        $record['dias_habiles'],
                        $umbral
                    );

                    $dataset[] = $record;
                }

                return $dataset;
            }
        };

        return $serviceMock->buildDataset([]);
    }
}
