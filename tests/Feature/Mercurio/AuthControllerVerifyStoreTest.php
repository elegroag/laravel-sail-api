<?php

namespace Tests\Feature\Mercurio;

use App\Models\Mercurio01;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerVerifyStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Cerrar mocks de Mockery para evitar fugas entre tests
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function test_verify_store_retorna_error_si_no_existe_usuario_mercurio07()
    {
        // Petición con datos válidos pero sin usuario padre en mercurio07
        $payload = [
            'documento' => '12345678',
            'coddoc' => 'CC',
            'tipo' => 'P',
            'delivery_method' => 'email',
        ];

        $resp = $this->postJson('/web/verify_store', $payload);

        $resp->assertOk()
            ->assertJson([
                'success' => false,
            ]);

        // No debe haberse creado registro en mercurio19
        $this->assertDatabaseMissing('mercurio19', [
            'documento' => $payload['documento'],
            'coddoc' => $payload['coddoc'],
            'tipo' => $payload['tipo'],
        ]);
    }

    #[Test]
    public function test_verify_store_envia_codigo_por_email_y_actualiza_mercurio19()
    {
        // Arrange: crear tipo en mercurio06 y usuario mercurio07 (padre)
        Mercurio06::factory()->create(['tipo' => 'P']);
        $user07 = Mercurio07::factory()->activo()->create(['tipo' => 'P']);
        // Crear caja (Mercurio01) requerida por sendCodeEmail()
        Mercurio01::factory()->create();

        // Mock: interceptar new SenderEmail() y simular envío
        $mailMock = Mockery::mock('overload:App\\Services\\Utils\\SenderEmail');
        $mailMock->shouldReceive('setters')->once()->andReturnSelf();
        $mailMock->shouldReceive('send')->once()->andReturn('Correo enviado exitosamente');

        $payload = [
            'documento' => $user07->getDocumento(),
            'coddoc' => $user07->getCoddoc(),
            'tipo' => $user07->getTipo(),
            'delivery_method' => 'email',
        ];

        // Act
        $resp = $this->postJson('/web/verify_store', $payload);

        // Assert
        $resp->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'token',
            ]);

        // Debe existir mercurio19 y con campos actualizados
        $user19 = Mercurio19::where('documento', $payload['documento'])
            ->where('coddoc', $payload['coddoc'])
            ->where('tipo', $payload['tipo'])
            ->first();

        $this->assertNotNull($user19, 'No se creó/actualizó Mercurio19');
        $this->assertNotEmpty($user19->getToken(), 'Token no fue asignado');
        $this->assertNotEmpty($user19->getCodver(), 'Código de verificación no fue generado');
        $this->assertSame(0, (int) $user19->getIntentos(), 'Intentos debe reiniciarse a 0');
        $this->assertNotEmpty($user19->getInicio(), 'Inicio debe establecerse');
    }

    #[Test]
    public function test_verify_store_envia_codigo_por_whatsapp_y_actualiza_mercurio19()
    {
        // Arrange: crear tipo en mercurio06 y usuario mercurio07 con whatsapp
        Mercurio06::factory()->create(['tipo' => 'P']);
        $user07 = Mercurio07::factory()->activo()->create([
            'tipo' => 'P',
            'whatsapp' => '3110000000',
        ]);

        // Mock: interceptar new ApiWhatsapp() para evitar llamadas externas
        $waMock = Mockery::mock('overload:App\\Services\\Api\\ApiWhatsapp');
        $waMock->shouldReceive('send')->once()->andReturnSelf();
        $waMock->shouldReceive('toArray')->once()->andReturn(['ok' => true]);

        $payload = [
            'documento' => $user07->getDocumento(),
            'coddoc' => $user07->getCoddoc(),
            'tipo' => $user07->getTipo(),
            'delivery_method' => 'whatsapp',
        ];

        // Act
        $resp = $this->postJson('/web/verify_store', $payload);

        // Assert
        $resp->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'token',
            ]);

        $user19 = Mercurio19::where('documento', $payload['documento'])
            ->where('coddoc', $payload['coddoc'])
            ->where('tipo', $payload['tipo'])
            ->first();

        $this->assertNotNull($user19, 'No se creó/actualizó Mercurio19');
        $this->assertNotEmpty($user19->getToken(), 'Token no fue asignado');
        $this->assertNotEmpty($user19->getCodver(), 'Código de verificación no fue generado');
        $this->assertSame(0, (int) $user19->getIntentos(), 'Intentos debe reiniciarse a 0');
        $this->assertNotEmpty($user19->getInicio(), 'Inicio debe establecerse');
    }
}
