<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\Reports\InformeSolicitudPdfService;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class InformeSolicitudTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        $this->withoutMiddleware(CajasAuthenticated::class);
    }

    public function test_pdf_valida_tipopc_invalido(): void
    {
        $response = $this->getJson(route('cajas.informe-solicitud.pdf', [
            'ruuid' => 'ruuid-demo-12345',
            'tipopc' => 99,
        ]));

        $response->assertStatus(422);
    }

    public function test_pdf_valida_ruuid_requerido(): void
    {
        $response = $this->getJson(route('cajas.informe-solicitud.pdf', [
            'tipopc' => 1,
        ]));

        $response->assertStatus(422);
    }

    public function test_pdf_retorna_404_cuando_no_existe_solicitud(): void
    {
        $this->mock(InformeSolicitudPdfService::class, function ($mock): void {
            $mock->shouldReceive('generate')
                ->once()
                ->andThrow(new NotFoundHttpException('No se encontró la solicitud con el RUUID y tipo indicados.'));
        });

        $response = $this->getJson(route('cajas.informe-solicitud.pdf', [
            'ruuid' => 'ruuid-inexistente-xyz',
            'tipopc' => 1,
        ]));

        $response->assertStatus(404);
        $response->assertJsonPath('message', 'No se encontró la solicitud con el RUUID y tipo indicados.');
    }

    public function test_pdf_devuelve_application_pdf(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'informe_pdf_');
        file_put_contents($tempFile, '%PDF-1.4 fake content');

        $this->mock(InformeSolicitudPdfService::class, function ($mock) use ($tempFile): void {
            $mock->shouldReceive('generate')
                ->once()
                ->with('1', 'ruuid-ok-12345678')
                ->andReturn([
                    'path' => $tempFile,
                    'filename' => 'informe_solicitud_ruuid-ok-12345678.pdf',
                    'payload' => [],
                ]);
        });

        $response = $this->get(route('cajas.informe-solicitud.pdf', [
            'ruuid' => 'ruuid-ok-12345678',
            'tipopc' => 1,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString(
            'informe_solicitud_ruuid-ok-12345678.pdf',
            (string) $response->headers->get('Content-Disposition')
        );

        @unlink($tempFile);
    }

    public function test_rutas_registradas_y_protegidas(): void
    {
        $this->assertTrue(Route::has('cajas.informe-solicitud.index'));
        $this->assertTrue(Route::has('cajas.informe-solicitud.pdf'));

        $route = Route::getRoutes()->getByName('cajas.informe-solicitud.pdf');
        $this->assertContains('cajas.auth', $route->gatherMiddleware());
    }
}
