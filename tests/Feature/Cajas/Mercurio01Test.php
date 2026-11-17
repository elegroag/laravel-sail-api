<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio01Test extends TestCase
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
        foreach (['cajas.mercurio01.index', 'cajas.mercurio01.store', 'cajas.mercurio01.update'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_codapl_requerido(): void
    {
        $response = $this->from('/cajas/mercurio01/index')
            ->post(route('cajas.mercurio01.store'), [
                'codapl' => '',
                'email' => 'demo@example.com',
                'path' => '/tmp',
            ]);

        $response->assertSessionHasErrors(['codapl']);
    }

    public function test_store_valida_path_requerido(): void
    {
        $response = $this->from('/cajas/mercurio01/index')
            ->post(route('cajas.mercurio01.store'), [
                'codapl' => 'CA',
                'email' => 'demo@example.com',
                'path' => '',
            ]);

        $response->assertSessionHasErrors(['path']);
    }
}
