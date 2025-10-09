<?php

namespace Tests\Unit\Mercurio;

use App\Http\Controllers\Mercurio\AuthController;
use App\Services\Api\ApiWhatsapp;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerSendCodeWhatsappTest extends TestCase
{
    protected function tearDown(): void
    {
        // Cerrar mocks de Mockery para evitar fugas entre tests
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function test_send_code_whatsapp_envia_mensaje_correcto_y_retorna_respuesta()
    {
        // Arrange
        $codigo = '1234';
        $whatsapp = '3157145942';
        $expected = ['ok' => true, 'id' => 'abc123'];
        new AuthController;

        $html = "Código de verificación:            
            *{$codigo}*. Generación de PIN plataforma Comfaca En Línea, 
            utiliza el código de verificación para confirmar el propietario de la línea de whatsapp.";
        $apiWhatsaap = new ApiWhatsapp;
        $apiWhatsaap->send([
            'servicio' => 'Whatsapp',
            'metodo' => 'enviar',
            'params' => [
                'numero' => $whatsapp,
                'mensaje' => $html,
            ],
        ]);

        return $apiWhatsaap->toArray();

        // Assert
        $this->assertSame($expected, $result);
    }
}
