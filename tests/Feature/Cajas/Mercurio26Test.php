<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio26Test extends TestCase
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
            'cajas.mercurio26.index',
            'cajas.mercurio26.store',
            'cajas.mercurio26.destroy',
            'cajas.mercurio26.moveUp',
            'cajas.mercurio26.moveDown',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_tipo_requerido(): void
    {
        $response = $this->from('/cajas/mercurio26/index')
            ->post(route('cajas.mercurio26.store'), [
                'tipo' => '',
                'nota' => 'Promo',
            ]);

        $response->assertSessionHasErrors(['tipo']);
    }

    public function test_store_valida_archivo_requerido(): void
    {
        $response = $this->from('/cajas/mercurio26/index')
            ->post(route('cajas.mercurio26.store'), [
                'tipo' => 'F',
                'nota' => 'Promo',
            ]);

        $response->assertSessionHasErrors(['archivo']);
    }

    public function test_store_valida_tipo_invalido(): void
    {
        $response = $this->from('/cajas/mercurio26/index')
            ->post(route('cajas.mercurio26.store'), [
                'tipo' => 'X',
                'nota' => 'Promo',
            ]);

        $response->assertSessionHasErrors(['tipo']);
    }
}
