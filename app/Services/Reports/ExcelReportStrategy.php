<?php

namespace App\Services\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Estrategia Excel basada en PhpSpreadsheet (XLSX).
 * Conserva la firma retornando un StreamedResponse transmitiendo el archivo generado.
 */
class ExcelReportStrategy implements ReportFormatStrategy
{
    public function __construct() {}

    public function stream(Builder $query, array $columns, int $chunkSize = 1000): StreamedResponse
    {
        $filename = 'reporte_' . (method_exists(Date::class, 'now') ? Date::now()->format('Ymd_His') : date('Ymd_His')) . '.xlsx';

        return new StreamedResponse(function () use ($query, $columns, $chunkSize) {
            $spreadsheet = new Spreadsheet();

            // Encabezados
            $headers = array_keys($columns);
            $colIndex = 1; // 1-based
            foreach ($headers as $header) {
                $letter = Coordinate::stringFromColumnIndex($colIndex);
                $spreadsheet->getActiveSheet()->setCellValue($letter . '1', $header);
                $colIndex++;
            }

            $rowIndex = 2;
            $query->chunk($chunkSize, function ($rows) use (&$rowIndex, $columns, $spreadsheet) {
                foreach ($rows as $row) {
                    $colIndex = 1;
                    foreach ($columns as $accessor) {
                        if (is_callable($accessor)) {
                            $value = $accessor($row);
                        } else {
                            $value = data_get($row, $accessor);
                        }
                        $letter = Coordinate::stringFromColumnIndex($colIndex);
                        $spreadsheet->getActiveSheet()->setCellValue($letter . $rowIndex, $value);
                        $colIndex++;
                    }
                    $rowIndex++;
                }
            });

            // Auto-ajustar el ancho básico de columnas
            $lastCol = count($columns);
            for ($c = 1; $c <= $lastCol; $c++) {
                $letter = Coordinate::stringFromColumnIndex($c);
                $spreadsheet->getActiveSheet()->getColumnDimension($letter)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            // Deshabilitar fórmulas pre-calculadas para reducir memoria en streaming
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
