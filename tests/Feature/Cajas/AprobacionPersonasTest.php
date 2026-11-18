<?php

namespace Tests\Feature\Cajas;

use App\Exceptions\DebugException;
use App\Http\Middleware\CajasAuthenticated;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio45;
use App\Models\Mercurio47;
use App\Services\Cajas\ActualizacionTrabajadorService;
use App\Services\Cajas\AprobacionBeneficiarioService;
use App\Services\Cajas\AprobacionCertificadoService;
use App\Services\Cajas\AprobacionConyugeService;
use App\Services\Cajas\AprobacionTrabajadorService;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Aprobación de trabajadores, cónyuges, beneficiarios, certificados y actualización de datos de trabajador.
 * Usa SQLite en memoria para no tocar la base de datos configurada en phpunit.xml.
 */
class AprobacionPersonasTest extends TestCase
{
    private const RUTAS_OPCIONALES = ['edit', 'update', 'aportes', 'deshacer_form', 'deshacer', 'reaprobar', 'editar_formulario', 'editar_solicitud', 'validar_multiafiliacion', 'trayectoria'];

    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        config([
            'logging.default' => 'stderr',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        $comunes = function (Blueprint $table) {
            $table->increments('id');
            $table->string('usuario')->nullable();
            $table->string('estado', 1)->nullable();
            $table->date('fecsol')->nullable();
            $table->string('motivo')->nullable();
            $table->string('codest', 2)->nullable();
            $table->date('fecest')->nullable();
        };
        $nombres = function (Blueprint $table) {
            foreach (['prinom', 'segnom', 'priape', 'segape'] as $columna) {
                $table->string($columna)->nullable();
            }
        };

        Schema::create('mercurio31', function (Blueprint $table) use ($comunes, $nombres) {
            $comunes($table);
            $nombres($table);
            $table->string('cedtra')->nullable();
            $table->string('nit')->nullable();
            $table->string('codsuc')->nullable();
        });
        Schema::create('mercurio32', function (Blueprint $table) use ($comunes, $nombres) {
            $comunes($table);
            $nombres($table);
            $table->string('cedtra')->nullable();
            $table->string('cedcon')->nullable();
        });
        Schema::create('mercurio34', function (Blueprint $table) use ($comunes, $nombres) {
            $comunes($table);
            $nombres($table);
            $table->string('cedtra')->nullable();
            $table->string('numdoc')->nullable();
        });
        Schema::create('mercurio45', function (Blueprint $table) use ($comunes) {
            $comunes($table);
            $table->string('cedtra')->nullable();
            $table->string('codben')->nullable();
            $table->string('nombre')->nullable();
            $table->string('nomcer')->nullable();
        });
        Schema::create('mercurio47', function (Blueprint $table) use ($comunes) {
            $comunes($table);
            $table->string('documento')->nullable();
            $table->string('tipo', 2)->nullable();
            $table->string('coddoc', 2)->nullable();
            $table->string('tipact', 1)->nullable();
        });
        Schema::create('mercurio07', function (Blueprint $table) {
            $table->string('tipo', 2);
            $table->string('coddoc', 2);
            $table->string('documento');
            $table->string('nombre')->nullable();
        });
        Schema::create('mercurio28', function (Blueprint $table) {
            $table->string('tipo', 2);
            $table->string('campo');
            $table->string('detalle')->nullable();
        });
        Schema::create('mercurio33', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('actualizacion');
            $table->string('campo');
            $table->string('antval')->nullable();
            $table->string('valor')->nullable();
        });
        Schema::create('gener42', function (Blueprint $table) {
            $table->increments('id');
            $table->string('usuario');
            $table->string('permiso');
        });
        Schema::create('mercurio10', function (Blueprint $table) {
            $table->string('tipopc', 2);
            $table->integer('numero');
            $table->integer('item');
            $table->string('estado', 1)->nullable();
            $table->string('nota', 800);
            $table->date('fecsis')->nullable();
            $table->string('codest', 2)->nullable();
            $table->string('campos_corregir', 200)->nullable();
            $table->string('ruuid', 20)->nullable();
            $table->string('cerrada', 1)->nullable();
            $table->date('feccie')->nullable();
        });
        Schema::create('mercurio11', function (Blueprint $table) {
            $table->string('codest', 2)->primary();
            $table->string('detalle');
        });
        Schema::create('mercurio12', function (Blueprint $table) {
            $table->integer('coddoc')->primary();
            $table->string('detalle');
        });
        Schema::create('mercurio37', function (Blueprint $table) {
            $table->string('tipopc', 2);
            $table->integer('numero');
            $table->integer('coddoc');
            $table->string('archivo')->nullable();
            $table->string('fhash')->nullable();
        });

        $this->withoutMiddleware(CajasAuthenticated::class);
        $this->withSession(['user' => ['usuario' => 'u1']]);
    }

    /**
     * @return array<string, array{0: string, 1: class-string<SolicitudAprobacionService>, 2: string, 3: string, 4: list<string>, 5: string, 6: array<string, string>, 7: array<string, string>}>
     *                                                                                                                                                                                            path, servicio, tipopc, tabla, rutas opcionales, búsqueda, atributos propios y ajenos
     */
    public static function modulos(): array
    {
        $todas = ['edit', 'update', 'deshacer_form', 'deshacer', 'reaprobar', 'editar_formulario', 'editar_solicitud'];

        return [
            'trabajadores' => [
                'aprobaciontra', AprobacionTrabajadorService::class, '1', 'mercurio31',
                [...$todas, 'aportes', 'validar_multiafiliacion', 'trayectoria'], '900111',
                ['nit' => '900111', 'prinom' => 'ALFA'], ['nit' => '900222', 'prinom' => 'BETA', 'cedtra' => '222'],
            ],
            'conyuges' => [
                'aprobacioncon', AprobacionConyugeService::class, '3', 'mercurio32',
                $todas, '555',
                ['cedcon' => '555'], ['cedcon' => '666', 'cedtra' => '222'],
            ],
            'beneficiarios' => [
                'aprobacionben', AprobacionBeneficiarioService::class, '4', 'mercurio34',
                $todas, '777',
                ['numdoc' => '777'], ['numdoc' => '888', 'cedtra' => '222'],
            ],
            'certificados' => [
                'aprobacioncer', AprobacionCertificadoService::class, '8', 'mercurio45',
                [], 'ESCOLAR',
                ['nomcer' => 'CERT ESCOLAR', 'nombre' => 'ALFA HIJO'], ['nomcer' => 'CERT MEDICO', 'nombre' => 'BETA', 'cedtra' => '222'],
            ],
            'actualizacion trabajador' => [
                'actualizatra', ActualizacionTrabajadorService::class, '14', 'mercurio47',
                [], '100200',
                ['documento' => '100200'], ['documento' => '100300'],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function fila(string $table, array $attrs = []): array
    {
        $base = ['usuario' => 'u1', 'estado' => 'P', 'fecsol' => '2026-01-10'];

        $propios = match ($table) {
            'mercurio47' => ['documento' => '100200', 'tipo' => 'T', 'coddoc' => '1', 'tipact' => 'T'],
            'mercurio45' => ['cedtra' => '100200', 'codben' => '999', 'nombre' => 'ALFA HIJO', 'nomcer' => 'CERT ESCOLAR'],
            default => ['cedtra' => '100200', 'prinom' => 'ALFA', 'priape' => 'PEREZ'],
        };

        return array_merge($base, $propios, $attrs);
    }

    private function crear(string $table, array $attrs = []): int
    {
        return DB::table($table)->insertGetId($this->fila($table, $attrs));
    }

    /** @param class-string<SolicitudAprobacionService> $service */
    private function mockSolicitud(string $service, string $table, int $id, string $estado = 'P'): MockInterface
    {
        return $this->partialMock($service, function ($mock) use ($id, $table, $estado): void {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('findOrFail')->with($id)->andReturnUsing(
                fn () => (new (match ($table) {
                    'mercurio31' => Mercurio31::class,
                    'mercurio32' => Mercurio32::class,
                    'mercurio34' => Mercurio34::class,
                    'mercurio45' => Mercurio45::class,
                    default => Mercurio47::class,
                }))->forceFill(['id' => $id, 'estado' => $estado])
            );
        });
    }

    /** @return array<string, string> */
    private function inertiaHeaders(string $component, string $props): array
    {
        return [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => (string) app(HandleInertiaRequests::class)->version(Request::create('/')),
            'X-Inertia-Partial-Component' => $component,
            'X-Inertia-Partial-Data' => $props,
        ];
    }

    #[DataProvider('modulos')]
    public function test_rutas_registradas_y_protegidas(string $path, string $service, string $tipopc, string $table, array $features): void
    {
        $base = ['root', 'index', 'show', 'archivo', 'aprobar', 'rechazar'];
        if ($path !== 'actualizatra') {
            $base[] = 'devolver';
        }

        foreach (array_merge($base, $features) as $name) {
            $route = Route::getRoutes()->getByName("cajas.{$path}.{$name}");
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }

        foreach (array_diff(self::RUTAS_OPCIONALES, $features) as $name) {
            $this->assertNull(Route::getRoutes()->getByName("cajas.{$path}.{$name}"), "no debería existir {$name}");
        }

        if ($path === 'actualizatra') {
            $this->assertNull(Route::getRoutes()->getByName('cajas.actualizatra.devolver'));
        }
    }

    #[DataProvider('modulos')]
    public function test_endpoints_del_spa_legacy_fueron_retirados(string $path): void
    {
        $uris = collect(Route::getRoutes()->getRoutes())->map(fn ($route) => $route->uri())->all();

        foreach (['aprueba', 'infor', 'buscar', 'listar', 'aplicar_filtro', 'borrar-filtro', 'opcional', 'editar', 'buscar-sisu', 'validar-multiafiliacion', 'pendiente-email', 'rezago-correo', 'export'] as $legacy) {
            $this->assertNotContains("cajas/{$path}/{$legacy}", $uris, $legacy);
        }
    }

    #[DataProvider('modulos')]
    public function test_lista_filtra_por_usuario_estado_y_busqueda(string $path, string $service, string $tipopc, string $table, array $features, string $search, array $propios, array $ajenos): void
    {
        $propia = $this->crear($table, $propios);
        $this->crear($table, $ajenos);
        $this->crear($table, ['usuario' => 'otro'] + $ajenos);
        $this->crear($table, ['estado' => 'A'] + $ajenos);
        $temporal = $this->crear($table, ['usuario' => 'otro', 'estado' => 'T'] + $ajenos);

        $svc = app($service);

        $pendientes = $svc->paginate($svc->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'P'])), 'u1');
        $this->assertSame(2, $pendientes->total());

        $busqueda = $svc->paginate($svc->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'P', 'q' => $search])), 'u1');
        $this->assertSame([$propia], collect($busqueda->items())->pluck('id')->all());

        $temporales = $svc->paginate($svc->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'T'])), 'u1');
        $this->assertSame([$temporal], collect($temporales->items())->pluck('id')->all());

        $this->assertSame(['P' => 2, 'D' => 0, 'X' => 0, 'A' => 1, 'T' => 1], $svc->counts('u1'));
    }

    public function test_actualizacion_de_trabajador_solo_lista_cambios_de_trabajador(): void
    {
        $trabajador = $this->crear('mercurio47', ['tipact' => 'T']);
        $this->crear('mercurio47', ['tipact' => 'E']);

        $svc = app(ActualizacionTrabajadorService::class);
        $lista = $svc->paginate($svc->filtersFromRequest(Request::create('/')), 'u1');

        $this->assertSame([$trabajador], collect($lista->items())->pluck('id')->all());
    }

    #[DataProvider('modulos')]
    public function test_fila_del_listado_expone_identificacion_y_urls(string $path, string $service, string $tipopc, string $table, array $features, string $search, array $propios): void
    {
        $id = $this->crear($table, $propios);

        $svc = app($service);
        $row = $svc->mapRows($svc->paginate($svc->filtersFromRequest(Request::create('/')), 'u1'))[0];

        $this->assertSame($id, $row['id']);
        $this->assertSame(route("cajas.{$path}.show", ['id' => $id]), $row['show_url']);
        $this->assertSame(
            in_array('edit', $features, true) ? route("cajas.{$path}.edit", ['id' => $id]) : null,
            $row['edit_url']
        );

        $esperada = match ($path) {
            'aprobacioncon' => '555',
            'aprobacionben' => '777',
            'actualizatra' => '100200',
            default => '100200',
        };
        $this->assertSame($esperada, $row['identificacion']);
    }

    #[DataProvider('modulos')]
    public function test_show_entrega_ficha_y_acciones(string $path, string $service, string $tipopc, string $table, array $features): void
    {
        $id = $this->crear($table);
        DB::table('mercurio37')->insert(['tipopc' => $tipopc, 'numero' => $id, 'coddoc' => 3, 'archivo' => 'cc.pdf']);
        DB::table('mercurio12')->insert(['coddoc' => 3, 'detalle' => 'Cédula']);

        $this->partialMock($service, function ($mock): void {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('secciones')->andReturn([['titulo' => 'Información', 'campos' => [['label' => 'Documento', 'value' => '100200']]]]);
            $mock->shouldReceive('consultarSisu')->andReturn(['titulo' => 'SISU', 'consultado' => true, 'existe' => false, 'afiliada' => false, 'estado' => null, 'datos' => []]);
            $mock->shouldReceive('sisuExtra')->andReturn(null);
            $mock->shouldReceive('aprobarFormulario')->andReturn([
                'campos' => [], 'defaults' => [], 'controles' => [], 'catalogos_disponibles' => true, 'dependencias' => [], 'nota_max' => 2000,
            ]);
        });

        $this->get("/cajas/{$path}/{$id}", $this->inertiaHeaders('Cajas/Solicitudes/pages/Show', 'modulo,solicitud,secciones,adjuntos,acciones,sisu,sisuExtra,aprobar'))
            ->assertOk()
            ->assertJsonPath('component', 'Cajas/Solicitudes/pages/Show')
            ->assertJsonPath('props.solicitud.estado', 'P')
            ->assertJsonPath('props.adjuntos.0.detalle', 'Cédula')
            ->assertJsonPath('props.acciones.aprobar', true)
            ->assertJsonPath('props.acciones.rechazar', true)
            ->assertJsonPath('props.acciones.devolver', $path !== 'actualizatra')
            ->assertJsonPath('props.acciones.editar', in_array('edit', $features, true))
            ->assertJsonPath('props.acciones.multiafiliacion', $path === 'aprobaciontra')
            ->assertJsonPath('props.acciones.trayectoria', $path === 'aprobaciontra');
    }

    public function test_acciones_del_trabajador_segun_estado(): void
    {
        $svc = app(AprobacionTrabajadorService::class);
        $modelo = fn (string $estado) => (new Mercurio31)->forceFill(['estado' => $estado]);

        $pendiente = $svc->acciones($modelo('P'), false);
        $this->assertTrue($pendiente['aportes'] && $pendiente['multiafiliacion'] && $pendiente['trayectoria'] && $pendiente['editar']);
        $this->assertFalse($pendiente['deshacer'] || $pendiente['reaprobar']);

        $aprobada = $svc->acciones($modelo('A'), false);
        $this->assertTrue($aprobada['deshacer'] && $aprobada['reaprobar'] && $aprobada['trayectoria']);
        $this->assertFalse($aprobada['aportes'] || $aprobada['multiafiliacion'] || $aprobada['aprobar']);
    }

    public function test_acciones_de_certificado_y_actualizacion_de_trabajador_son_limitadas(): void
    {
        $certificado = app(AprobacionCertificadoService::class)->acciones((new Mercurio45)->forceFill(['estado' => 'P']), false);
        $this->assertTrue($certificado['aprobar'] && $certificado['devolver'] && $certificado['rechazar']);
        $this->assertFalse($certificado['editar'] || $certificado['aportes'] || $certificado['deshacer'] || $certificado['reaprobar'] || $certificado['multiafiliacion']);

        $actualizacion = app(ActualizacionTrabajadorService::class)->acciones((new Mercurio47)->forceFill(['estado' => 'P']), false);
        $this->assertTrue($actualizacion['aprobar'] && $actualizacion['rechazar']);
        $this->assertFalse($actualizacion['devolver'] || $actualizacion['editar'] || $actualizacion['multiafiliacion'] || $actualizacion['trayectoria']);
    }

    public function test_certificado_y_actualizacion_de_trabajador_no_piden_checklist(): void
    {
        foreach ([AprobacionCertificadoService::class, ActualizacionTrabajadorService::class] as $service) {
            $svc = $this->partialMock($service, function ($mock): void {
                $mock->shouldAllowMockingProtectedMethods();
                $mock->shouldReceive('catalogos')->andReturn([]);
            });

            $this->assertArrayNotHasKey('validaciones_control', $svc->aprobarRules(), $service);
            $formulario = $svc->aprobarFormulario(new Mercurio47);
            $this->assertSame([], $formulario['controles'], $service);
            $this->assertTrue($formulario['catalogos_disponibles'], $service);
        }

        foreach ([AprobacionTrabajadorService::class, AprobacionConyugeService::class, AprobacionBeneficiarioService::class] as $service) {
            $this->assertArrayHasKey('validaciones_control', app($service)->aprobarRules(), $service);
        }
    }

    public function test_certificado_solo_pide_fecha_y_nota(): void
    {
        $mock = $this->mockSolicitud(AprobacionCertificadoService::class, 'mercurio45', 1);
        $mock->shouldReceive('aprobar')
            ->once()
            ->withArgs(fn (int $id, array $data) => $id === 1 && array_keys($data) === ['fecapr', 'nota_aprobar'])
            ->andReturn(true);

        $this->from('/cajas/aprobacioncer/1')->post('/cajas/aprobacioncer/1/aprobar', [])
            ->assertSessionHasErrors(['fecapr', 'nota_aprobar']);

        $this->post('/cajas/aprobacioncer/1/aprobar', ['fecapr' => now()->toDateString(), 'nota_aprobar' => 'Presentado'])
            ->assertRedirect(route('cajas.aprobacioncer.index'))
            ->assertSessionHas('success');
    }

    public function test_trabajador_valida_los_campos_de_aprobacion(): void
    {
        $this->mockSolicitud(AprobacionTrabajadorService::class, 'mercurio31', 1);

        $this->from('/cajas/aprobaciontra/1')->post('/cajas/aprobaciontra/1/aprobar', [])
            ->assertSessionHasErrors(['codsuc', 'codlis', 'vendedor', 'empleador', 'tippag', 'giro', 'fecafi', 'fecapr', 'nota_aprobar', 'validaciones_control']);

        $this->from('/cajas/aprobaciontra/1')->post('/cajas/aprobaciontra/1/aprobar', ['tippag' => 'D'])
            ->assertSessionHasErrors(['codban', 'numcue']);
    }

    public function test_conyuge_y_beneficiario_validan_sus_campos_propios(): void
    {
        $this->mockSolicitud(AprobacionConyugeService::class, 'mercurio32', 1);
        $this->from('/cajas/aprobacioncon/1')->post('/cajas/aprobacioncon/1/aprobar', [])
            ->assertSessionHasErrors(['tippag', 'recsub', 'fecafi', 'fecapr', 'nota_aprobar', 'validaciones_control']);

        $this->mockSolicitud(AprobacionBeneficiarioService::class, 'mercurio34', 1);
        $this->from('/cajas/aprobacionben/1')->post('/cajas/aprobacionben/1/aprobar', [])
            ->assertSessionHasErrors(['giro', 'pago', 'fecafi', 'fecapr', 'nota_aprobar', 'validaciones_control'])
            ->assertSessionDoesntHaveErrors(['numhij', 'fecpre', 'codgir']);
    }

    public function test_formulario_del_trabajador_usa_sucursales_de_sisu_o_el_valor_por_defecto(): void
    {
        $solicitud = (new Mercurio31)->forceFill(['id' => 1, 'nit' => '900111', 'codsuc' => '002']);

        $svc = $this->partialMock(AprobacionTrabajadorService::class, function ($mock): void {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('catalogos')->andReturn([]);
            $mock->shouldReceive('opcionesDinamicas')->with(\Mockery::any(), 'sucursales')->andReturn([['value' => '002', 'label' => '002 SUR']]);
            $mock->shouldReceive('opcionesDinamicas')->with(\Mockery::any(), 'listas')->andReturn([]);
        });

        $formulario = $svc->aprobarFormulario($solicitud);
        $campos = collect($formulario['campos'])->keyBy('name');

        $this->assertSame([['value' => '002', 'label' => '002 SUR']], $campos['codsuc']['options']);
        $this->assertSame([['value' => '001', 'label' => '001']], $campos['codlis']['options']);
        $this->assertSame('002', $formulario['defaults']['codsuc']);
        $this->assertSame('N', $formulario['defaults']['giro']);
        $this->assertArrayNotHasKey('dinamico', $campos['codsuc']);
        $this->assertNotEmpty($formulario['controles']);
    }

    public function test_multiafiliacion_se_delega_al_servicio(): void
    {
        $this->mockSolicitud(AprobacionTrabajadorService::class, 'mercurio31', 1)
            ->shouldReceive('validarMultiafiliacion')->once()->andReturn(['multi' => true]);

        $this->postJson('/cajas/aprobaciontra/1/validar-multiafiliacion')
            ->assertOk()
            ->assertExactJson(['multi' => true]);
    }

    public function test_trayectoria_entrega_trayectorias_y_salarios(): void
    {
        $this->mockSolicitud(AprobacionTrabajadorService::class, 'mercurio31', 1, 'A')
            ->shouldReceive('trayectoria')->once()->andReturn([
                'consultado' => true,
                'trayectorias' => [['fecafi' => '2025-01-01', 'nit' => '900111', 'codsuc' => '001', 'fecret' => '']],
                'salarios' => [['fecha' => '2025-01-01', 'salario' => '1500000']],
            ]);

        $this->get('/cajas/aprobaciontra/1/trayectoria', $this->inertiaHeaders('Cajas/Solicitudes/pages/Trayectoria', 'modulo,solicitud,resultado'))
            ->assertOk()
            ->assertJsonPath('component', 'Cajas/Solicitudes/pages/Trayectoria')
            ->assertJsonPath('props.resultado.trayectorias.0.nit', '900111')
            ->assertJsonPath('props.resultado.salarios.0.salario', '1500000');
    }

    public function test_actualizacion_de_trabajador_exige_el_permiso_62(): void
    {
        $id = $this->crear('mercurio47');
        $payload = ['nota_aprobar' => 'Datos verificados'];

        $this->from("/cajas/actualizatra/{$id}")->post("/cajas/actualizatra/{$id}/aprobar", $payload)
            ->assertRedirect("/cajas/actualizatra/{$id}")
            ->assertSessionHasErrors(['form' => 'El usuario no dispone de permisos de aprobación.']);

        $this->assertSame('P', DB::table('mercurio47')->where('id', $id)->value('estado'));
    }

    public function test_actualizacion_de_trabajador_con_permiso_delega_al_proceso(): void
    {
        DB::table('gener42')->insert(['usuario' => 'u1', 'permiso' => '62']);

        $this->partialMock(ActualizacionTrabajadorService::class, function ($mock): void {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('findOrFail')->with(1)->andReturn((new Mercurio47)->forceFill(['id' => 1, 'estado' => 'P']));
            $mock->shouldReceive('procesarAprobacion')->once()
                ->withArgs(fn (int $id, array $data) => $id === 1 && $data['nota_aprobar'] === 'Datos verificados')
                ->andThrow(new DebugException('SISU rechazó la actualización.', 501));
        });

        $this->from('/cajas/actualizatra/1')->post('/cajas/actualizatra/1/aprobar', ['nota_aprobar' => 'Datos verificados'])
            ->assertSessionHasErrors(['form' => 'SISU rechazó la actualización.']);
    }

    public function test_rechazar_actualizacion_de_trabajador_registra_estado_y_seguimiento(): void
    {
        $id = $this->crear('mercurio47');

        app(ActualizacionTrabajadorService::class)->rechazar($id, '01', 'No procede');

        $this->assertSame('X', DB::table('mercurio47')->where('id', $id)->value('estado'));
        $seguimiento = DB::table('mercurio10')->where('tipopc', '14')->where('numero', $id)->first();
        $this->assertSame('X', $seguimiento->estado);
        $this->assertSame('No procede', $seguimiento->nota);
    }

    public function test_actualizacion_de_trabajador_expone_los_cambios_solicitados(): void
    {
        $id = $this->crear('mercurio47');
        DB::table('mercurio28')->insert(['tipo' => 'T', 'campo' => 'telefono', 'detalle' => 'Teléfono']);
        DB::table('mercurio28')->insert(['tipo' => 'E', 'campo' => 'telefono', 'detalle' => 'Teléfono empresa']);
        DB::table('mercurio33')->insert(['actualizacion' => $id, 'campo' => 'telefono', 'antval' => '111', 'valor' => '222']);

        $cambios = app(ActualizacionTrabajadorService::class)->cambios((new Mercurio47)->forceFill(['id' => $id]));

        $this->assertSame([['campo' => 'telefono', 'label' => 'Teléfono', 'anterior' => '111', 'nuevo' => '222']], $cambios);
    }

    public function test_parametros_para_deshacer_la_afiliacion_de_cada_modulo(): void
    {
        $deshacer = function (SolicitudAprobacionService $svc, $modelo): array {
            $metodo = new \ReflectionMethod($svc, 'deshacerSisu');

            return $metodo->invoke($svc, $modelo, 'deshacer_x', 'nota');
        };

        $trabajador = $deshacer(app(AprobacionTrabajadorService::class), (new Mercurio31)->forceFill([
            'nit' => '900111', 'cedtra' => '100200', 'tipdoc' => '1', 'fecafi' => '2026-01-02',
        ]));
        $this->assertSame(['nit' => '900111', 'cedtra' => '100200', 'coddoc' => '1', 'fecafi' => '2026-01-02', 'nota' => 'nota'], $trabajador['params']);

        $conyuge = $deshacer(app(AprobacionConyugeService::class), (new Mercurio32)->forceFill(['cedtra' => '100200', 'cedcon' => '555', 'tipdoc' => '1']));
        $this->assertSame(['cedtra' => '100200', 'cedcon' => '555', 'tipo_documento' => '1', 'nota' => 'nota'], $conyuge['params']);

        $beneficiario = $deshacer(app(AprobacionBeneficiarioService::class), (new Mercurio34)->forceFill(['cedtra' => '100200', 'numdoc' => '777', 'tipdoc' => '4']));
        $this->assertSame(['cedtra' => '100200', 'numdoc' => '777', 'tipo_documento' => '4', 'nota' => 'nota'], $beneficiario['params']);
    }

    public function test_certificado_no_consulta_sisu(): void
    {
        $sisu = app(AprobacionCertificadoService::class)->consultarSisu((new Mercurio45)->forceFill(['cedtra' => '100200']));

        $this->assertFalse($sisu['habilitado']);
        $this->assertFalse($sisu['consultado']);
    }

    #[DataProvider('modulos')]
    public function test_devolver_y_rechazar_delegan_al_servicio(string $path, string $service, string $tipopc, string $table): void
    {
        $mock = $this->mockSolicitud($service, $table, 1);
        $mock->shouldReceive('rechazar')->once()->with(1, '02', 'No cumple');

        if ($path !== 'actualizatra') {
            $mock->shouldReceive('devolver')->once()->with(1, '01', 'Falta la cédula', ['03']);
            $this->post("/cajas/{$path}/1/devolver", ['codest' => '01', 'nota' => 'Falta la cédula', 'campos_corregir' => ['03']])
                ->assertRedirect(route("cajas.{$path}.index"))
                ->assertSessionHas('success');
        }

        $this->post("/cajas/{$path}/1/rechazar", ['codest' => '02', 'nota' => 'No cumple'])
            ->assertRedirect(route("cajas.{$path}.index"))
            ->assertSessionHas('success');
    }
}
