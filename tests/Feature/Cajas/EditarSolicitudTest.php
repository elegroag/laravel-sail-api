<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\CajaServices\EditarSolicitudFormulario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Usa SQLite en memoria para no tocar la base de datos configurada en phpunit.xml.
 */
class EditarSolicitudTest extends TestCase
{
    private const MODULOS = [
        'aprobacionemp',
        'aprobaciontra',
        'aprobacioncon',
        'aprobacionben',
        'aprobaindepen',
        'aprobacionpen',
        'aprobacionfac',
        'aprobacioncom',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('mercurio31', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cedtra')->nullable();
            $table->string('priape')->nullable();
            $table->string('prinom')->nullable();
            $table->string('email')->nullable();
            $table->string('estado', 1)->nullable();
        });

        Schema::create('mercurio30', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nit')->nullable();
            $table->string('tipper', 1)->nullable();
            $table->string('priape')->nullable();
            $table->string('segape')->nullable();
            $table->string('prinom')->nullable();
            $table->string('segnom')->nullable();
            $table->string('repleg')->nullable();
            $table->string('tipsoc', 2)->nullable();
            $table->string('estado', 1)->nullable();
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

        $this->withoutMiddleware(CajasAuthenticated::class);
        $this->withSession(['user' => ['usuario' => 'funcionario1']]);
    }

    public function test_edita_trabajador_pendiente_y_registra_seguimiento(): void
    {
        $id = DB::table('mercurio31')->insertGetId([
            'cedtra' => '1001',
            'priape' => 'PEREZ',
            'prinom' => 'ANA',
            'email' => 'ana@correo.co',
            'estado' => 'P',
        ]);

        $response = $this->postJson('/cajas/aprobaciontra/editar-solicitud', [
            'id' => $id,
            'priape' => 'GOMEZ',
            'prinom' => 'ANA',
            'email' => 'nuevo@correo.co',
            'cedtra' => '9999',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('cambios', ['priape', 'email']);

        $solicitud = DB::table('mercurio31')->where('id', $id)->first();
        $this->assertSame('GOMEZ', $solicitud->priape);
        $this->assertSame('nuevo@correo.co', $solicitud->email);
        $this->assertSame('1001', $solicitud->cedtra);

        $seguimiento = DB::table('mercurio10')->where('tipopc', '1')->where('numero', $id)->first();
        $this->assertNotNull($seguimiento);
        $this->assertSame('P', $seguimiento->estado);
        $this->assertSame(1, (int) $seguimiento->item);
        $this->assertStringContainsString('funcionario1', $seguimiento->nota);
        $this->assertStringContainsString('priape, email', $seguimiento->nota);
        $this->assertNull($seguimiento->campos_corregir);
    }

    #[DataProvider('estadosNoEditables')]
    public function test_rechaza_edicion_si_la_solicitud_no_esta_pendiente(string $estado): void
    {
        $id = DB::table('mercurio31')->insertGetId([
            'cedtra' => '1001',
            'priape' => 'PEREZ',
            'estado' => $estado,
        ]);

        $response = $this->postJson('/cajas/aprobaciontra/editar-solicitud', [
            'id' => $id,
            'priape' => 'GOMEZ',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 422);

        $this->assertSame('PEREZ', DB::table('mercurio31')->where('id', $id)->value('priape'));
        $this->assertSame(0, DB::table('mercurio10')->count());
    }

    public static function estadosNoEditables(): array
    {
        return [
            'aprobada' => ['A'],
            'devuelta' => ['D'],
            'rechazada' => ['X'],
            'temporal' => ['T'],
        ];
    }

    public function test_retorna_404_si_la_solicitud_no_existe(): void
    {
        $response = $this->postJson('/cajas/aprobaciontra/editar-solicitud', [
            'id' => 999,
            'priape' => 'GOMEZ',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 404);
    }

    public function test_sin_cambios_no_registra_seguimiento(): void
    {
        $id = DB::table('mercurio31')->insertGetId([
            'cedtra' => '1001',
            'priape' => 'PEREZ',
            'estado' => 'P',
        ]);

        $response = $this->postJson('/cajas/aprobaciontra/editar-solicitud', [
            'id' => $id,
            'priape' => 'PEREZ',
            'prinom' => '',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('cambios', []);

        $this->assertSame(0, DB::table('mercurio10')->count());
    }

    public function test_valida_formato_de_los_campos(): void
    {
        $id = DB::table('mercurio31')->insertGetId(['cedtra' => '1001', 'estado' => 'P']);

        $response = $this->postJson('/cajas/aprobaciontra/editar-solicitud', [
            'id' => $id,
            'email' => 'correo-invalido',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 422);
        $this->assertArrayHasKey('email', $response->json('errors'));
    }

    public function test_empresa_recalcula_representante_legal_segun_tipo_persona(): void
    {
        $id = DB::table('mercurio30')->insertGetId([
            'nit' => '900100200',
            'tipper' => 'N',
            'priape' => 'PEREZ',
            'prinom' => 'ANA',
            'repleg' => 'PEREZ ANA',
            'tipsoc' => '01',
            'estado' => 'P',
        ]);

        $response = $this->postJson('/cajas/aprobacionemp/editar-solicitud', [
            'id' => $id,
            'tipper' => 'N',
            'priape' => 'GOMEZ',
            'segape' => 'RUIZ',
            'prinom' => 'ANA',
            'tipsoc' => '3',
            'nit' => '111',
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $empresa = DB::table('mercurio30')->where('id', $id)->first();
        $this->assertSame('GOMEZ RUIZ ANA', $empresa->repleg);
        $this->assertSame('03', $empresa->tipsoc);
        $this->assertSame('900100200', $empresa->nit);
    }

    public function test_formulario_devuelve_datos_y_campos_de_solicitud_pendiente(): void
    {
        $this->mock(EditarSolicitudFormulario::class, function ($mock): void {
            $mock->shouldReceive('campos')->once()->with('1')->andReturn([
                ['name' => 'priape', 'label' => 'Primer apellido', 'type' => 'text', 'form_type' => 'input', 'data_source' => [], 'grupo' => 'Datos personales'],
            ]);
        });

        $id = DB::table('mercurio31')->insertGetId(['cedtra' => '1001', 'priape' => 'PEREZ', 'estado' => 'P']);

        $response = $this->postJson('/cajas/aprobaciontra/editar-formulario', ['id' => $id]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.priape', 'PEREZ')
            ->assertJsonPath('campos.0.name', 'priape');
    }

    public function test_formulario_genera_componentes_agrupados_por_seccion(): void
    {
        $campos = collect(app(EditarSolicitudFormulario::class)->campos('1'))->keyBy('name');

        $this->assertSame(['input', 'text'], [$campos['priape']['form_type'], $campos['priape']['type']]);
        $this->assertSame(['date', 'text'], [$campos['fecnac']['form_type'], $campos['fecnac']['type']]);
        $this->assertSame(['input', 'email'], [$campos['email']['form_type'], $campos['email']['type']]);
        $this->assertSame(['input', 'number'], [$campos['salario']['form_type'], $campos['salario']['type']]);
        $this->assertSame('select', $campos['tipsal']['form_type']);
        $this->assertNotEmpty($campos['tipsal']['data_source']);
        $this->assertArrayNotHasKey('cedtra', $campos->all());

        $this->assertSame('Datos personales', $campos['priape']['grupo']);
        $this->assertSame('Residencia y contacto', $campos['email']['grupo']);
        $this->assertSame('Datos laborales', $campos['salario']['grupo']);

        $orden = $campos->pluck('order')->values()->all();
        $ordenado = $orden;
        sort($ordenado);
        $this->assertSame($ordenado, $orden);
    }

    public function test_formulario_rechaza_solicitud_no_pendiente(): void
    {
        $id = DB::table('mercurio31')->insertGetId(['cedtra' => '1001', 'estado' => 'A']);

        $response = $this->postJson('/cajas/aprobaciontra/editar-formulario', ['id' => $id]);

        $response->assertOk()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 422);
    }

    public function test_rutas_de_edicion_registradas_y_protegidas(): void
    {
        foreach (self::MODULOS as $modulo) {
            foreach (['editar-formulario', 'editar-solicitud'] as $accion) {
                $route = Route::getRoutes()->match(Request::create("/cajas/{$modulo}/{$accion}", 'POST'));
                $this->assertContains('cajas.auth', $route->gatherMiddleware(), "{$modulo}/{$accion}");
            }
        }
    }
}
