<?php

namespace Tests\Feature\Cajas;

use App\Exceptions\DebugException;
use App\Http\Middleware\CajasAuthenticated;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Mercurio30;
use App\Services\Cajas\AprobacionEmpresaService;
use App\Services\Cajas\Solicitudes\SolicitudDetalleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Usa SQLite en memoria para no tocar la base de datos configurada en phpunit.xml.
 */
class AprobacionEmpTest extends TestCase
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

        Schema::create('mercurio30', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nit')->nullable();
            $table->string('razsoc')->nullable();
            $table->string('documento')->nullable();
            $table->string('cedrep')->nullable();
            $table->string('usuario')->nullable();
            $table->string('estado', 1)->nullable();
            $table->date('fecsol')->nullable();
            $table->string('sat_cedrep')->nullable();
            $table->string('motivo')->nullable();
            $table->string('codest', 2)->nullable();
            $table->date('fecest')->nullable();
            $table->string('tipper', 1)->nullable();
            $table->string('priape')->nullable();
            $table->string('segape')->nullable();
            $table->string('prinom')->nullable();
            $table->string('segnom')->nullable();
            $table->string('repleg')->nullable();
            $table->string('tipsoc', 2)->nullable();
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

    private function empresa(array $attrs = []): int
    {
        return DB::table('mercurio30')->insertGetId(array_merge([
            'nit' => '900100200',
            'razsoc' => 'ACME SAS',
            'usuario' => 'u1',
            'estado' => 'P',
            'fecsol' => '2026-01-10',
        ], $attrs));
    }

    private function mockEmpresaExistente(int $id, string $estado = 'P'): MockInterface
    {
        return $this->partialMock(AprobacionEmpresaService::class, function ($mock) use ($id, $estado): void {
            $mock->shouldReceive('findOrFail')->with($id)->andReturn(new Mercurio30(['id' => $id, 'estado' => $estado]));
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

    public function test_rutas_registradas_y_protegidas(): void
    {
        foreach ([
            'index', 'show', 'edit', 'update', 'aportes', 'deshacer_form', 'archivo',
            'aprobar', 'devolver', 'rechazar', 'reaprobar', 'deshacer', 'editar_formulario', 'editar_solicitud',
        ] as $name) {
            $route = Route::getRoutes()->getByName("cajas.aprobacionemp.{$name}");
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_endpoints_html_del_spa_legacy_fueron_retirados(): void
    {
        $uris = collect(Route::getRoutes()->getRoutes())->map(fn ($route) => $route->uri())->all();

        foreach (['aprueba', 'infor', 'buscar', 'listar', 'aplicar_filtro', 'borrar-filtro', 'devolver', 'rechazar', 'reaprobar', 'deshacer', 'opcional', 'editar'] as $legacy) {
            $this->assertNotContains("cajas/aprobacionemp/{$legacy}", $uris, $legacy);
        }
    }

    public function test_lista_filtra_por_usuario_estado_y_busqueda(): void
    {
        $propia = $this->empresa(['nit' => '111', 'razsoc' => 'ALFA']);
        $this->empresa(['nit' => '222', 'razsoc' => 'BETA']);
        $this->empresa(['nit' => '333', 'usuario' => 'otro']);
        $this->empresa(['nit' => '444', 'estado' => 'A']);
        $temporal = $this->empresa(['nit' => '555', 'usuario' => 'otro', 'estado' => 'T']);

        $service = app(AprobacionEmpresaService::class);

        $pendientes = $service->paginate($service->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'P'])), 'u1');
        $this->assertSame(2, $pendientes->total());

        $busqueda = $service->paginate($service->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'P', 'q' => 'alf'])), 'u1');
        $this->assertSame([$propia], collect($busqueda->items())->pluck('id')->all());

        $temporales = $service->paginate($service->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'T'])), 'u1');
        $this->assertSame([$temporal], collect($temporales->items())->pluck('id')->all());

        $this->assertSame(['P' => 2, 'D' => 0, 'X' => 0, 'A' => 1, 'T' => 1], $service->counts('u1'));
    }

    public function test_filtros_invalidos_se_normalizan(): void
    {
        $filters = app(AprobacionEmpresaService::class)
            ->filtersFromRequest(Request::create('/', 'GET', ['estado' => 'Z', 'per_page' => 7, 'q' => '  abc ']));

        $this->assertSame(['estado' => 'P', 'q' => 'abc', 'per_page' => 10], $filters);
    }

    public function test_fila_del_listado_expone_urls_y_alerta(): void
    {
        $id = $this->empresa(['fecsol' => now()->subDays(20)->toDateString(), 'sat_cedrep' => '123']);

        $service = app(AprobacionEmpresaService::class);
        $row = $service->mapRows($service->paginate($service->filtersFromRequest(Request::create('/')), 'u1'))[0];

        $this->assertSame($id, $row['id']);
        $this->assertSame('SAT', $row['recepcion']);
        $this->assertSame('danger', $row['alerta']);
        $this->assertSame('Pendiente', $row['estado_detalle']);
        $this->assertSame(route('cajas.aprobacionemp.show', ['id' => $id]), $row['show_url']);
        $this->assertSame(route('cajas.aprobacionemp.edit', ['id' => $id]), $row['edit_url']);
    }

    public function test_index_devuelve_props_del_listado(): void
    {
        $this->empresa(['nit' => '111']);
        $this->empresa(['nit' => '222']);

        $response = $this->get('/cajas/aprobacionemp/index?per_page=25', $this->inertiaHeaders(
            'Cajas/AprobacionEmp/Index',
            'items,filters,estadoOptions,perPageOptions'
        ));

        $response->assertOk()
            ->assertJsonPath('component', 'Cajas/AprobacionEmp/Index')
            ->assertJsonPath('props.items.meta.pagination.total', 2)
            ->assertJsonPath('props.filters.per_page', 25)
            ->assertJsonPath('props.estadoOptions.0.value', 'P')
            ->assertJsonPath('props.estadoOptions.0.count', 2);
    }

    public function test_show_redirige_a_la_lista_si_la_solicitud_no_existe(): void
    {
        $this->get('/cajas/aprobacionemp/999')
            ->assertRedirect(route('cajas.aprobacionemp.index'))
            ->assertSessionHas('error');
    }

    public function test_show_entrega_ficha_adjuntos_seguimiento_y_acciones(): void
    {
        $id = $this->empresa(['estado' => 'P']);
        DB::table('mercurio11')->insert(['codest' => '01', 'detalle' => 'Documento ilegible']);
        DB::table('mercurio12')->insert(['coddoc' => 3, 'detalle' => 'RUT']);
        DB::table('mercurio37')->insert(['tipopc' => '2', 'numero' => $id, 'coddoc' => 3, 'archivo' => 'rut.pdf']);
        DB::table('mercurio10')->insert([
            'tipopc' => '2', 'numero' => $id, 'item' => 1, 'estado' => 'P', 'nota' => '<b>Radicada</b>', 'fecsis' => '2026-01-10',
        ]);

        $this->partialMock(AprobacionEmpresaService::class, function ($mock): void {
            $mock->shouldReceive('consultarSisu')->andReturn(['consultado' => true, 'existe' => false, 'afiliada' => false, 'estado' => null, 'datos' => []]);
            $mock->shouldReceive('secciones')->andReturn([['titulo' => 'Información de la empresa', 'campos' => [['label' => 'NIT', 'value' => '900100200']]]]);
            $mock->shouldReceive('aprobarFormulario')->andReturn(['options' => [], 'defaults' => [], 'catalogos_disponibles' => false]);
        });

        $response = $this->get("/cajas/aprobacionemp/{$id}", $this->inertiaHeaders(
            'Cajas/AprobacionEmp/Show',
            'solicitud,secciones,adjuntos,seguimiento,acciones,sisu,motivos,camposCorregibles,aprobar'
        ));

        $response->assertOk()
            ->assertJsonPath('component', 'Cajas/AprobacionEmp/Show')
            ->assertJsonPath('props.solicitud.estado_detalle', 'Pendiente')
            ->assertJsonPath('props.adjuntos.0.detalle', 'RUT')
            ->assertJsonPath('props.adjuntos.0.url', route('cajas.aprobacionemp.show', ['id' => $id]).'/archivos/3')
            ->assertJsonPath('props.seguimiento.0.nota', 'Radicada')
            ->assertJsonPath('props.motivos.0.codest', '01')
            ->assertJsonPath('props.camposCorregibles.0.coddoc', '3')
            ->assertJsonPath('props.acciones.aprobar', true)
            ->assertJsonPath('props.acciones.deshacer', false);
    }

    public function test_acciones_segun_estado(): void
    {
        $service = app(AprobacionEmpresaService::class);

        $pendiente = $service->acciones(new Mercurio30(['estado' => 'P']), false);
        $this->assertTrue($pendiente['editar'] && $pendiente['aprobar'] && $pendiente['devolver'] && $pendiente['rechazar']);
        $this->assertFalse($pendiente['deshacer'] || $pendiente['reaprobar']);

        $aprobada = $service->acciones(new Mercurio30(['estado' => 'A']), false);
        $this->assertTrue($aprobada['deshacer'] && $aprobada['reaprobar']);
        $this->assertFalse($aprobada['aprobar'] || $aprobada['editar'] || $aprobada['aportes']);

        $temporal = $service->acciones(new Mercurio30(['estado' => 'T']), true);
        $this->assertFalse($temporal['aprobar'] || $temporal['devolver'] || $temporal['rechazar']);
        $this->assertTrue($temporal['reaprobar']);

        $devuelta = $service->acciones(new Mercurio30(['estado' => 'D']), true);
        $this->assertTrue($devuelta['aprobar'] && $devuelta['reaprobar']);
        $this->assertFalse($devuelta['editar']);
    }

    public function test_aprobar_valida_campos_obligatorios(): void
    {
        $this->mockEmpresaExistente(5);

        $response = $this->from('/cajas/aprobacionemp/5')->post('/cajas/aprobacionemp/5/aprobar', []);

        $response->assertRedirect('/cajas/aprobacionemp/5');
        $response->assertSessionHasErrors(['tipdur', 'codsuc', 'actapr', 'diahab', 'fecafi', 'fecapr', 'nota_aprobar', 'validaciones_control']);
    }

    private function payloadAprobar(): array
    {
        return [
            'tipdur' => 'S', 'codind' => '03', 'todmes' => 'S', 'forpre' => 'S', 'pymes' => 'N',
            'contratista' => 'N', 'tipemp' => 'P', 'tipapo' => 'P', 'tipsoc' => '01', 'ofiafi' => '01',
            'colegio' => 'N', 'codsuc' => '001', 'actapr' => 'ACTA-1', 'diahab' => 5,
            'fecafi' => now()->toDateString(), 'fecapr' => now()->toDateString(), 'nota_aprobar' => 'Aprobada',
            'validaciones_control' => ['camara_comercio' => true, 'paz_y_salvo' => true, 'aportes_empresa' => true],
        ];
    }

    public function test_aprobar_exitoso_delega_al_servicio_y_redirige_a_la_lista(): void
    {
        $this->mockEmpresaExistente(5)->shouldReceive('aprobar')
            ->once()
            ->withArgs(fn (int $id, array $data) => $id === 5 && $data['codsuc'] === '001' && $data['validaciones_control']['aportes_empresa'] === true)
            ->andReturn(true);

        $this->post('/cajas/aprobacionemp/5/aprobar', $this->payloadAprobar())
            ->assertRedirect(route('cajas.aprobacionemp.index'))
            ->assertSessionHas('success');
    }

    public function test_aprobar_con_error_de_negocio_vuelve_con_mensaje(): void
    {
        $this->mockEmpresaExistente(5)->shouldReceive('aprobar')
            ->andThrow(new DebugException('Debe marcar todas las validaciones de control.', 422));

        $this->from('/cajas/aprobacionemp/5')->post('/cajas/aprobacionemp/5/aprobar', $this->payloadAprobar())
            ->assertRedirect('/cajas/aprobacionemp/5')
            ->assertSessionHasErrors(['form' => 'Debe marcar todas las validaciones de control.']);
    }

    public function test_devolver_requiere_motivo_y_nota(): void
    {
        $this->mockEmpresaExistente(5);

        $this->from('/cajas/aprobacionemp/5')->post('/cajas/aprobacionemp/5/devolver', ['codest' => '', 'nota' => ''])
            ->assertSessionHasErrors(['codest', 'nota']);
    }

    public function test_devolver_y_rechazar_delegan_al_servicio(): void
    {
        $mock = $this->mockEmpresaExistente(5);
        $mock->shouldReceive('devolver')->once()->with(5, '01', 'Falta el RUT', ['03', '04']);
        $mock->shouldReceive('rechazar')->once()->with(5, '02', 'No cumple');

        $this->post('/cajas/aprobacionemp/5/devolver', ['codest' => '01', 'nota' => 'Falta el RUT', 'campos_corregir' => ['03', '04']])
            ->assertRedirect(route('cajas.aprobacionemp.index'))
            ->assertSessionHas('success');

        $this->post('/cajas/aprobacionemp/5/rechazar', ['codest' => '02', 'nota' => 'No cumple'])
            ->assertRedirect(route('cajas.aprobacionemp.index'))
            ->assertSessionHas('success');
    }

    public function test_deshacer_exige_motivo_solo_para_devolver_o_rechazar(): void
    {
        $mock = $this->mockEmpresaExistente(5);
        $mock->shouldReceive('deshacer')->once()->with(5, 'I', null, 'Cierre', false)
            ->andReturn(['success' => true, 'msj' => 'Se completó el proceso con éxito.']);

        $this->from('/cajas/aprobacionemp/5/deshacer')
            ->post('/cajas/aprobacionemp/5/deshacer', ['action' => 'D', 'nota' => 'x'])
            ->assertSessionHasErrors(['codest']);

        $this->post('/cajas/aprobacionemp/5/deshacer', ['action' => 'I', 'nota' => 'Cierre'])
            ->assertRedirect(route('cajas.aprobacionemp.show', ['id' => 5]))
            ->assertSessionHas('success');
    }

    public function test_deshacer_sin_cambios_en_sisu_muestra_el_motivo(): void
    {
        $mock = $this->mockEmpresaExistente(5);
        $mock->shouldReceive('deshacer')->andReturn(['success' => false, 'msj' => 'No se realizó ninguna acción.']);

        $this->from('/cajas/aprobacionemp/5/deshacer')
            ->post('/cajas/aprobacionemp/5/deshacer', ['action' => 'I', 'nota' => 'Cierre'])
            ->assertSessionHasErrors(['form' => 'No se realizó ninguna acción.']);
    }

    public function test_reaprobar_requiere_nota_y_delega(): void
    {
        $mock = $this->mockEmpresaExistente(5);
        $mock->shouldReceive('reaprobar')->once()->with(5, 'Ya existe en SISU');

        $this->from('/cajas/aprobacionemp/5')->post('/cajas/aprobacionemp/5/reaprobar', [])
            ->assertSessionHasErrors(['nota']);

        $this->post('/cajas/aprobacionemp/5/reaprobar', ['nota' => 'Ya existe en SISU'])
            ->assertRedirect(route('cajas.aprobacionemp.show', ['id' => 5]))
            ->assertSessionHas('success');
    }

    public function test_reaprobar_actualiza_estado_y_seguimiento(): void
    {
        $id = $this->empresa(['estado' => 'D']);

        app(AprobacionEmpresaService::class)->reaprobar($id, 'Ya existe en SISU');

        $this->assertSame('A', DB::table('mercurio30')->where('id', $id)->value('estado'));
        $seguimiento = DB::table('mercurio10')->where('tipopc', '2')->where('numero', $id)->first();
        $this->assertSame('A', $seguimiento->estado);
        $this->assertSame('Ya existe en SISU', $seguimiento->nota);
    }

    public function test_actualizar_registra_cambios_y_seguimiento(): void
    {
        $id = $this->empresa(['tipper' => 'N', 'priape' => 'PEREZ', 'prinom' => 'ANA', 'tipsoc' => '01']);

        $this->put("/cajas/aprobacionemp/{$id}", ['tipper' => 'N', 'priape' => 'GOMEZ', 'prinom' => 'ANA', 'tipsoc' => '3'])
            ->assertRedirect(route('cajas.aprobacionemp.show', ['id' => $id]))
            ->assertSessionHas('success');

        $empresa = DB::table('mercurio30')->where('id', $id)->first();
        $this->assertSame('GOMEZ', $empresa->priape);
        $this->assertSame('GOMEZ ANA', $empresa->repleg);
        $this->assertSame('03', $empresa->tipsoc);
        $this->assertStringContainsString('u1', DB::table('mercurio10')->where('numero', $id)->value('nota'));
    }

    public function test_actualizar_rechaza_solicitud_no_pendiente(): void
    {
        $id = $this->empresa(['estado' => 'A', 'priape' => 'PEREZ']);

        $this->from("/cajas/aprobacionemp/{$id}/edit")
            ->put("/cajas/aprobacionemp/{$id}", ['priape' => 'GOMEZ'])
            ->assertSessionHasErrors(['form' => 'Solo se pueden editar solicitudes pendientes']);

        $this->assertSame('PEREZ', DB::table('mercurio30')->where('id', $id)->value('priape'));
    }

    public function test_archivo_adjunto_no_disponible_vuelve_a_la_ficha(): void
    {
        $id = $this->empresa();
        $this->mock(SolicitudDetalleService::class, function ($mock): void {
            $mock->shouldReceive('archivoAdjunto')->with('2', 1, '03')->andReturn(null);
        });

        $this->get("/cajas/aprobacionemp/{$id}/archivos/03")
            ->assertRedirect(route('cajas.aprobacionemp.show', ['id' => $id]))
            ->assertSessionHas('error');
    }

    public function test_archivo_adjunto_no_disponible_responde_json_en_ajax(): void
    {
        $id = $this->empresa();
        $this->mock(SolicitudDetalleService::class, function ($mock): void {
            $mock->shouldReceive('archivoAdjunto')->with('2', 1, '03')->andReturn(null);
        });

        $this->getJson("/cajas/aprobacionemp/{$id}/archivos/03")
            ->assertNotFound()
            ->assertJson(['message' => 'El archivo no está disponible.']);
    }

    public function test_archivo_adjunto_se_sirve_en_linea(): void
    {
        $id = $this->empresa();
        $path = tempnam(sys_get_temp_dir(), 'adj');
        file_put_contents($path, '%PDF-1.4 prueba');

        $this->mock(SolicitudDetalleService::class, function ($mock) use ($path): void {
            $mock->shouldReceive('archivoAdjunto')->with('2', 1, '03')->andReturn(['path' => $path, 'name' => 'rut.pdf']);
        });

        $response = $this->get("/cajas/aprobacionemp/{$id}/archivos/03");

        $response->assertOk();
        $this->assertStringContainsString('inline; filename="rut.pdf"', $response->headers->get('Content-Disposition'));
        @unlink($path);
    }
}
