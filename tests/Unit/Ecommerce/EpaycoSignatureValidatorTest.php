<?php

namespace Tests\Unit\Ecommerce;

use App\Services\Ecommerce\EpaycoSignatureValidator;
use PHPUnit\Framework\TestCase;

class EpaycoSignatureValidatorTest extends TestCase
{
    public function test_valida_firma_oficial_epayco(): void
    {
        $customerId = '123456';
        $pKey = 'secretpkey';
        $data = [
            'x_ref_payco' => 'ref001',
            'x_transaction_id' => 'tx001',
            'x_amount' => '15000',
            'x_currency_code' => 'COP',
        ];

        $expected = hash('sha256', '123456^secretpkey^ref001^tx001^15000^COP');
        $data['x_signature'] = $expected;

        $validator = new EpaycoSignatureValidator($customerId, $pKey);

        $this->assertTrue($validator->credentialsConfigured());
        $this->assertSame($expected, $validator->expectedSignature($data));
        $this->assertTrue($validator->isValid($data));
    }

    public function test_rechaza_firma_alterada(): void
    {
        $validator = new EpaycoSignatureValidator('123456', 'secretpkey');
        $data = [
            'x_ref_payco' => 'ref001',
            'x_transaction_id' => 'tx001',
            'x_amount' => '15000',
            'x_currency_code' => 'COP',
            'x_signature' => 'firma-invalida',
        ];

        $this->assertFalse($validator->isValid($data));
    }

    public function test_sin_credenciales_no_valida(): void
    {
        $validator = new EpaycoSignatureValidator('', '');
        $this->assertFalse($validator->credentialsConfigured());
        $this->assertFalse($validator->isValid([
            'x_ref_payco' => 'a',
            'x_transaction_id' => 'b',
            'x_amount' => '1',
            'x_currency_code' => 'COP',
            'x_signature' => 'x',
        ]));
    }
}
