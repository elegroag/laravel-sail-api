<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Mercurio09Test extends TestCase
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
            'cajas.mercurio09.index',
            'cajas.mercurio09.store',
            'cajas.mercurio09.update',
            'cajas.mercurio09.destroy',
            'cajas.mercurio09.documentos',
            'cajas.mercurio09.documentos.trabajador',
            'cajas.mercurio09.documentos.empresa',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_tipopc_requerido(): void
    {
        $response = $this->from('/cajas/mercurio09/index')
            ->post(route('cajas.mercurio09.store'), [
                'tipopc' => '',
                'detalle' => 'Afiliación empresa',
                'dias' => 30,
            ]);

        $response->assertSessionHasErrors(['tipopc']);
    }

    public function test_store_valida_detalle_requerido(): void
    {
        $response = $this->from('/cajas/mercurio09/index')
            ->post(route('cajas.mercurio09.store'), [
                'tipopc' => '9',
                'detalle' => '',
                'dias' => 30,
            ]);

        $response->assertSessionHasErrors(['detalle']);
    }

    public function test_sync_trabajador_valida_coddoc(): void
    {
        $response = $this->from('/cajas/mercurio09/2/documentos')
            ->post(route('cajas.mercurio09.documentos.trabajador', ['tipopc' => '2']), [
                'coddoc' => '',
                'adjunto' => true,
            ]);

        $response->assertSessionHasErrors(['coddoc']);
    }

    public function test_sync_empresa_valida_tipsoc(): void
    {
        $response = $this->from('/cajas/mercurio09/2/documentos?tab=empresa')
            ->post(route('cajas.mercurio09.documentos.empresa', ['tipopc' => '2']), [
                'tipsoc' => '',
                'coddoc' => 1,
                'adjunto' => true,
            ]);

        $response->assertSessionHasErrors(['tipsoc']);
    }
}
