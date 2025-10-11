<?php

namespace App\Services\ReportGenerator\Products;

use App\Services\ReportGenerator\Contracts\IReportProduct;
use Generator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class OptimizedXlsxProduct implements IReportProduct
{
    private Generator $data;
    private array $headers = [];

    public function setData(Generator $data): void
    {
        $this->data = $data;
        if ($this->data->valid()) {
            $this->headers = $this->data->current();
            $this->data->next();
        }
    }

    public function generateContent(): string
    {
        throw new \BadMethodCallException('OptimizedXlsxProduct is not yet implemented.');
    }

    public function streamOutput(string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($filename) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $rowIndex = 1;
            // Escribir encabezados si existen
            if (!empty($this->headers)) {
                foreach ($this->headers as $i => $head) {
                    $col = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->setCellValue($col . $rowIndex, $head);
                }
                $rowIndex++;
            }

            // Escribir filas de datos desde el Generator
            foreach ($this->data as $row) {
                foreach ($row as $i => $value) {
                    $col = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->setCellValue($col . $rowIndex, $value);
                }
                $rowIndex++;
            }

            // Auto-ajustar ancho de columnas para headers
            if (!empty($this->headers)) {
                $colCount = count($this->headers);
                for ($c = 1; $c <= $colCount; $c++) {
                    $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
                }
            }

            // Enviar a la salida estándar
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            // Liberar memoria
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);

        return $response;
    }
}
