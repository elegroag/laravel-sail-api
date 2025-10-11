<?php

namespace App\Services\ReportGenerator\Products;

use App\Services\ReportGenerator\Contracts\IReportProduct;
use Generator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvStreamingProduct implements IReportProduct
{
    private Generator $data;
    private array $headers = []; // Para almacenar los encabezados/columnas

    public function setData(Generator $data): void
    {
        $this->data = $data;
        if ($this->data->valid()) {
            $this->headers = $this->data->current(); // Captura los encabezados
            $this->data->next(); // Avanza el generador a la primera fila de datos
        }
    }

    public function generateContent(): string
    {
        // Este método no es aplicable para streaming, ya que el contenido se escribe al vuelo.
        throw new \BadMethodCallException("The CsvStreamingProduct is designed for streamOutput only.");
    }

    public function streamOutput(string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            $output = fopen('php://output', 'w');

            // 1. Inclusión del BOM UTF-8 para compatibilidad con Excel
            fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // 2. Escritura de encabezados
            if (!empty($this->headers)) {
                fputcsv($output, $this->headers);
            }

            // 3. Escritura de datos: el Generator alimenta fputcsv() en cada iteración
            foreach ($this->data as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, 200, [
            // Configuración de Cabeceras HTTP para descarga CSV
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);

        return $response;
    }
}
