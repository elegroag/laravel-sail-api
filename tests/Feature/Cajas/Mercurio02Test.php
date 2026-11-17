<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio02Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        config(['logging.default' => 'stderr']);

        $this->withoutMiddleware(CajasAuthenticated::class);
    }

    public function test_rutas_requieren_middleware_cajas_auth(): void
    {
        foreach (['cajas.mercurio02.index', 'cajas.mercurio02.store', 'cajas.mercurio02.update'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_campos_requeridos(): void
    {
        $response = $this->from('/cajas/mercurio02/index')
            ->post(route('cajas.mercurio02.store'), [
                'codcaj' => '',
                'nit' => '',
                'razsoc' => '',
            ]);

        $response->assertSessionHasErrors(['codcaj', 'nit', 'razsoc', 'sigla', 'email', 'direccion', 'telefono', 'codciu', 'pagweb']);
    }
}
