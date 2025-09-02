<?php
Core::importLibrary("tcpdf", "TCPDF");

class Tpdf extends TCPDF
{
    public static $titulo = '';
    public static $tipoHoja = 'Legal';
    public static $unidades = 'mm';
    public static $pX = 15;
    public static $orientationHoja = 'P';

    public function __construct()
    {
        $this->SetTitle(self::$titulo);
        parent::__construct(self::$orientationHoja, self::$unidades, self::$tipoHoja);
        $this->SetAutoPageBreak(TRUE, 0);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(5);
    }

    public static function setInicializa($orientationHoja, $titulo, $pX)
    {
        self::$orientationHoja = $orientationHoja;
        self::$titulo = $titulo;
        self::$pX = $pX;
    }

    public function Header()
    {
        $this->SetY(0);
        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->AutoPageBreak;
        $this->SetAutoPageBreak(false, 0);
        if (self::$orientationHoja == 'P') {
            $img_file = 'public/img/membrete_oficios_cobros.jpg';
            $this->Image($img_file, 0, 0, 215, 298, '', '', '', false, 300, '', false, false, 0);
        } else {
            $img_file = 'public/img/membrete.jpg';
            $this->Image($img_file, 0, 0, 298, 215, '', '', '', false, 300, '', false, false, 0);
        }
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        $this->setPageMark();

        if (strlen(self::$titulo) > 0) {
            $this->SetX(self::$pX);
            $this->SetY(8);
            $this->SetTextColor(0);
            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(self::$pX, 5, utf8_decode(self::$titulo), 0, 0, '', 0, '');
        }
    }

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
