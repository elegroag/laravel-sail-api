<?php

namespace Tests\Unit\Services\Reports;

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

        $this->assertSame('CC 5555555', $normalized['tipo_identificacion']);
        $this->assertSame('999', $normalized['cedtra_titular']);
        $this->assertSame('Maria Lopez', $normalized['nombre_titular']);
        $this->assertArrayNotHasKey('sat_fecapr', $normalized);
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

    public function test_dias_habiles_usa_fecha_actual_cuando_no_hay_fecapr(): void
    {
        $dias = DiasHabilesCalculator::between('2026-01-01', null);

        $this->assertIsInt($dias);
        $this->assertGreaterThanOrEqual(0, $dias);
    }

    public function test_filtro_sin_fecha_aprobacion_no_usa_cadena_vacia_en_sql(): void
    {
        $query = Mercurio32::query()->whereBetween('fecsol', ['2026-01-01', '2026-12-31']);
        $service = new OportunidadAfiliacionService;

        $method = new \ReflectionMethod($service, 'aplicarFiltroSinFechaAprobacion');
        $method->invoke($service, $query);

        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('`fecapr` is null', $sql);
        $this->assertStringContainsString('`fecapr` = ?', $sql);
        $this->assertStringNotContainsString("= ''", $sql);
        $this->assertContains('0000-00-00', $query->getBindings());
    }

    public function test_dias_habiles_desde_fecsol_hasta_fecapr_para_solicitud_trabajador(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 100,
                'nit' => '900123456',
                'razsoc' => 'Empresa Demo',
                'cedtra' => '1234567890',
                'tipdoc' => 'CC',
                'priape' => 'Perez',
                'prinom' => 'Juan',
                'fecsol' => '2026-06-01',
                'fecapr' => '2026-06-10',
                'fecest' => null,
                'estado' => 'A',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('2026-06-01', $registro['fecsol']);
        $this->assertSame('2026-06-10', $registro['fecapr']);
        $this->assertSame(7, $registro['dias_habiles'], 'lun 01 a mie 10 = 7 habiles');
        $this->assertSame('VENCIDO', $registro['estado_oportunidad'], 'mas de 3 habiles con fecapr = VENCIDO');

        Carbon::setTestNow();
    }

    public function test_dias_habiles_desde_fecsol_hasta_fecest_cuando_no_hay_fecapr(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio32::factory()->make([
                'id' => 200,
                'cedtra' => '999',
                'cedcon' => '888',
                'tipdoc' => 'CC',
                'priape' => 'Gomez',
                'prinom' => 'Ana',
                'fecsol' => '2026-06-15',
                'fecapr' => null,
                'fecest' => '2026-06-19',
                'estado' => 'A',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('2026-06-15', $registro['fecsol']);
        $this->assertSame('2026-06-19', $registro['fecest']);
        $this->assertNull($registro['fecapr']);
        $this->assertSame(4, $registro['dias_habiles'], 'lun 15 a vie 19 = 4 habiles (sin contar inicio)');
        $this->assertSame('VENCIDO', $registro['estado_oportunidad'], 'sin fecapr y mas de 3 habiles = VENCIDO');

        Carbon::setTestNow();
    }

    public function test_pendiente_no_toma_fecest_y_cuenta_hasta_hoy(): void
    {
        Carbon::setTestNow('2026-06-26 12:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 210,
                'nit' => '900111000',
                'razsoc' => 'Pendiente Historica',
                'cedtra' => '1500001',
                'tipdoc' => 'CC',
                'priape' => 'Diaz',
                'prinom' => 'Camilo',
                'fecsol' => '2026-06-15',
                'fecapr' => null,
                'fecest' => '2026-06-19',
                'estado' => 'P',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('Pendiente', $registro['estado']);
        $this->assertNull($registro['fecapr'], 'pendiente no debe mostrar fecapr');
        $this->assertNull($registro['fecest'], 'pendiente no debe mostrar fecest');
        $this->assertNull($registro['fecha_cierre'], 'pendiente no debe tener fecha_cierre');
        $this->assertSame(9, $registro['dias_habiles'], 'lun 15 a vie 26 = 9 habiles (sin contar inicio)');
        $this->assertSame('VENCIDO', $registro['estado_oportunidad']);
        $this->assertSame('2026-06-15', $registro['fecsol']);
    }

    public function test_devuelto_no_toma_fecest_y_cuenta_hasta_hoy(): void
    {
        Carbon::setTestNow('2026-06-23 10:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 220,
                'nit' => '900222000',
                'razsoc' => 'Devuelta',
                'cedtra' => '1600001',
                'tipdoc' => 'CC',
                'priape' => 'Reyes',
                'prinom' => 'Sara',
                'fecsol' => '2026-06-22',
                'fecapr' => null,
                'fecest' => '2026-06-30',
                'estado' => 'D',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('Devuelto', $registro['estado']);
        $this->assertNull($registro['fecapr']);
        $this->assertNull($registro['fecest']);
        $this->assertNull($registro['fecha_cierre']);
        $this->assertSame(1, $registro['dias_habiles'], 'lun 22 a mar 23 = 1 habil (sin contar inicio)');
    }

    public function test_temporal_no_toma_fecest_y_cuenta_hasta_hoy(): void
    {
        Carbon::setTestNow('2026-06-29 10:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio32::factory()->make([
                'id' => 230,
                'cedtra' => '1700001',
                'cedcon' => '1700002',
                'tipdoc' => 'CC',
                'priape' => 'Luna',
                'prinom' => 'Ana',
                'fecsol' => '2026-06-15',
                'fecapr' => null,
                'fecest' => '2026-06-19',
                'estado' => 'T',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('TEMPORAL', $registro['estado']);
        $this->assertNull($registro['fecapr']);
        $this->assertNull($registro['fecest']);
        $this->assertNull($registro['fecha_cierre']);
        $this->assertSame(10, $registro['dias_habiles'], 'lun 15 a lun 29 = 10 habiles');
        $this->assertSame('VENCIDO', $registro['estado_oportunidad']);
    }

    public function test_dias_habiles_usan_hoy_cuando_no_hay_fecapr_ni_fecest(): void
    {
        Carbon::setTestNow('2026-06-26 12:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 300,
                'nit' => '900999999',
                'razsoc' => 'Pendiente SA',
                'cedtra' => '555000111',
                'tipdoc' => 'CC',
                'priape' => 'Lopez',
                'prinom' => 'Maria',
                'fecsol' => '2026-06-22',
                'fecapr' => null,
                'fecest' => null,
                'estado' => 'P',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $this->assertSame(4, $dataset[0]['dias_habiles'], 'lun 22 a vie 26 = 4 habiles (lun-mar-mie-jue-vie, menos 1)');
        $this->assertSame('VENCIDO', $dataset[0]['estado_oportunidad'], 'sin fecapr y mas de 3 habiles = VENCIDO');

        Carbon::setTestNow();
    }

    public function test_dias_habiles_no_incluyen_fines_de_semana(): void
    {
        $dataset = $this->ejecutarServicioConModelos([
            Mercurio30::factory()->make([
                'id' => 400,
                'nit' => '800111222',
                'razsoc' => 'Empresa Beta',
                'cedtra' => null,
                'fecsol' => '2026-06-01',
                'fecapr' => '2026-06-12',
                'fecest' => null,
                'estado' => 'A',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $this->assertSame(9, $dataset[0]['dias_habiles'], 'lun 01 a vie 12 = 9 habiles (saltando 2 fines de semana)');
        $this->assertSame('VENCIDO', $dataset[0]['estado_oportunidad'], '9 habiles > 3 = VENCIDO');
    }

    public function test_aprobada_sin_fecapr_cae_a_fecest_en_columna_y_calculo(): void
    {
        Carbon::setTestNow('2026-06-29');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 500,
                'nit' => '900555000',
                'razsoc' => 'Aprobada Legacy',
                'cedtra' => '7000001',
                'tipdoc' => 'CC',
                'priape' => 'Rojas',
                'prinom' => 'Pedro',
                'fecsol' => '2026-06-01',
                'fecapr' => null,
                'fecest' => '2026-06-04',
                'estado' => 'A',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('Aprobado', $registro['estado']);
        $this->assertNull($registro['fecapr'], 'fecapr queda null cuando no existe aprobacion real');
        $this->assertSame('2026-06-04', $registro['fecest']);
        $this->assertSame('2026-06-04', $registro['fecha_cierre'], 'fecha_cierre cae a fecest cuando fecapr es null');
        $this->assertSame(3, $registro['dias_habiles'], 'lun 01 a jue 04 = 3 habiles');
        $this->assertSame('EN_TRAMITE', $registro['estado_oportunidad'], 'estado se evalua con fecapr real (null), no con fecha_cierre');
    }

    public function test_aprobada_sin_fecapr_ni_fecest_usa_hoy(): void
    {
        Carbon::setTestNow('2026-06-25 12:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 600,
                'nit' => '900666000',
                'razsoc' => 'Sin Fechas',
                'cedtra' => '8000001',
                'tipdoc' => 'CC',
                'priape' => 'Mora',
                'prinom' => 'Luis',
                'fecsol' => '2026-06-22',
                'fecapr' => null,
                'fecest' => null,
                'estado' => 'A',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $registro = $dataset[0];

        $this->assertSame('Aprobado', $registro['estado']);
        $this->assertNull($registro['fecapr']);
        $this->assertNull($registro['fecest']);
        $this->assertNull($registro['fecha_cierre'], 'sin fecapr ni fecest, fecha_cierre es null');
        $this->assertSame(3, $registro['dias_habiles'], 'lun 22 a jue 25 = 3 habiles (mar, mie, jue)');
    }

    public function test_aprobada_con_fecapr_prevalece_sobre_fecest(): void
    {
        Carbon::setTestNow('2026-06-29');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 700,
                'nit' => '900777000',
                'razsoc' => 'Con Fecapr',
                'cedtra' => '9000001',
                'tipdoc' => 'CC',
                'fecsol' => '2026-06-01',
                'fecapr' => '2026-06-02',
                'fecest' => '2026-06-30',
                'estado' => 'A',
            ]),
        ]);

        $this->assertCount(1, $dataset);
        $this->assertSame('2026-06-02', $dataset[0]['fecapr']);
        $this->assertSame('2026-06-02', $dataset[0]['fecha_cierre'], 'fecapr prevalece sobre fecest');
    }

    #[DataProvider('casosDiasHabilesProvider')]
    public function test_calculo_dias_habiles_entre_fecsol_y_fecapr(string $fecsol, string $fecapr, int $esperado): void
    {
        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make([
                'id' => 500 + abs(crc32($fecsol.$fecapr)) % 1000,
                'nit' => '900000001',
                'razsoc' => 'Calculo SA',
                'cedtra' => '1010101010',
                'tipdoc' => 'CC',
                'fecsol' => $fecsol,
                'fecapr' => $fecapr,
                'fecest' => null,
                'estado' => 'A',
            ]),
        ]);

        $this->assertSame($esperado, $dataset[0]['dias_habiles'], "{$fecsol} -> {$fecapr}");
    }

    public static function casosDiasHabilesProvider(): array
    {
        // 2026-06-01 = lunes, 2026-06-06 = sabado, 2026-06-07 = domingo
        return [
            'mismo dia' => ['2026-06-01', '2026-06-01', 0],
            'lunes a viernes misma semana' => ['2026-06-01', '2026-06-05', 4],
            'cruza fin de semana' => ['2026-06-05', '2026-06-08', 1],
            'dos semanas' => ['2026-06-01', '2026-06-12', 9],
        ];
    }

    public function test_resumen_cuenta_estados_segun_dias_habiles(): void
    {
        // Con umbral de 3:
        //   - id=1 fecsol=2026-06-01 lun -> fecapr=2026-06-02 mar = 0 habiles = EN_TERMINO
        //   - id=2 fecsol=2026-06-01 lun -> fecapr=2026-06-15 lun = 10 habiles = VENCIDO
        //   - id=3 fecsol=2026-06-22 lun -> sin fecapr ni fecest, hoy=2026-06-23 mar = 0 habiles = EN_TRAMITE
        Carbon::setTestNow('2026-06-23 12:00:00');

        $dataset = $this->ejecutarServicioConModelos([
            Mercurio31::factory()->make(['id' => 1, 'nit' => '900000001', 'razsoc' => 'Aprobada', 'cedtra' => '1', 'fecsol' => '2026-06-01', 'fecapr' => '2026-06-02', 'fecest' => null, 'estado' => 'A']),
            Mercurio31::factory()->make(['id' => 2, 'nit' => '900000002', 'razsoc' => 'Vencida', 'cedtra' => '2', 'fecsol' => '2026-06-01', 'fecapr' => '2026-06-15', 'fecest' => null, 'estado' => 'A']),
            Mercurio31::factory()->make(['id' => 3, 'nit' => '900000003', 'razsoc' => 'Tramite', 'cedtra' => '3', 'fecsol' => '2026-06-22', 'fecapr' => null, 'fecest' => null, 'estado' => 'P']),
        ]);

        $this->assertCount(3, $dataset);

        $estados = array_count_values(array_column($dataset, 'estado_oportunidad'));
        $this->assertSame(1, $estados['EN_TERMINO'] ?? 0, '0 habiles con fecapr = EN_TERMINO');
        $this->assertSame(1, $estados['VENCIDO'] ?? 0, 'mas de 3 habiles = VENCIDO');
        $this->assertSame(1, $estados['EN_TRAMITE'] ?? 0, 'sin aprobar y dentro del umbral = EN_TRAMITE');

        Carbon::setTestNow();
    }

    /**
     * Ejecuta el servicio contra un dataset en memoria inyectado,
     * evitando la conexion a la base de datos.
     *
     * @param  array<int, object>  $modelos
     * @return array<int, array<string, mixed>>
     */
    private function ejecutarServicioConModelos(array $modelos): array
    {
        $serviceMock = new class($modelos) extends OportunidadAfiliacionService
        {
            /** @var array<int, object> */
            private array $modelosInyectados;

            public function __construct(array $modelos)
            {
                $this->modelosInyectados = $modelos;
            }

            public function buildDataset(array $filtros = []): array
            {
                $reflection = new ReflectionClass(OportunidadAfiliacionService::class);
                $resolver = $reflection->getMethod('resolverEstadoOportunidad');
                $resolver->setAccessible(true);

                $umbral = (int) config('reportes.oportunidad_umbral_dias', 3);

                $dataset = [];
                foreach ($this->modelosInyectados as $model) {
                    $tipopc = $this->detectarTipopc($model);
                    $config = config("reportes.oportunidad_tipos.{$tipopc}");
                    $record = AfiliacionNormalizer::normalize($model, $tipopc, $config, []);

                    $fin = $record['fecha_cierre'];
                    $record['dias_habiles'] = DiasHabilesCalculator::between($record['fecsol'], $fin);
                    $record['estado_oportunidad'] = $resolver->invoke($this, $record['fecapr'], $record['dias_habiles'], $umbral);
                    $dataset[] = $record;
                }

                return $dataset;
            }

            private function detectarTipopc(object $model): int
            {
                $map = [
                    Mercurio31::class => 1,
                    Mercurio30::class => 2,
                    Mercurio32::class => 3,
                ];

                foreach ($map as $class => $tipopc) {
                    if ($model instanceof $class) {
                        return $tipopc;
                    }
                }

                return 1;
            }
        };

        return $serviceMock->buildDataset([]);
    }
}
