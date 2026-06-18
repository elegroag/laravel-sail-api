<?php

namespace App\Services\Reports;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OportunidadAfiliacionExcelExporter
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public static function stream(array $headers, array $rows, string $filename): StreamedResponse
    {
        return new StreamedResponse(
            static function () use ($headers, $rows): void {
                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();
                $rowIndex = 1;

                foreach ($headers as $i => $head) {
                    $col = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->setCellValue($col.$rowIndex, $head);
                }

                $headerRange = 'A1:'.Coordinate::stringFromColumnIndex(count($headers)).'1';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E1F2'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                $rowIndex++;

                foreach ($rows as $row) {
                    foreach ($row as $i => $value) {
                        $col = Coordinate::stringFromColumnIndex($i + 1);
                        $sheet->setCellValue($col.$rowIndex, $value);
                    }
                    $rowIndex++;
                }

                if ($rowIndex > 2) {
                    $dataRange = 'A2:'.Coordinate::stringFromColumnIndex(count($headers)).($rowIndex - 1);
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                        ],
                    ]);
                }

                for ($c = 1; $c <= count($headers); $c++) {
                    $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
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
    }
}
