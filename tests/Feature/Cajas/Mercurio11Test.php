<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio11Test extends TestCase
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
        foreach (['cajas.mercurio11.index', 'cajas.mercurio11.store', 'cajas.mercurio11.update', 'cajas.mercurio11.destroy'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_codest_requerido(): void
    {
        $response = $this->from('/cajas/mercurio11/index')
            ->post(route('cajas.mercurio11.store'), [
                'codest' => '',
                'detalle' => 'Motivo de prueba',
            ]);

        $response->assertSessionHasErrors(['codest']);
    }

    public function test_store_valida_codest_tamano(): void
    {
        $response = $this->from('/cajas/mercurio11/index')
            ->post(route('cajas.mercurio11.store'), [
                'codest' => 'ABC',
                'detalle' => 'Motivo de prueba',
            ]);

        $response->assertSessionHasErrors(['codest']);
    }

    public function test_store_valida_detalle_requerido(): void
    {
        $response = $this->from('/cajas/mercurio11/index')
            ->post(route('cajas.mercurio11.store'), [
                'codest' => 'XX',
                'detalle' => '',
            ]);

        $response->assertSessionHasErrors(['detalle']);
    }
}
