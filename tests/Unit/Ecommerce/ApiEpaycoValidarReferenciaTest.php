<?php

namespace Tests\Unit\Ecommerce;

use App\Services\Api\ApiEpayco;
use ReflectionMethod;
use Tests\TestCase;

class ApiEpaycoValidarReferenciaTest extends TestCase
{
    public function test_detecta_error_de_datos_o_conexion_en_envelope(): void
    {
        $payload = [
            'status' => false,
            'message' => 'Error de datos o conexión.',
            'data' => [
                'status' => 'error',
                'description' => 'Error de datos o conexión verifique de nuevo.',
            ],
        ];

        $this->assertTrue($this->invokeProtected('esRespuestaErrorEpayco', [$payload]));
        $this->assertSame(
            'Error de datos o conexión verifique de nuevo.',
            $this->invokeProtected('mensajeErrorEpayco', [$payload])
        );
    }

    public function test_detecta_error_plano_sin_data(): void
    {
        $payload = [
            'status' => 'error',
            'description' => 'Error de datos o conexión verifique de nuevo.',
        ];

        $this->assertTrue($this->invokeProtected('esRespuestaErrorEpayco', [$payload]));
        $this->assertSame(
            'Error de datos o conexión verifique de nuevo.',
            $this->invokeProtected('mensajeErrorEpayco', [$payload])
        );
    }

    public function test_no_marca_error_en_transaccion_aceptada(): void
    {
        $payload = [
            'success' => true,
            'data' => [
                'x_cod_transaction_state' => 1,
                'x_response' => 'Aceptada',
                'x_ref_payco' => 384302186,
            ],
        ];

        $this->assertFalse($this->invokeProtected('esRespuestaErrorEpayco', [$payload]));
    }

    /**
     * @param  list<mixed>  $args
     */
    private function invokeProtected(string $method, array $args): mixed
    {
        $api = new ApiEpayco;
        $ref = new ReflectionMethod(ApiEpayco::class, $method);
        $ref->setAccessible(true);

        return $ref->invoke($api, ...$args);
    }
}
