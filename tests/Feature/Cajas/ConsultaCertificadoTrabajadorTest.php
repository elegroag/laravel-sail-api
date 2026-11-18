<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ConsultaCertificadoTrabajadorTest extends TestCase
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
            'consulta.certificadoTrabajador',
            'consulta.trabajadoresPorNit',
            'consulta.generarCertificadoTrabajador',
        ] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertContains('cajas.auth', $route->gatherMiddleware(), $name);
        }
    }

    public function test_vista_es_get_y_acciones_son_post(): void
    {
        $view = Route::getRoutes()->getByName('consulta.certificadoTrabajador');
        $trabajadores = Route::getRoutes()->getByName('consulta.trabajadoresPorNit');
        $generar = Route::getRoutes()->getByName('consulta.generarCertificadoTrabajador');

        $this->assertNotNull($view);
        $this->assertNotNull($trabajadores);
        $this->assertNotNull($generar);
        $this->assertContains('GET', $view->methods());
        $this->assertContains('POST', $trabajadores->methods());
        $this->assertContains('POST', $generar->methods());
        $this->assertSame('certificadoTrabajadorView', $view->getActionMethod());
        $this->assertSame('trabajadoresPorNit', $trabajadores->getActionMethod());
        $this->assertSame('certificadoTrabajador', $generar->getActionMethod());
    }
}
