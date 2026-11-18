<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio53Test extends TestCase
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
            'cajas.mercurio53.index',
            'cajas.mercurio53.store',
            'cajas.mercurio53.update',
            'cajas.mercurio53.destroy',
            'cajas.mercurio53.moveUp',
            'cajas.mercurio53.moveDown',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_archivo_requerido(): void
    {
        $response = $this->from('/cajas/mercurio53/index')
            ->post(route('cajas.mercurio53.store'), []);

        $response->assertSessionHasErrors(['archivo']);
    }
}
