<?php

namespace Tests\Feature\Services\ReportGenerator;

use App\Models\Mercurio30;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que el endpoint de generación de reportes CSV funciona correctamente.
     *
     * @return void
     */
    public function test_can_download_a_csv_report_for_mercurio30(): void
    {
        // 1. Preparación (Arrange)
        // Crear datos de prueba. Usamos factory para crear dos registros.
        Mercurio30::factory()->create([
            'razsoc' => 'Usuario de Prueba Uno',
            'email' => 'uno@test.com',
        ]);
        Mercurio30::factory()->create([
            'razsoc' => 'Usuario de Prueba Dos',
            'email' => 'dos@test.com',
        ]);

        // 2. Actuación (Act)
        // Llamar al endpoint para descargar el reporte en formato CSV.
        $response = $this->get(route('api.reports.download', ['format' => 'csv']));

        // 3. Aserción (Assert)
        // Verificar que la respuesta sea exitosa.
        $response->assertStatus(200);

        // Verificar las cabeceras HTTP correctas para una descarga de CSV.
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader(
            'Content-Disposition',
            fn($header) =>
            str_starts_with($header, 'attachment; filename="mercurio30_csv_')
        );

        // Verificar el contenido del CSV.
        $content = $response->getContent();
        $columns = Schema::getColumnListing('mercurio30');
        $expectedCsvHeader = '"' . implode('","', $columns) . '"';

        // Verificar que el BOM de UTF-8 esté presente.
        $this->assertStringStartsWith(chr(0xEF) . chr(0xBB) . chr(0xBF), $content);

        // Verificar que los encabezados y los datos de prueba están en el contenido.
        $this->assertStringContainsString(implode(',', $columns), $content);
        $this->assertStringContainsString('Usuario de Prueba Uno', $content);
        $this->assertStringContainsString('uno@test.com', $content);
        $this->assertStringContainsString('Usuario de Prueba Dos', $content);
        $this->assertStringContainsString('dos@test.com', $content);
    }

    /**
     * Prueba que se lanza una excepción para formatos no soportados.
     *
     * @return void
     */
    public function test_throws_exception_for_unsupported_format(): void
    {
        // Desactivar el manejo de excepciones de Laravel para que la prueba capture el error.
        $this->withoutExceptionHandling();

        // Esperar que se lance una InvalidArgumentException.
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Formato de reporte no soportado: xyz');

        // Llamar al endpoint con un formato inválido.
        $this->get(route('api.reports.download', ['format' => 'xyz']));
    }
}
