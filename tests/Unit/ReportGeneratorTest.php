<?php

namespace Tests\Unit;

use App\Services\Reports\CsvReportStrategy;
use App\Services\Reports\ExcelReportStrategy;
use App\Services\Reports\ReportGenerator;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;
use Mockery as m;

class ReportGeneratorTest extends TestCase
{
    /** @return array{builder: Builder, rows: array<int, object>} */
    private function makeChunkingBuilder(array $rows): array
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('chunk')->andReturnUsing(function ($chunkSize, $callback) use ($rows) {
            // Entregamos todos los rows en un único chunk para simplificar
            $callback(collect($rows));
        });
        return ['builder' => $builder, 'rows' => $rows];
    }

    private function getResponseContent(StreamedResponse $response): string
    {
        ob_start();
        $response->sendContent();
        return ob_get_clean();
    }

    public function test_csv_report_streams_headers_and_rows(): void
    {
        $rows = [
            (object) ['id' => 1, 'name' => 'Alpha', 'price' => 10.5],
            (object) ['id' => 2, 'name' => 'Beta',  'price' => 20.0],
        ];
        $mock = $this->makeChunkingBuilder($rows);
        $columns = [
            'ID' => 'id',
            'Nombre' => 'name',
            'Precio' => fn($r) => number_format($r->price, 2, ',', '.'),
        ];

        $gen = (new ReportGenerator(new CsvReportStrategy()))
            ->for($mock['builder'])
            ->columns($columns)
            ->filename('test.csv')
            ->chunkSize(100);

        $resp = $gen->download();
        $this->assertInstanceOf(StreamedResponse::class, $resp);
        $this->assertStringContainsString('attachment; filename="test.csv"', $resp->headers->get('Content-Disposition'));
        $this->assertStringContainsString('text/csv', $resp->headers->get('Content-Type'));

        $content = $this->getResponseContent($resp);
        // Debe contener headers y 2 filas (precio puede venir entrecomillado por coma decimal)
        $this->assertStringContainsString('ID,Nombre,Precio', $content);
        $this->assertStringContainsString('1,Alpha', $content);
        $this->assertStringContainsString('10,50', $content);
        $this->assertStringContainsString('2,Beta', $content);
        $this->assertStringContainsString('20,00', $content);
    }

    public function test_excel_csv_report_streams_with_excel_headers_and_rows(): void
    {
        $rows = [
            (object) ['code' => 'A1', 'desc' => 'Item A'],
            (object) ['code' => 'B2', 'desc' => 'Item B'],
        ];
        $mock = $this->makeChunkingBuilder($rows);
        $columns = [
            'Código' => 'code',
            'Descripción' => 'desc',
        ];

        $gen = (new ReportGenerator(new ExcelReportStrategy()))
            ->for($mock['builder'])
            ->columns($columns)
            ->filename('excel.xlsx');

        $resp = $gen->download();
        $this->assertInstanceOf(StreamedResponse::class, $resp);
        $this->assertStringContainsString('attachment; filename="excel.xlsx"', $resp->headers->get('Content-Disposition'));
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $resp->headers->get('Content-Type'));

        $content = $this->getResponseContent($resp);
        // Encabezados separados por ';' y filas presentes
        $this->assertStringContainsString("Código;Descripción", $content);
        $this->assertStringContainsString("A1;Item A", $content);
        $this->assertStringContainsString("B2;Item B", $content);
    }
}
