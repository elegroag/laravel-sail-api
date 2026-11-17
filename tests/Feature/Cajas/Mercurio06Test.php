<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio06Test extends TestCase
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
        $names = [
            'cajas.mercurio06.index',
            'cajas.mercurio06.store',
            'cajas.mercurio06.update',
            'cajas.mercurio06.destroy',
            'cajas.mercurio06.campos.store',
            'cajas.mercurio06.campos.update',
            'cajas.mercurio06.campos.destroy',
        ];

        foreach ($names as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_campos_requeridos(): void
    {
        $response = $this->from('/cajas/mercurio06/index')
            ->post(route('cajas.mercurio06.store'), [
                'tipo' => '',
                'detalle' => '',
            ]);

        $response->assertSessionHasErrors(['tipo', 'detalle']);
    }

    public function test_store_campo_valida_requeridos(): void
    {
        $response = $this->from('/cajas/mercurio06/index?campos_tipo=A')
            ->post(route('cajas.mercurio06.campos.store', ['tipo' => 'A']), [
                'campo' => '',
                'detalle' => '',
                'orden' => '',
            ]);

        $response->assertSessionHasErrors(['campo', 'detalle', 'orden']);
    }
}
