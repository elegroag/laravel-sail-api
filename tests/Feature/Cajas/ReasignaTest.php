<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReasignaTest extends TestCase
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
        foreach (['reasigna', 'reasigna.index', 'reasigna.proceso', 'reasigna.cambiar_usuario'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_y_alias_apuntan_al_mismo_controlador(): void
    {
        $index = Route::getRoutes()->getByName('reasigna.index');
        $home = Route::getRoutes()->getByName('reasigna');

        $this->assertNotNull($index);
        $this->assertNotNull($home);
        $this->assertSame('index', $index->getActionMethod());
        $this->assertSame($index->getActionMethod(), $home->getActionMethod());
    }

    public function test_proceso_y_cambiar_usuario_son_post(): void
    {
        $proceso = Route::getRoutes()->getByName('reasigna.proceso');
        $cambiar = Route::getRoutes()->getByName('reasigna.cambiar_usuario');

        $this->assertNotNull($proceso);
        $this->assertNotNull($cambiar);
        $this->assertContains('POST', $proceso->methods());
        $this->assertContains('POST', $cambiar->methods());
    }
}
