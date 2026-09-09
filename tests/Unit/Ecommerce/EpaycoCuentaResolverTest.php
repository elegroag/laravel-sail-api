<?php

namespace Tests\Unit\Ecommerce;

use App\Exceptions\DebugException;
use App\Models\EpaycoCuenta;
use App\Services\Ecommerce\EpaycoCuentaResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpaycoCuentaResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_encuentra_cuenta_por_p_id_customer(): void
    {
        $cuenta = EpaycoCuenta::create([
            'account' => 'comercio-test',
            'env_mode' => 'development',
            'public_key' => 'pub_test',
            'private_key' => 'priv_test',
            'p_key' => 'pkey_test',
            'p_id_customer' => '1552108',
        ]);

        $found = (new EpaycoCuentaResolver)->findByPIdCustomer('1552108');

        $this->assertTrue($found->is($cuenta));
    }

    public function test_falla_si_no_existe_cuenta(): void
    {
        $this->expectException(DebugException::class);

        (new EpaycoCuentaResolver)->findByPIdCustomer('no-existe');
    }

    public function test_falla_si_p_id_customer_vacio(): void
    {
        $this->expectException(DebugException::class);

        (new EpaycoCuentaResolver)->findByPIdCustomer('   ');
    }
}
