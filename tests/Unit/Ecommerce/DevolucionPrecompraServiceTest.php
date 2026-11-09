<?php

namespace Tests\Unit\Ecommerce;

use App\Models\DevolucionPrecompra;
use App\Models\PrecompraServicio;
use App\Services\Ecommerce\DevolucionPrecompraService;
use App\Services\Ecommerce\EstadoDevolucionPrecompra;
use App\Services\Ecommerce\EstadoPrecompra;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DevolucionPrecompraServiceTest extends TestCase
{
    private DevolucionPrecompraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DevolucionPrecompraService;
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        if (! Schema::hasTable('precompras_servicios')) {
            $this->markTestSkipped('Tabla precompras_servicios no disponible en el entorno de test.');
        }

        if (! Schema::hasTable('devolucion_precompra')) {
            Schema::create('devolucion_precompra', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('precompra_id');
                $table->string('estado', 20)->default('SOLICITADA');
                $table->string('motivo_solicitud', 255)->nullable();
                $table->text('nota_afiliado')->nullable();
                $table->text('nota_gestion')->nullable();
                $table->string('usuario_gestion', 50)->nullable();
                $table->timestamp('fecha_solicitud')->useCurrent();
                $table->timestamp('fecha_en_tramite')->nullable();
                $table->timestamp('fecha_resolucion')->nullable();
                $table->timestamps();
                $table->unique('precompra_id');
            });
        }

        if (! Schema::hasColumn('precompras_servicios', 'motivo_vencida')) {
            Schema::table('precompras_servicios', function (Blueprint $table) {
                $table->string('motivo_vencida', 50)->nullable();
                $table->text('detalle_vencida')->nullable();
                $table->timestamp('fecha_vencida')->nullable();
            });
        }
    }

    private function crearPrecompraVencida(): PrecompraServicio
    {
        return PrecompraServicio::create([
            'documento' => '900' . random_int(100000, 999999),
            'codser' => 'TST',
            'numero' => random_int(1, 9999),
            'codben' => '1',
            'valor' => 10000,
            'estado' => EstadoPrecompra::VENCIDA,
            'motivo_vencida' => EstadoPrecompra::MOTIVO_VENCIDA_SIN_CUPOS,
            'fecha_precompra' => now(),
            'fecha_pago' => now()->subDay(),
            'fecha_vencida' => now(),
        ]);
    }

    public function test_solicitar_y_aprobar_pasa_precompra_a_devolucion(): void
    {
        $precompra = $this->crearPrecompraVencida();

        $devolucion = $this->service->solicitar($precompra, 'Prueba', 'Nota afiliado');
        $this->assertSame(EstadoDevolucionPrecompra::SOLICITADA, $devolucion->estado);

        $aprobada = $this->service->aprobar($devolucion, 'operador1', 'OK reembolso');
        $this->assertSame(EstadoDevolucionPrecompra::APROBADO, $aprobada->estado);

        $precompra->refresh();
        $this->assertSame(EstadoPrecompra::DEVOLUCION, $precompra->estado);
    }

    public function test_rechazar_mantiene_precompra_vencida(): void
    {
        $precompra = $this->crearPrecompraVencida();
        $devolucion = $this->service->solicitar($precompra);
        $this->service->rechazar($devolucion, 'operador1', 'Sin soporte');

        $precompra->refresh();
        $this->assertSame(EstadoPrecompra::VENCIDA, $precompra->estado);

        $devolucion->refresh();
        $this->assertSame(EstadoDevolucionPrecompra::RECHAZADA, $devolucion->estado);
    }

    public function test_no_permite_doble_solicitud(): void
    {
        $precompra = $this->crearPrecompraVencida();
        $this->service->solicitar($precompra);
        $precompra->load('devolucion');

        $this->expectException(\RuntimeException::class);
        $this->service->solicitar($precompra);
    }

    protected function tearDown(): void
    {
        DevolucionPrecompra::query()->whereHas('precompra', function ($q) {
            $q->where('codser', 'TST');
        })->delete();
        PrecompraServicio::query()->where('codser', 'TST')->delete();
        parent::tearDown();
    }
}
