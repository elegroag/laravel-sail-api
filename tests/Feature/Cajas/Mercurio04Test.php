<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio04Test extends TestCase
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
            'cajas.mercurio04.index',
            'cajas.mercurio04.store',
            'cajas.mercurio04.update',
            'cajas.mercurio04.destroy',
            'cajas.mercurio04.opciones',
            'cajas.mercurio04.opciones.store',
            'cajas.mercurio04.opciones.destroy',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_codofi_requerido(): void
    {
        $response = $this->from('/cajas/mercurio04/index')
            ->post(route('cajas.mercurio04.store'), [
                'codofi' => '',
                'detalle' => 'Oficina central',
                'principal' => 'N',
                'estado' => 'A',
            ]);

        $response->assertSessionHasErrors(['codofi']);
    }

    public function test_store_valida_detalle_requerido(): void
    {
        $response = $this->from('/cajas/mercurio04/index')
            ->post(route('cajas.mercurio04.store'), [
                'codofi' => '01',
                'detalle' => '',
                'principal' => 'N',
                'estado' => 'A',
            ]);

        $response->assertSessionHasErrors(['detalle']);
    }

    public function test_store_opcion_valida_tipopc(): void
    {
        $response = $this->from('/cajas/mercurio04/01/opciones')
            ->post(route('cajas.mercurio04.opciones.store', ['codofi' => '01']), [
                'tipopc' => '',
                'usuario' => 1,
            ]);

        $response->assertSessionHasErrors(['tipopc']);
    }

    public function test_store_opcion_valida_usuario(): void
    {
        $response = $this->from('/cajas/mercurio04/01/opciones')
            ->post(route('cajas.mercurio04.opciones.store', ['codofi' => '01']), [
                'tipopc' => '2',
                'usuario' => '',
            ]);

        $response->assertSessionHasErrors(['usuario']);
    }
}
