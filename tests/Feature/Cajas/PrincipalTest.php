<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PrincipalTest extends TestCase
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

    public function test_rutas_principales_requieren_middleware_cajas_auth(): void
    {
        foreach (['cajas.principal', 'cajas.principal.index', 'cajas.dashboard'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_y_alias_apuntan_al_mismo_controlador(): void
    {
        $index = Route::getRoutes()->getByName('cajas.principal.index');
        $home = Route::getRoutes()->getByName('cajas.principal');

        $this->assertNotNull($index);
        $this->assertNotNull($home);
        $this->assertSame($index->getActionMethod(), $home->getActionMethod());
        $this->assertSame('index', $index->getActionMethod());
    }

    public function test_dashboard_apunta_al_metodo_dashboard(): void
    {
        $route = Route::getRoutes()->getByName('cajas.dashboard');

        $this->assertNotNull($route);
        $this->assertSame('dashboard', $route->getActionMethod());
    }
}
