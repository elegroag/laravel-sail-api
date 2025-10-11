<?php

namespace App\Services\ReportGenerator\Products;

use App\Services\ReportGenerator\Contracts\IReportProduct;
use Generator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DompdfProduct implements IReportProduct
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
        throw new \BadMethodCallException('DompdfProduct is designed for streamOutput only.');
    }

    public function streamOutput(string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($filename) {
            // Crear documento PDF con TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('ReportService');
            $pdf->SetAuthor('ReportService');
            $pdf->SetTitle($filename);
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->AddPage();

            // Construir HTML simple con tabla
            $html = '<style>table{border-collapse:collapse;width:100%;font-size:10pt}th,td{border:1px solid #333;padding:4px}th{background:#f0f0f0}</style>';
            $html .= '<table>';

            if (!empty($this->headers)) {
                $html .= '<thead><tr>';
                foreach ($this->headers as $head) {
                    $html .= '<th>' . htmlspecialchars((string) $head) . '</th>';
                }
                $html .= '</tr></thead>';
            }

            $html .= '<tbody>';
            foreach ($this->data as $row) {
                $html .= '<tr>';
                foreach ($row as $value) {
                    $html .= '<td>' . htmlspecialchars((string) $value) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Salida al buffer
            echo $pdf->Output($filename, 'S');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);

        return $response;
    }
}
