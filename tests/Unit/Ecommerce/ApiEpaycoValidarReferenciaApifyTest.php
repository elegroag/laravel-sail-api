<?php

namespace Tests\Unit\Ecommerce;

use App\Services\Api\ApiEpayco;
use Illuminate\Support\Facades\Http;
use ReflectionProperty;
use Tests\TestCase;

class ApiEpaycoValidarReferenciaApifyTest extends TestCase
{
    public function test_consulta_apify_normaliza_transaccion_anidada_en_transaction(): void
    {
        Http::fake([
            '*/login' => Http::response(['token' => $this->jwtConExp()], 200),
            '*/payment/transaction' => Http::response([
                'success' => true,
                'textResponse' => 'Transacción consultada existosamente',
                'data' => [
                    'transaction' => [
                        'refPayco' => 385298450,
                        'invoice' => 'ORD81-1788989346',
                        'amount' => 100,
                        'currency' => 'COP',
                        'bank' => 'BANCO DAVIVIENDA',
                        'response' => 'Aceptada',
                        'status' => 'Aceptada',
                        'autorizacion' => '640808494',
                        'transactionId' => '385298450178898938',
                        'date' => '2026-09-09 16:29:49',
                        'codeResponse' => 1,
                        'codTransactionState' => 1,
                        'responseReasonText' => 'Aprobada',
                        'franchise' => 'PSE',
                        'signature' => 'sig-nested',
                    ],
                ],
            ], 200),
        ]);

        $resultado = $this->apiConLlaves()->validarReferenciaApify('385298450');

        $this->assertTrue($resultado['success']);
        $this->assertTrue($resultado['data']['aprobado']);
        $this->assertSame(1, $resultado['data']['cod_estado']);
        $this->assertSame('Aceptada', $resultado['data']['respuesta']);
        $this->assertSame('Aprobada', $resultado['data']['motivo']);
        $this->assertSame(385298450, $resultado['data']['ref_payco']);
        $this->assertSame('640808494', $resultado['data']['x_approval_code']);
        $this->assertSame('PSE', $resultado['data']['x_franchise']);
    }

    public function test_consulta_apify_normaliza_transaccion_aceptada(): void
    {
        Http::fake([
            '*/login' => Http::response(['token' => $this->jwtConExp()], 200),
            '*/payment/transaction' => Http::response([
                'success' => true,
                'titleResponse' => 'Correcto',
                'textResponse' => 'Transacción consultada existosamente',
                'lastAction' => 'Consultar Transaccion',
                'data' => [
                    'refPayco' => 385298450,
                    'invoice' => 'INV-81',
                    'amount' => '15000.00',
                    'currency' => 'COP',
                    'bank' => 'BANCO TEST',
                    'response' => 'Aceptada',
                    'status' => 'Aceptada',
                    'autorizacion' => 'ABC123',
                    'transactionId' => 'TX-999',
                    'date' => '2026-09-09 12:00:00',
                    'codeResponse' => 1,
                    'codTransactionState' => '1',
                    'responseReasonText' => 'Aprobada',
                    'franchise' => 'VS',
                    'cardNumber' => '****1111',
                    'signature' => 'sig-test',
                ],
            ], 200),
        ]);

        $resultado = $this->apiConLlaves()->validarReferenciaApify('385298450');

        $this->assertTrue($resultado['success']);
        $this->assertTrue($resultado['data']['aprobado']);
        $this->assertSame(1, $resultado['data']['cod_estado']);
        $this->assertSame('Aceptada', $resultado['data']['respuesta']);
        $this->assertSame('Aprobada', $resultado['data']['motivo']);
        $this->assertSame(385298450, $resultado['data']['ref_payco']);
        $this->assertSame('apify', $resultado['data']['origen_consulta']);
        $this->assertSame('TX-999', $resultado['data']['x_transaction_id']);
    }

    public function test_consulta_apify_transaccion_no_existe(): void
    {
        Http::fake([
            '*/login' => Http::response(['token' => $this->jwtConExp()], 200),
            '*/payment/transaction' => Http::response([
                'success' => false,
                'titleResponse' => 'Error',
                'textResponse' => 'Transacción no existe',
                'data' => ['error' => []],
            ], 200),
        ]);

        $resultado = $this->apiConLlaves()->validarReferenciaApify('999999');

        $this->assertFalse($resultado['success']);
        $this->assertSame('Transacción no existe', $resultado['errors']);
    }

    public function test_consulta_apify_sin_credenciales(): void
    {
        $resultado = (new ApiEpayco)->validarReferenciaApify('385298450');

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('autenticar', $resultado['errors']);
    }

    public function test_force_approved_omite_apify_y_retorna_aprobado(): void
    {
        config([
            'app.epayco.force_approved' => true,
            'app.env' => 'local',
            'app.app_mode' => 'development',
        ]);

        Http::fake();

        $resultado = (new ApiEpayco)->validarReferenciaApify('QA-FORCE-001');

        $this->assertTrue($resultado['success']);
        $this->assertTrue($resultado['data']['aprobado']);
        $this->assertSame(1, $resultado['data']['cod_estado']);
        $this->assertSame('Aceptada (FORCE_APPROVED)', $resultado['data']['respuesta']);
        $this->assertSame('Simulado por EPAYCO_FORCE_APPROVED', $resultado['data']['motivo']);
        $this->assertSame('QA-FORCE-001', $resultado['data']['ref_payco']);
        $this->assertSame('force_approved', $resultado['data']['origen_consulta']);
        Http::assertNothingSent();
    }

    public function test_force_approved_no_aplica_en_production(): void
    {
        config([
            'app.epayco.force_approved' => true,
            'app.env' => 'production',
            'app.app_mode' => 'production',
        ]);

        $resultado = (new ApiEpayco)->validarReferenciaApify('385298450');

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('autenticar', $resultado['errors']);
    }

    private function apiConLlaves(): ApiEpayco
    {
        $api = new ApiEpayco;

        $public = new ReflectionProperty(ApiEpayco::class, 'publicKey');
        $public->setAccessible(true);
        $public->setValue($api, 'pub_test');

        $private = new ReflectionProperty(ApiEpayco::class, 'privateKey');
        $private->setAccessible(true);
        $private->setValue($api, 'priv_test');

        $mode = new ReflectionProperty(ApiEpayco::class, 'mode');
        $mode->setAccessible(true);
        $mode->setValue($api, 'production');

        return $api;
    }

    private function jwtConExp(): string
    {
        $header = rtrim(strtr(base64_encode('{"alg":"none"}'), '+/', '-_'), '=');
        $payload = rtrim(strtr(base64_encode(json_encode([
            'exp' => time() + 600,
        ])), '+/', '-_'), '=');

        return $header.'.'.$payload.'.sig';
    }
}
