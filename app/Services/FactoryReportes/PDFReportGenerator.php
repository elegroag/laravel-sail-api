<?php
require_service('FactoryReportes/ReportGenerator');
require_service('FactoryReportes/Tpdf');

class PDFReportGenerator implements ReportGenerator
{

    public static $pdf;
    public static $x_with = 10;
    public static $xr_with = 8;
    public static $y_max = 170;
    public static $orientation = 'L';
    private $filepath;
    private $file;
    private $title;

    public function initializa()
    {
        $this->filepath = Core::getInitialPath() . "public/temp/{$this->file}.pdf";
        Tpdf::setInicializa(self::$orientation, $this->title, ((self::$orientation == 'L') ? 15 : 10));
        self::$pdf = new Tpdf();
        self::$pdf->SetMargins(((self::$orientation == 'L') ? 8 : self::$x_with), ((self::$orientation == 'L') ? 20 : 30), self::$xr_with);
        self::$pdf->AddPage();
        self::$y_max = ((self::$orientation == 'L') ? 170 : 250);
    }

    public function generateReport($titulo, $file, $columns)
    {
        // Generar reporte en PDF con las condiciones aplicadas a los trabajadores
        $this->title = $titulo;
        $this->file = $file;
        $this->initializa();
    }

    /**
     * addLine function 
     * @return void 
     */
    public function addLine($data, $fsize = 8)
    {
        self::$pdf->SetFont('helvetica', '', $fsize);
        self::$pdf->Ln();
        self::$pdf->SetX(self::$x_with);
        $a = 0;
        while ($a < count($data)) {
            self::$pdf->Cell($data[$a][1], $data[$a][2], $data[$a][0], 1, 0, $data[$a][3], 0);
            $a++;
        }
        if (self::$pdf->GetY() >= self::$y_max) {
            self::$pdf->AddPage();
            self::$pdf->SetAutoPageBreak(TRUE, 0);
            self::$pdf->setPageMark();
        }
    }

    /**
     * addHeader function
     * @return void
     */
    public function addHeader($headers, $subtitle, $fsize = 9)
    {
        self::$pdf->SetFont('helvetica', '', $fsize);
        self::$pdf->Ln();

        if ($subtitle != '') {
            self::$pdf->Ln();
            self::$pdf->SetX(self::$x_with);
            self::$pdf->Cell(150, 5, $subtitle, 0, 1, 'L', 0);
            self::$pdf->Ln();
        }
        self::$pdf->SetX(self::$x_with);
        $a = 0;

        self::$pdf->SetFillColor(192, 233, 178);
        while ($a < count($headers)) {
            self::$pdf->MultiCell($headers[$a][1], $headers[$a][2], $headers[$a][0], 1, 'L', 1, 0, '', '', true, 0, false, true, 4, 'M');
            $a++;
        }

        if (self::$pdf->GetY() >= self::$y_max) {
            self::$pdf->AddPage();
            self::$pdf->SetAutoPageBreak(TRUE, 0);
            self::$pdf->setPageMark();
        }
    }

    public function addParrafo($_fields, $fsize = 10)
    {
        foreach ($_fields as $field) {
            self::$pdf->SetTextColor(1);
            self::$pdf->SetFont('helvetica', '', $fsize);
            self::$pdf->SetX(self::$x_with);
            if (
                strlen($field) > 120 ||
                strlen(strstr($field, 'style')) > 0 ||
                strlen(strstr($field, '<b>')) > 0 ||
                strlen(strstr($field, '<p')) > 0
            ) {
                self::$pdf->writeHTML($field, true, false, true, true, 'left');
            } else {
                self::$pdf->Cell(self::$x_with, 5, $field, 0, 0, '', 0, '');
                self::$pdf->Ln();
            }
        }
    }

    public function addHtml($html)
    {
        self::$pdf->SetX(self::$x_with);
        self::$pdf->writeHTML($html, true, false, true, true, 'left');
    }

    /**
     * outFile function
     * @return void
     */
    public function outFile()
    {
        self::$pdf->Output($this->filepath, 'F');
        return $this->filepath;
    }
}
