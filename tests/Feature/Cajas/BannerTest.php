<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BannerTest extends TestCase
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
        foreach (['cajas.banners.index', 'cajas.banners.store', 'cajas.banners.update', 'cajas.banners.destroy'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_store_valida_estado_requerido(): void
    {
        $response = $this->from('/cajas/banners/index')
            ->post(route('cajas.banners.store'), [
                'estado' => '',
                'fecha_inicia' => '2026-01-01',
                'fecha_finaliza' => '2026-01-31',
            ]);

        $response->assertSessionHasErrors(['estado']);
    }

    public function test_store_valida_fechas_requeridas(): void
    {
        $response = $this->from('/cajas/banners/index')
            ->post(route('cajas.banners.store'), [
                'estado' => 'A',
                'fecha_inicia' => '',
                'fecha_finaliza' => '',
            ]);

        $response->assertSessionHasErrors(['fecha_inicia', 'fecha_finaliza']);
    }

    public function test_store_valida_fecha_final_posterior(): void
    {
        $response = $this->from('/cajas/banners/index')
            ->post(route('cajas.banners.store'), [
                'estado' => 'A',
                'fecha_inicia' => '2026-02-01',
                'fecha_finaliza' => '2026-01-01',
            ]);

        $response->assertSessionHasErrors(['fecha_finaliza']);
    }
}
