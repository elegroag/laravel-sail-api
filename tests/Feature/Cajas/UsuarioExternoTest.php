<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UsuarioExternoTest extends TestCase
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
            'cajas.usuario.index',
            'cajas.usuario.show',
            'cajas.usuario.edit',
            'cajas.usuario.update',
            'cajas.usuario.guardar',
            'cajas.usuario.borrar',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_index_apunta_al_metodo_index(): void
    {
        $route = Route::getRoutes()->getByName('cajas.usuario.index');

        $this->assertNotNull($route);
        $this->assertSame('index', $route->getActionMethod());
        $this->assertStringContainsString('usuario/index', $route->uri());
    }

    public function test_show_apunta_al_metodo_show(): void
    {
        $route = Route::getRoutes()->getByName('cajas.usuario.show');

        $this->assertNotNull($route);
        $this->assertSame('show', $route->getActionMethod());
        $this->assertStringContainsString('{documento}', $route->uri());
        $this->assertStringContainsString('{tipo}', $route->uri());
        $this->assertStringContainsString('{coddoc}', $route->uri());
    }

    public function test_edit_apunta_al_metodo_edit(): void
    {
        $route = Route::getRoutes()->getByName('cajas.usuario.edit');

        $this->assertNotNull($route);
        $this->assertSame('edit', $route->getActionMethod());
        $this->assertStringContainsString('/edit', $route->uri());
    }

    public function test_update_apunta_al_metodo_update(): void
    {
        $route = Route::getRoutes()->getByName('cajas.usuario.update');

        $this->assertNotNull($route);
        $this->assertSame('update', $route->getActionMethod());
        $this->assertContains('PUT', $route->methods());
    }
}
