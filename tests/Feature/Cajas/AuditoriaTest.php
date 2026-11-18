<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuditoriaTest extends TestCase
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
        foreach (['auditoria', 'auditoria.index', 'auditoria.show', 'auditoria.reporte', 'auditoria.exportar'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_y_alias_apuntan_al_mismo_controlador(): void
    {
        $index = Route::getRoutes()->getByName('auditoria.index');
        $home = Route::getRoutes()->getByName('auditoria');

        $this->assertNotNull($index);
        $this->assertNotNull($home);
        $this->assertSame('index', $index->getActionMethod());
        $this->assertSame($index->getActionMethod(), $home->getActionMethod());
    }

    public function test_show_apunta_al_metodo_show(): void
    {
        $route = Route::getRoutes()->getByName('auditoria.show');

        $this->assertNotNull($route);
        $this->assertSame('show', $route->getActionMethod());
        $this->assertStringContainsString('{tipopc}', $route->uri());
        $this->assertStringContainsString('{id}', $route->uri());
    }
}
