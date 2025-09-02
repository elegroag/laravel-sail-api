<?php
Core::importLibrary('DocumentoAdapter', 'Formularios');

class FormularioEmpresa extends DocumentoAdapter
{

    private $empresa;

    /**
     * main function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('empresa')) {
            throw new DebugException("Error la empresa no esté disponible", 501);
        }
        $this->empresa = $this->request->getParam('empresa');
        $this->pdf->SetTitle("Formulario afiliación de empresa {$this->empresa->getRepleg()}, COMFACA");
        $this->pdf->SetAuthor("{$this->empresa->getPriape()} {$this->empresa->getSegape()} {$this->empresa->getPrinom()} {$this->empresa->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Formulario de afiliación a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');
        $this->bloqueEmpresa();
        $selloFirma = Core::getInitialPath() . 'public/docs/sello-firma.png';
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
    }

    /**
     * bloqueEmpresa function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $this->empresa
     * @param TCPDF $this->pdf
     * @return void
     */
    function bloqueEmpresa()
    {
        $_codciu = ParamsEmpresa::getCiudades();
        $_codzon = ParamsEmpresa::getZonas();
        $_codact = ParamsEmpresa::getActividades();
        $_coddep = ParamsEmpresa::getDepartamentos();

        $today = new Date();
        if ($this->empresa->getCiupri() == NULL) {
            $this->empresa->setCiupri($this->empresa->getCodciu());
        }

        $this->pdf->SetXY(162, 43);
        $this->pdf->Cell(12, 4, $today->getDay(), 0, 0, 'C');
        $this->pdf->Cell(12, 4, $today->getMonth(), 0, 0, 'C');
        $this->pdf->Cell(15, 4, $today->getYear(), 0, 0, 'C');

        $cc = "";
        $ti = "";
        $rc = "";
        $ce = "";
        $nuip = "";
        $p = "";
        $nit = "";
        $cd = "";
        ($this->empresa->getTipdoc() == 1) ? $cc = "X" : "";
        ($this->empresa->getTipdoc() == 2) ? $ti = "X" : "";
        ($this->empresa->getTipdoc() == 3) ? $nit = "X" : "";
        ($this->empresa->getTipdoc() == 4) ? $ce = "X" : "";
        ($this->empresa->getTipdoc() == 6) ? $p = "X" : "";
        ($this->empresa->getTipdoc() == 7) ? $rc = "X" : "";
        ($this->empresa->getTipdoc() == 8) ? $cd = "X" : ""; //verificar si es el de permoso especial

        $natural = "";
        $juridica = "";
        $oficial = "";
        $privada = "";
        $mixta = "";
        ($this->empresa->getTipsoc() == 06) ? $natural = "X" : "";
        ($this->empresa->getTipsoc() != 06) ? $juridica = "X" : "";
        ($this->empresa->getTipemp() == 'O') ? $oficial = "X" : "";
        ($this->empresa->getTipemp() == 'P') ? $privada = "X" : "";
        ($this->empresa->getTipemp() == 'M') ? $mixta = "X" : "";

        $this->pdf->SetXY(32, 61);
        $this->pdf->Cell(8, 5, '' . $cc, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $ti, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $rc, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $ce, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $nuip, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $p, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $nit, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $cd, 0, 0, 'C');
        $this->pdf->Cell(90, 5, $this->empresa->getNit(), 0, 0, 'L');
        $this->pdf->Cell(15, 5, $this->empresa->getDigver(), 0, 0, 'C');

        $this->pdf->SetXY(8, 71);
        $this->pdf->Cell(195, 5, Tag::capitalize($this->empresa->getRazsoc()), 0, 0, 'L');

        /**
         * linea x
         */
        $this->pdf->SetFont('helvetica', '', 7);
        $this->pdf->SetXY(10, 79);
        if ($this->empresa->getDirpri() == NULL) {
            if (!(strrchr($this->empresa->getDireccion(), "BRR") == false)) {
                $ext = explode("BRR", $this->empresa->getDireccion());
                $this->pdf->Cell(70, 5, $ext[0], 0, 0, 'L');
                $this->pdf->Cell(50, 5, ((count($ext) == 1) ? $ext[0] : $ext[1]), 0, 0, 'L');
            } else {
                $this->pdf->Cell(70, 5, $this->empresa->getDireccion(), 0, 0, 'L');
                $this->pdf->Cell(50, 5, 'BRR', 0, 0, 'L');
            }
        } else {
            if (!(strrchr($this->empresa->getDirpri(), "BRR") == false)) {
                $ext = explode("BRR", $this->empresa->getDirpri());
                $this->pdf->Cell(70, 5, $ext[0], 0, 0, 'L');
                $this->pdf->Cell(50, 5, ((count($ext) == 1) ? $ext[0] : $ext[1]), 0, 0, 'L');
            } else {
                $this->pdf->Cell(70, 5, $this->empresa->getDirpri(), 0, 0, 'L');
                $this->pdf->Cell(50, 5, 'BRR', 0, 0, 'L');
            }
        }
        $this->pdf->Cell(33, 5, @$_codciu[$this->empresa->getCiupri()], 0, 0, 'L');

        if ($this->empresa->getCiupri() == NULL) {
            $this->pdf->Cell(33, 5, "NINGUNO", 0, 0, 'L');
        } else {
            $this->pdf->Cell(33, 5, @$_coddep[substr($this->empresa->getCiupri(), 0, 2)], 0, 0, 'L');
        }

        /**
         * linea x
         */
        $this->pdf->SetXY(9, 89);
        if (!(strrchr($this->empresa->getDireccion(), "BRR") == false)) {
            $ext = explode("BRR", $this->empresa->getDireccion());
            $this->pdf->Cell(70, 5, $ext[0], 0, 0, 'L');
            $this->pdf->Cell(50, 5, ((count($ext) == 1) ? $ext[0] : $ext[1]), 0, 0, 'L');
        } else {
            $this->pdf->Cell(70, 5, $this->empresa->getDireccion(), 0, 0, 'L');
            $this->pdf->Cell(50, 5, 'BRR', 0, 0, 'L');
        }

        $this->pdf->Cell(33, 5, @$_codciu[$this->empresa->getCodciu()], 0, 0, 'L');

        if ($this->empresa->getCodciu() == NULL) {
            $this->pdf->Cell(33, 5, $_coddep[substr($this->empresa->getCodciu(), 0, 2)], 0, 0, 'L');
        } else {
            $this->pdf->Cell(33, 5, $_coddep['18'], 0, 0, 'L');
        }

        /**
         * linea x
         */
        $this->pdf->SetXY(9, 97);
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->Cell(38, 5, $this->empresa->getTelefono(), 0, 0, 'L');
        $this->pdf->Cell(42, 5, $this->empresa->getCelular(), 0, 0, 'L');
        $this->pdf->Cell(116, 5, $this->empresa->getEmail(), 0, 0, 'L');

        /**
         * linea x
         */
        $this->pdf->SetY(108);
        $this->pdf->SetX(74);
        $this->pdf->Cell(5, 5,  '' . $natural, 0, 0, 'L');
        $this->pdf->SetX(100);
        $this->pdf->Cell(5, 5,  '' . $juridica, 0, 0, 'L');
        $this->pdf->SetX(145);
        $this->pdf->Cell(5, 5,  '' . $oficial, 0, 0, 'L');
        $this->pdf->SetX(167);
        $this->pdf->Cell(5, 5,  '' . $privada, 0, 0, 'L');
        $this->pdf->SetX(194);
        $this->pdf->Cell(5, 5,  '' . $mixta, 0, 0, 'L');

        /**
         * linea x
         */
        $this->pdf->setXY(9, 117);
        $this->pdf->SetFont('helvetica', '', 8);

        $this->pdf->Cell(142, 5, Tag::capitalize(@$_codact[$this->empresa->getCodact()]), 0, 0, 'L');
        $this->pdf->Cell(35, 5, $this->empresa->getCodact(), 0, 0, 'L');


        /**
         * linea x
         */
        $this->pdf->SetXY(9, 126);
        $this->pdf->Cell(95, 5, @$_codzon[$this->empresa->getCodzon()], 0, 0, 'L');
        $this->pdf->Cell(100, 5, Tag::capitalize($this->empresa->getRazsoc()), 0, 0, 'L');

        /**
         * linea x
         */
        $this->pdf->setXY(32, 140);
        $this->pdf->Cell(8, 5, '' . $cc, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $ti, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $rc, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $ce, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $nuip, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $p, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $nit, 0, 0, 'C');
        $this->pdf->Cell(8, 5, '' . $cd, 0, 0, 'C');
        $this->pdf->Cell(105, 5, $this->empresa->getCedrep(), 0, 0, 'L');

        /**
         * linea x
         */
        $this->pdf->setXY(9, 150);
        $this->pdf->Cell(195, 5, Tag::capitalize($this->empresa->getRepleg()), 0, 0, 'L');

        $this->pdf->setY(158);
        $this->pdf->setX(10);
        $this->pdf->Cell(51, 5, $this->empresa->getTelefono(), 0, 0, 'L');
        $this->pdf->Cell(52, 5, $this->empresa->getCelular(), 0, 0, 'L');
        $this->pdf->Cell(93, 5, $this->empresa->getEmail(), 0, 0, 'L');


        /**
         * linea x
         */
        $this->pdf->SetXY(10, 172);
        $this->pdf->Cell(137, 5, $this->empresa->getRepleg(), 0, 0, 'L');
        $this->pdf->Cell(137, 5, "REPRESENTANTE LEGAL", 0, 0, 'L');
        $this->pdf->Cell(58, 5, "", 0, 0, 'L');

        $this->pdf->SetXY(10, 181);
        if ($this->empresa->getTelpri() == NULL) {
            $this->pdf->Cell(51, 5, $this->empresa->getTelefono(), 0, 0, 'L');
        } else {
            $this->pdf->Cell(51, 5, $this->empresa->getTelpri(), 0, 0, 'L');
        }

        if ($this->empresa->getCelpri() == NULL) {
            $this->pdf->Cell(51, 5, $this->empresa->getCelular(), 0, 0, 'L');
        } else {
            $this->pdf->Cell(52, 5, $this->empresa->getCelpri(), 0, 0, 'L');
        }
        $this->pdf->Cell(93, 5, $this->empresa->getEmail(), 0, 0, 'L');
        return $this->pdf;
    }
}
