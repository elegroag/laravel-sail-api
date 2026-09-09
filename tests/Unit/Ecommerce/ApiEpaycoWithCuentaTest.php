<?php

namespace Tests\Unit\Ecommerce;

use App\Models\EpaycoCuenta;
use App\Services\Api\ApiEpayco;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEpaycoWithCuentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_with_cuenta_fija_llaves_y_modo(): void
    {
        $cuenta = EpaycoCuenta::create([
            'account' => 'comercio-prod',
            'env_mode' => 'production',
            'public_key' => 'pub_prod',
            'private_key' => 'priv_prod',
            'p_key' => 'pkey_prod',
            'p_id_customer' => '999',
        ]);

        $api = (new ApiEpayco)->withCuenta($cuenta);

        $this->assertSame('pub_prod', $api->getPublicKey());
        $this->assertSame('production', $api->getMode());
        $this->assertFalse($api->isTestMode());
    }
}
