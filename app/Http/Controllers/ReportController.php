<?php

namespace App\Http\Controllers;

use App\Models\Mercurio30;
use App\Services\ReportGenerator\ReportService;
use Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    /**
     * Extrae datos del modelo Mercurio30 de forma eficiente usando un generador.
     */
    private function mercurio30DataExtractor(): Generator
    {
        // Obtener los nombres de las columnas dinámicamente
        $columns = Schema::getColumnListing('mercurio30');

        // 1. Enviar los encabezados
        yield $columns;

        // 2. Utilizar cursor() para una extracción de datos con bajo consumo de memoria
        foreach (Mercurio30::cursor() as $record) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $record->{$column};
            }
            // Enviar cada fila de datos
            yield $rowData;
        }
    }

    /**
     * Genera y descarga un reporte en el formato especificado.
     *
     * @param string $format
     * @param ReportService $reportService (Inyección automática de dependencias)
     * @return void
     */
    public function downloadReport(string $format, ReportService $reportService)
    {
        $filename = "mercurio30_{$format}_" . date('Ymd_His') . ".{$format}";
        $dataGenerator = $this->mercurio30DataExtractor();

        // El servicio se encarga de todo el flujo Abstract Factory/Generators
        $reportService->generateAndStream($format, $dataGenerator, $filename);
    }
}
