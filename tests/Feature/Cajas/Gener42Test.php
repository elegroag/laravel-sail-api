<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Gener42Test extends TestCase
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
        foreach (['cajas.gener42.index', 'cajas.gener42.assign', 'cajas.gener42.revoke'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_assign_valida_usuario_requerido(): void
    {
        $response = $this->from('/cajas/gener42/index')
            ->post(route('cajas.gener42.assign'), [
                'usuario' => '',
                'permisos' => ['ABCDE'],
            ]);

        $response->assertSessionHasErrors(['usuario']);
    }

    public function test_assign_valida_permisos_requeridos(): void
    {
        $response = $this->from('/cajas/gener42/index')
            ->post(route('cajas.gener42.assign'), [
                'usuario' => 1,
                'permisos' => [],
            ]);

        $response->assertSessionHasErrors(['permisos']);
    }

    public function test_revoke_valida_permisos_requeridos(): void
    {
        $response = $this->from('/cajas/gener42/index')
            ->post(route('cajas.gener42.revoke'), [
                'usuario' => 1,
                'permisos' => [],
            ]);

        $response->assertSessionHasErrors(['permisos']);
    }
}
