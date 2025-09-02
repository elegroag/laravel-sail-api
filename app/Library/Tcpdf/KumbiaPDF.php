<?php
namespace App\Library\Tcpdf;

use TCPDF;

interface GeneratePdfInterface
{
    public function crear($filename, $titulo, $x_with);
    public function out();
}


class KumbiaPDF extends TCPDF
{

    public static $_titulo;
    public static $x_with;
    public static $backgroundImage;
    public static $footerImage;
    public static $format = 'A4';
    public static $unidad = 'mm';

    public function __construct($titulo = null, $orientation = "P")
    {
        if ($titulo) $this->SetTitle($titulo);
        parent::__construct($orientation, self::$unidad, self::$format, true, 'UTF-8', false);
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetAutoPageBreak(TRUE, 10);
    }

    public static function setInicializa($titulo, $x_with)
    {
        self::$_titulo = $titulo;
        self::$x_with = $x_with;
    }

    public static function setBackgroundImage($image)
    {
        self::$backgroundImage = $image;
    }

    public static function setFooterImage($image)
    {
        self::$footerImage = $image;
    }

    public function Header()
    {
        if (!is_null(self::$backgroundImage)) {
            // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);
            // set bacground image
            $this->SetHeaderMargin(0);

            $this->Image(self::$backgroundImage, 0, 0, 210, null, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();
        }
    }

    public function Footer()
    {
        if (!is_null(self::$footerImage)) {
            $this->setY(-50);
            $this->Image(self::$footerImage, 90, $this->getY(), 30, 20, 'PNG');
        }
    }
}
