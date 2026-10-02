<?php

namespace Tests\Feature\Mercurio;

use App\Exceptions\DebugException;
use App\Models\CarnetToken;
use App\Services\Carnet\CarnetArchivoService;
use App\Services\Carnet\CarnetDigitalService;
use App\Services\Carnet\CarnetEmailService;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

/**
 * CarnetToken se simula con un alias de Mockery para no escribir en la base de datos compartida de pruebas.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class CarnetEnviarTest extends TestCase
{
    private const USER = ['documento' => '1117504321', 'coddoc' => '1'];

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        $this->withoutMiddleware();

        $this->mock(CarnetDigitalService::class, function ($mock) {
            $mock->shouldReceive('datosCarnet')->andReturn(['trabajador' => [], 'beneficiarios' => [], 'consultado' => '']);
            $mock->shouldReceive('urlVerificacion')->andReturn('https://example.test/verificar/x');
        });
        $this->mock(CarnetArchivoService::class, function ($mock) {
            $mock->shouldReceive('pdf')->andReturn('%PDF-fake');
            $mock->shouldReceive('png')->andReturn("\x89PNG-fake");
        });

        $token = Mockery::mock('alias:'.CarnetToken::class);
        $token->shouldReceive('obtenerParaAfiliado')->andReturn($token);
    }

    public function test_envia_el_pdf_al_correo_del_afiliado(): void
    {
        $this->mock(CarnetEmailService::class, function ($mock) {
            $mock->shouldReceive('enviar')
                ->once()
                ->with(self::USER, 'T', '%PDF-fake', 'carnet-comfaca-1117504321.pdf', 'PDF')
                ->andReturn(['email' => 'edwin@example.com', 'email_masked' => 'e****@example.com']);
        });

        $this->withSession(['user' => self::USER, 'tipo' => 'T'])
            ->postJson('/mercurio/carnet/enviar/pdf')
            ->assertOk()
            ->assertJson(['success' => true, 'email_masked' => 'e****@example.com']);
    }

    public function test_responde_error_si_el_afiliado_no_tiene_correo(): void
    {
        $this->mock(CarnetEmailService::class, function ($mock) {
            $mock->shouldReceive('enviar')->andThrow(new DebugException('No tienes un correo electrónico válido registrado en tu cuenta.'));
        });

        $this->withSession(['user' => self::USER, 'tipo' => 'T'])
            ->postJson('/mercurio/carnet/enviar/png')
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_rechaza_usuarios_que_no_son_trabajadores(): void
    {
        $this->mock(CarnetEmailService::class, fn ($mock) => $mock->shouldNotReceive('enviar'));

        $this->withSession(['user' => self::USER, 'tipo' => 'E'])
            ->postJson('/mercurio/carnet/enviar/pdf')
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_rechaza_formato_no_permitido(): void
    {
        $this->mock(CarnetEmailService::class, fn ($mock) => $mock->shouldNotReceive('enviar'));

        $this->withSession(['user' => self::USER, 'tipo' => 'T'])
            ->postJson('/mercurio/carnet/enviar/svg')
            ->assertClientError();
    }
}
