<?php

namespace Tests\Feature\Cajas;

use App\Exceptions\DebugException;
use App\Http\Middleware\CajasAuthenticated;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Models\Mercurio47;
use App\Services\Cajas\ActualizacionEmpresaService;
use App\Services\Cajas\AprobacionFacultativoService;
use App\Services\Cajas\AprobacionIndependienteService;
use App\Services\Cajas\AprobacionPensionadoService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Flujo común de aprobación para independientes, pensionados, facultativos y actualización de datos de empresa.
 * Usa SQLite en memoria para no tocar la base de datos configurada en phpunit.xml.
 */
class AprobacionSolicitudesTest extends TestCase
{
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

        foreach (['mercurio41', 'mercurio38', 'mercurio36'] as $tabla) {
            Schema::create($tabla, function (Blueprint $table) {
                $table->increments('id');
                $table->string('cedtra')->nullable();
                $table->string('prinom')->nullable();
                $table->string('segnom')->nullable();
                $table->string('priape')->nullable();
                $table->string('segape')->nullable();
                $table->string('usuario')->nullable();
                $table->string('estado', 1)->nullable();
                $table->date('fecsol')->nullable();
                $table->string('motivo')->nullable();
                $table->string('codest', 2)->nullable();
                $table->date('fecest')->nullable();
            });
        }

        Schema::create('mercurio47', function (Blueprint $table) {
            $table->increments('id');
            $table->string('documento')->nullable();
            $table->string('tipo', 2)->nullable();
            $table->string('coddoc', 2)->nullable();
            $table->string('tipact', 1)->nullable();
            $table->string('usuario')->nullable();
            $table->string('estado', 1)->nullable();
            $table->date('fecsol')->nullable();
            $table->string('motivo')->nullable();
            $table->string('codest', 2)->nullable();
            $table->date('fecest')->nullable();
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
     * @return array<string, array{0: string, 1: class-string, 2: string, 3: string, 4: list<string>, 5: string}> path, servicio, tipopc, tabla, rutas opcionales, búsqueda
     */
    public static function modulos(): array
    {
        $todas = ['edit', 'update', 'aportes', 'deshacer_form', 'deshacer', 'reaprobar', 'editar_formulario', 'editar_solicitud'];

        return [
            'independientes' => ['aprobaindepen', AprobacionIndependienteService::class, '13', 'mercurio41', $todas, 'ALFA'],
            'pensionados' => ['aprobacionpen', AprobacionPensionadoService::class, '9', 'mercurio38', $todas, 'ALFA'],
            'facultativos' => ['aprobacionfac', AprobacionFacultativoService::class, '10', 'mercurio36', ['edit', 'update', 'editar_formulario', 'editar_solicitud'], 'ALFA'],
            'actualizacion empresa' => ['actualizaemp', ActualizacionEmpresaService::class, '5', 'mercurio47', [], '900111'],
        ];
    }

    /** @return array<string, mixed> */
    private function fila(string $table, array $attrs = []): array
    {
        $base = ['usuario' => 'u1', 'estado' => 'P', 'fecsol' => '2026-01-10'];

        if ($table === 'mercurio47') {
            return array_merge($base, ['documento' => '900111', 'tipo' => 'E', 'coddoc' => '1', 'tipact' => 'E'], $attrs);
        }

        return array_merge($base, ['cedtra' => '100200', 'prinom' => 'ALFA', 'priape' => 'PEREZ'], $attrs);
    }

    private function crear(string $table, array $attrs = []): int
    {
        return DB::table($table)->insertGetId($this->fila($table, $attrs));
    }

    /**
     * Simula la solicitud existente sin depender de la tabla.
     *
     * @param  class-string  $service
     */
    private function mockSolicitud(string $service, string $table, int $id, string $estado = 'P'): MockInterface
    {
        return $this->partialMock($service, function ($mock) use ($id, $table, $estado): void {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('findOrFail')->with($id)->andReturnUsing(
                fn () => (new (match ($table) {
                    'mercurio41' => Mercurio41::class,
                    'mercurio38' => Mercurio38::class,
                    'mercurio36' => Mercurio36::class,
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

    private function payloadAprobar(): array
    {
        return [
            'codind' => '50', 'todmes' => 'S', 'tipapo' => 'P', 'tipsoc' => '06', 'forpre' => 'S', 'tipdur' => 'S',
            'codsuc' => '001', 'actapr' => 'ACTA-1', 'diahab' => 5, 'tippag' => 'T',
            'fecafi' => now()->toDateString(), 'fecapr' => now()->toDateString(), 'nota_aprobar' => 'Aprobada con éxito',
            'validaciones_control' => ['camara_comercio' => true, 'paz_y_salvo' => true],
        ];
    }

    #[DataProvider('modulos')]
    public function test_rutas_registradas_y_protegidas(string $path, string $service, string $tipopc, string $table, array $features): void
    {
        $esperadas = array_merge(['root', 'index', 'show', 'archivo', 'aprobar', 'devolver', 'rechazar'], $features);

        foreach ($esperadas as $name) {
            $route = Route::getRoutes()->getByName("cajas.{$path}.{$name}");
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }

        foreach (array_diff(['edit', 'update', 'aportes', 'deshacer_form', 'deshacer', 'reaprobar', 'editar_formulario', 'editar_solicitud'], $features) as $name) {
            $this->assertNull(Route::getRoutes()->getByName("cajas.{$path}.{$name}"), "no debería existir {$name}");
        }
    }

    #[DataProvider('modulos')]
    public function test_endpoints_html_del_spa_legacy_fueron_retirados(string $path): void
    {
        $uris = collect(Route::getRoutes()->getRoutes())->map(fn ($route) => $route->uri())->all();

        foreach (['aprueba', 'infor', 'buscar', 'listar', 'aplicar_filtro', 'borrar-filtro', 'opcional', 'editar', 'pendiente-email', 'rezago-correo'] as $legacy) {
            $this->assertNotContains("cajas/{$path}/{$legacy}", $uris, $legacy);
        }
    }

    #[DataProvider('modulos')]
    public function test_lista_filtra_por_usuario_estado_y_busqueda(string $path, string $service, string $tipopc, string $table, array $features, string $search): void
    {
        $propia = $this->crear($table, $table === 'mercurio47' ? ['documento' => '900111'] : ['prinom' => 'ALFA']);
        $this->crear($table, $table === 'mercurio47' ? ['documento' => '900222'] : ['prinom' => 'BETA', 'cedtra' => '222']);
        $this->crear($table, ['usuario' => 'otro']);
        $this->crear($table, ['estado' => 'A']);
        $temporal = $this->crear($table, ['usuario' => 'otro', 'estado' => 'T']);

        $svc = app($service);

        $pendientes = $svc->paginate($svc->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'P'])), 'u1');
        $this->assertSame(2, $pendientes->total());

        $busqueda = $svc->paginate($svc->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'P', 'q' => $search])), 'u1');
        $this->assertSame([$propia], collect($busqueda->items())->pluck('id')->all());

        $temporales = $svc->paginate($svc->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'T'])), 'u1');
        $this->assertSame([$temporal], collect($temporales->items())->pluck('id')->all());

        $this->assertSame(['P' => 2, 'D' => 0, 'X' => 0, 'A' => 1, 'T' => 1], $svc->counts('u1'));
    }

    public function test_actualizacion_de_empresa_solo_lista_cambios_de_empresa(): void
    {
        $empresa = $this->crear('mercurio47', ['tipact' => 'E']);
        $this->crear('mercurio47', ['tipact' => 'T']);

        $svc = app(ActualizacionEmpresaService::class);
        $lista = $svc->paginate($svc->filtersFromRequest(Request::create('/')), 'u1');

        $this->assertSame([$empresa], collect($lista->items())->pluck('id')->all());
        $this->assertSame(1, $svc->counts('u1')['P']);
    }

    #[DataProvider('modulos')]
    public function test_fila_del_listado_expone_urls(string $path, string $service, string $tipopc, string $table, array $features): void
    {
        $id = $this->crear($table);

        $svc = app($service);
        $row = $svc->mapRows($svc->paginate($svc->filtersFromRequest(Request::create('/')), 'u1'))[0];

        $this->assertSame($id, $row['id']);
        $this->assertSame('Pendiente', $row['estado_detalle']);
        $this->assertSame(route("cajas.{$path}.show", ['id' => $id]), $row['show_url']);
        $this->assertSame(
            in_array('edit', $features, true) ? route("cajas.{$path}.edit", ['id' => $id]) : null,
            $row['edit_url']
        );
    }

    #[DataProvider('modulos')]
    public function test_index_devuelve_props_del_listado(string $path, string $service, string $tipopc, string $table): void
    {
        $this->crear($table);
        $this->crear($table);

        $this->get("/cajas/{$path}/index?per_page=25", $this->inertiaHeaders('Cajas/Solicitudes/pages/Index', 'modulo,items,filters,estadoOptions,perPageOptions'))
            ->assertOk()
            ->assertJsonPath('component', 'Cajas/Solicitudes/pages/Index')
            ->assertJsonPath('props.modulo.baseUrl', "/cajas/{$path}/index")
            ->assertJsonPath('props.items.meta.pagination.total', 2)
            ->assertJsonPath('props.filters.per_page', 25)
            ->assertJsonPath('props.estadoOptions.0.count', 2);
    }

    #[DataProvider('modulos')]
    public function test_show_redirige_a_la_lista_si_la_solicitud_no_existe(string $path): void
    {
        $this->get("/cajas/{$path}/999")
            ->assertRedirect(route("cajas.{$path}.index"))
            ->assertSessionHas('error');
    }

    #[DataProvider('modulos')]
    public function test_show_entrega_ficha_adjuntos_seguimiento_y_acciones(string $path, string $service, string $tipopc, string $table): void
    {
        $id = $this->crear($table);
        DB::table('mercurio11')->insert(['codest' => '01', 'detalle' => 'Documento ilegible']);
        DB::table('mercurio12')->insert(['coddoc' => 3, 'detalle' => 'Cédula']);
        DB::table('mercurio37')->insert(['tipopc' => $tipopc, 'numero' => $id, 'coddoc' => 3, 'archivo' => 'cc.pdf']);
        DB::table('mercurio10')->insert([
            'tipopc' => $tipopc, 'numero' => $id, 'item' => 1, 'estado' => 'P', 'nota' => '<b>Radicada</b>', 'fecsis' => '2026-01-10',
        ]);

        $this->partialMock($service, function ($mock): void {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('secciones')->andReturn([['titulo' => 'Información', 'campos' => [['label' => 'Documento', 'value' => '100200']]]]);
            $mock->shouldReceive('consultarSisu')->andReturn(['consultado' => true, 'existe' => false, 'afiliada' => false, 'estado' => null, 'datos' => []]);
            $mock->shouldReceive('aprobarFormulario')->andReturn([
                'campos' => [], 'defaults' => [], 'controles' => [], 'catalogos_disponibles' => false, 'dependencias' => [], 'nota_max' => 2000,
            ]);
        });

        $this->get("/cajas/{$path}/{$id}", $this->inertiaHeaders(
            'Cajas/Solicitudes/pages/Show',
            'modulo,solicitud,secciones,cambios,adjuntos,seguimiento,acciones,sisu,motivos,camposCorregibles,aprobar'
        ))
            ->assertOk()
            ->assertJsonPath('component', 'Cajas/Solicitudes/pages/Show')
            ->assertJsonPath('props.solicitud.estado', 'P')
            ->assertJsonPath('props.adjuntos.0.detalle', 'Cédula')
            ->assertJsonPath('props.adjuntos.0.url', route("cajas.{$path}.show", ['id' => $id]).'/archivos/3')
            ->assertJsonPath('props.seguimiento.0.nota', 'Radicada')
            ->assertJsonPath('props.secciones.0.titulo', 'Información')
            ->assertJsonPath('props.motivos.0.codest', '01')
            ->assertJsonPath('props.acciones.aprobar', true)
            ->assertJsonPath('props.acciones.deshacer', false);
    }

    public function test_acciones_segun_estado_y_capacidades(): void
    {
        $modelo = fn (string $estado) => (new Mercurio41)->forceFill(['estado' => $estado]);
        $svc = app(AprobacionIndependienteService::class);

        $pendiente = $svc->acciones($modelo('P'), false);
        $this->assertTrue($pendiente['editar'] && $pendiente['aprobar'] && $pendiente['devolver'] && $pendiente['rechazar']);
        $this->assertFalse($pendiente['deshacer'] || $pendiente['reaprobar']);

        $aprobada = $svc->acciones($modelo('A'), false);
        $this->assertTrue($aprobada['deshacer'] && $aprobada['reaprobar']);
        $this->assertFalse($aprobada['aprobar'] || $aprobada['editar'] || $aprobada['aportes']);

        $temporal = $svc->acciones($modelo('T'), true);
        $this->assertFalse($temporal['aprobar'] || $temporal['devolver'] || $temporal['rechazar']);

        $facultativo = app(AprobacionFacultativoService::class)->acciones($modelo('A'), true);
        $this->assertFalse($facultativo['deshacer'] || $facultativo['reaprobar'] || $facultativo['aportes']);

        $up = app(ActualizacionEmpresaService::class)->acciones($modelo('P'), false);
        $this->assertTrue($up['aprobar'] && $up['devolver'] && $up['rechazar']);
        $this->assertFalse($up['editar'] || $up['deshacer'] || $up['reaprobar'] || $up['aportes']);
    }

    #[DataProvider('modulos')]
    public function test_aprobar_valida_campos_obligatorios(string $path, string $service, string $tipopc, string $table): void
    {
        $this->mockSolicitud($service, $table, 1);

        $this->from("/cajas/{$path}/1")->post("/cajas/{$path}/1/aprobar", [])
            ->assertRedirect("/cajas/{$path}/1")
            ->assertSessionHasErrors(['fecapr', 'nota_aprobar', 'validaciones_control']);
    }

    #[DataProvider('modulos')]
    public function test_aprobar_exitoso_delega_al_servicio_y_redirige_a_la_lista(string $path, string $service, string $tipopc, string $table): void
    {
        $this->mockSolicitud($service, $table, 1)->shouldReceive('aprobar')
            ->once()
            ->withArgs(fn (int $id, array $data) => $id === 1 && $data['nota_aprobar'] === 'Aprobada con éxito' && $data['validaciones_control']['paz_y_salvo'] === true)
            ->andReturn(true);

        $this->post("/cajas/{$path}/1/aprobar", $this->payloadAprobar())
            ->assertRedirect(route("cajas.{$path}.index"))
            ->assertSessionHas('success');
    }

    #[DataProvider('modulos')]
    public function test_aprobar_con_error_de_negocio_vuelve_con_mensaje(string $path, string $service, string $tipopc, string $table): void
    {
        $this->mockSolicitud($service, $table, 1)->shouldReceive('aprobar')
            ->andThrow(new DebugException('Debe marcar todas las validaciones de control.', 422));

        $this->from("/cajas/{$path}/1")->post("/cajas/{$path}/1/aprobar", $this->payloadAprobar())
            ->assertRedirect("/cajas/{$path}/1")
            ->assertSessionHasErrors(['form' => 'Debe marcar todas las validaciones de control.']);
    }

    public function test_aprobar_exige_datos_bancarios_segun_tipo_de_pago(): void
    {
        $this->mockSolicitud(AprobacionIndependienteService::class, 'mercurio41', 1);

        $this->from('/cajas/aprobaindepen/1')
            ->post('/cajas/aprobaindepen/1/aprobar', ['tippag' => 'D'] + $this->payloadAprobar())
            ->assertSessionHasErrors(['codban', 'numcue']);
    }

    #[DataProvider('modulos')]
    public function test_devolver_requiere_motivo_y_nota(string $path, string $service, string $tipopc, string $table): void
    {
        $this->mockSolicitud($service, $table, 1);

        $this->from("/cajas/{$path}/1")->post("/cajas/{$path}/1/devolver", ['codest' => '', 'nota' => ''])
            ->assertSessionHasErrors(['codest', 'nota']);
    }

    #[DataProvider('modulos')]
    public function test_devolver_y_rechazar_delegan_al_servicio(string $path, string $service, string $tipopc, string $table): void
    {
        $mock = $this->mockSolicitud($service, $table, 1);
        $mock->shouldReceive('devolver')->once()->with(1, '01', 'Falta la cédula', ['03']);
        $mock->shouldReceive('rechazar')->once()->with(1, '02', 'No cumple');

        $this->post("/cajas/{$path}/1/devolver", ['codest' => '01', 'nota' => 'Falta la cédula', 'campos_corregir' => ['03']])
            ->assertRedirect(route("cajas.{$path}.index"))
            ->assertSessionHas('success');

        $this->post("/cajas/{$path}/1/rechazar", ['codest' => '02', 'nota' => 'No cumple'])
            ->assertRedirect(route("cajas.{$path}.index"))
            ->assertSessionHas('success');
    }

    public function test_reaprobar_y_deshacer_delegan_al_servicio(): void
    {
        $mock = $this->mockSolicitud(AprobacionPensionadoService::class, 'mercurio38', 1, 'A');
        $mock->shouldReceive('reaprobar')->once()->with(1, 'Ya existe en SISU');
        $mock->shouldReceive('deshacer')->once()->with(1, 'I', null, 'Cierre', false)
            ->andReturn(['success' => true, 'msj' => 'Se completó el proceso con éxito.']);

        $this->from('/cajas/aprobacionpen/1')->post('/cajas/aprobacionpen/1/reaprobar', [])
            ->assertSessionHasErrors(['nota']);

        $this->post('/cajas/aprobacionpen/1/reaprobar', ['nota' => 'Ya existe en SISU'])
            ->assertRedirect(route('cajas.aprobacionpen.show', ['id' => 1]))
            ->assertSessionHas('success');

        $this->from('/cajas/aprobacionpen/1/deshacer')
            ->post('/cajas/aprobacionpen/1/deshacer', ['action' => 'D', 'nota' => 'x'])
            ->assertSessionHasErrors(['codest']);

        $this->post('/cajas/aprobacionpen/1/deshacer', ['action' => 'I', 'nota' => 'Cierre'])
            ->assertRedirect(route('cajas.aprobacionpen.show', ['id' => 1]))
            ->assertSessionHas('success');
    }

    public function test_deshacer_sin_cambios_en_sisu_muestra_el_motivo(): void
    {
        $this->mockSolicitud(AprobacionIndependienteService::class, 'mercurio41', 1, 'A')
            ->shouldReceive('deshacer')->andReturn(['success' => false, 'msj' => 'No se realizó ninguna acción.']);

        $this->from('/cajas/aprobaindepen/1/deshacer')
            ->post('/cajas/aprobaindepen/1/deshacer', ['action' => 'I', 'nota' => 'Cierre'])
            ->assertSessionHasErrors(['form' => 'No se realizó ninguna acción.']);
    }

    public function test_actualizacion_de_empresa_expone_los_cambios_solicitados(): void
    {
        $id = $this->crear('mercurio47');
        DB::table('mercurio28')->insert(['tipo' => 'E', 'campo' => 'telefono', 'detalle' => 'Teléfono']);
        DB::table('mercurio33')->insert(['actualizacion' => $id, 'campo' => 'telefono', 'antval' => '111', 'valor' => '222']);
        DB::table('mercurio33')->insert(['actualizacion' => $id + 1, 'campo' => 'email', 'antval' => 'a', 'valor' => 'b']);

        $cambios = app(ActualizacionEmpresaService::class)->cambios((new Mercurio47)->forceFill(['id' => $id]));

        $this->assertSame([['campo' => 'telefono', 'label' => 'Teléfono', 'anterior' => '111', 'nuevo' => '222']], $cambios);
    }

    public function test_devolver_actualizacion_de_empresa_registra_estado_y_seguimiento(): void
    {
        $id = $this->crear('mercurio47');
        DB::table('mercurio11')->insert(['codest' => '01', 'detalle' => 'Datos inconsistentes']);

        app(ActualizacionEmpresaService::class)->rechazar($id, '01', 'No procede');

        $this->assertSame('X', DB::table('mercurio47')->where('id', $id)->value('estado'));
        $seguimiento = DB::table('mercurio10')->where('tipopc', '5')->where('numero', $id)->first();
        $this->assertSame('X', $seguimiento->estado);
        $this->assertSame('No procede', $seguimiento->nota);
    }

    #[DataProvider('modulos')]
    public function test_archivo_adjunto_no_disponible(string $path, string $service, string $tipopc, string $table): void
    {
        $id = $this->crear($table);
        $this->mock(SolicitudDetalleService::class, function ($mock) use ($tipopc): void {
            $mock->shouldReceive('archivoAdjunto')->with($tipopc, 1, '03')->andReturn(null);
        });

        $this->get("/cajas/{$path}/{$id}/archivos/03")
            ->assertRedirect(route("cajas.{$path}.show", ['id' => $id]))
            ->assertSessionHas('error');

        $this->getJson("/cajas/{$path}/{$id}/archivos/03")
            ->assertNotFound()
            ->assertJson(['message' => 'El archivo no está disponible.']);
    }

    #[DataProvider('modulos')]
    public function test_archivo_adjunto_se_sirve_en_linea_o_como_descarga(string $path, string $service, string $tipopc, string $table): void
    {
        $id = $this->crear($table);
        $file = tempnam(sys_get_temp_dir(), 'adj');
        file_put_contents($file, '%PDF-1.4 prueba');

        $this->mock(SolicitudDetalleService::class, function ($mock) use ($tipopc, $file): void {
            $mock->shouldReceive('archivoAdjunto')->with($tipopc, 1, '03')->andReturn(['path' => $file, 'name' => 'cc.pdf']);
        });

        $this->assertStringContainsString('inline; filename="cc.pdf"', $this->get("/cajas/{$path}/{$id}/archivos/03")->assertOk()->headers->get('Content-Disposition'));
        $this->assertStringContainsString('attachment; filename="cc.pdf"', $this->get("/cajas/{$path}/{$id}/archivos/03?download=1")->assertOk()->headers->get('Content-Disposition'));
        @unlink($file);
    }
}
