<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio57Test extends TestCase
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
        foreach ([
            'cajas.mercurio57.index',
            'cajas.mercurio57.store',
            'cajas.mercurio57.update',
            'cajas.mercurio57.destroy',
            'cajas.mercurio57.moveUp',
            'cajas.mercurio57.moveDown',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_estado_y_archivo(): void
    {
        $response = $this->from('/cajas/mercurio57/index')
            ->post(route('cajas.mercurio57.store'), [
                'estado' => '',
            ]);

        $response->assertSessionHasErrors(['estado', 'archivo']);
    }

    public function test_store_valida_estado_invalido(): void
    {
        $response = $this->from('/cajas/mercurio57/index')
            ->post(route('cajas.mercurio57.store'), [
                'estado' => 'X',
            ]);

        $response->assertSessionHasErrors(['estado']);
    }
}
