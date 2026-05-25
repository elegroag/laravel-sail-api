<?php

namespace App\Services\ReportGenerator\Products;

use App\Services\ReportGenerator\Contracts\IReportProduct;
use Generator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OptimizedXlsxProduct implements IReportProduct
{
    private Generator $data;

    private array $headers = [];

    private array $rows = [];

    /**
     * Recibe arrays planos — útil para reportes que ya tienen datos en memoria.
     * Evita el problema de "Cannot rewind a generator that was already run".
     *
     * @param  array  $headers
     * @param  array  $rows
     * @param  string $filename
     * @return StreamedResponse
     */
    public static function streamFromArray(array $headers, array $rows, string $filename): StreamedResponse
    {
        $response = new StreamedResponse(
            static function () use ($headers, $rows) {
                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();
                $rowIndex = 1;

                if (! empty($headers)) {
                    foreach ($headers as $i => $head) {
                        $col = Coordinate::stringFromColumnIndex($i + 1);
                        $sheet->setCellValue($col.$rowIndex, $head);
                    }
                    $rowIndex++;
                }

                foreach ($rows as $row) {
                    foreach ($row as $i => $value) {
                        $col = Coordinate::stringFromColumnIndex($i + 1);
                        $sheet->setCellValue($col.$rowIndex, $value);
                    }
                    $rowIndex++;
                }

                if (! empty($headers)) {
                    $colCount = count($headers);
                    for ($c = 1; $c <= $colCount; $c++) {
                        $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
                    }
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ]
        );

        return $response;
    }

    public function setData(Generator $data): void
    {
        // Legacy: consumir Generator a arrays (para otros reportes que usan Generator)
        $this->data = $data;

        if (! $data->valid()) {
            return;
        }

        $this->headers = $data->current();
        $data->next();

        foreach ($data as $row) {
            $this->rows[] = $row;
        }
    }

    public function generateContent(): string
    {
        throw new \BadMethodCallException('OptimizedXlsxProduct is not yet implemented.');
    }

    public function streamOutput(string $filename): StreamedResponse
    {
        return static::streamFromArray($this->headers, $this->rows, $filename);
    }
}
