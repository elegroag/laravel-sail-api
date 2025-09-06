<?php

namespace App\Services\Formularios\Oficios;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

class SolicitudEmpresa extends Documento
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
            throw new DebugException("Error la facultativo no esté disponible", 501);
        }
        $this->empresa = $this->request->getParam('empresa');
        $this->pdf->SetTitle("Solicitud de afiliación con NIT {$this->empresa->getNit()}, COMFACA");
        $autor = "{$this->empresa->getPriape()} {$this->empresa->getSegape()} {$this->empresa->getPrinom()} {$this->empresa->getSegnom()}";
        $this->pdf->SetAuthor($autor);
        $this->pdf->SetSubject("Carta de solicitud de afiliación a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $this->bloqueEmpresa();
        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
    }

    /**
     * bloqueEmpresa function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    function bloqueEmpresa()
    {
        $_codciu = ParamsEmpresa::getCiudades();
        $today = Carbon::now();
        if ($this->empresa->getCiupri() == NULL) {
            $this->empresa->setCiupri($this->empresa->getCodciu());
        }

        /**
         * linia x
         */
        $this->pdf->SetY(27);
        $this->pdf->SetX(34);
        $this->pdf->Cell(33, 5, (!$this->empresa->getCodciu()) ? 'Florencia' : $_codciu[$this->empresa->getCodciu()], 0, 0, 'L');
        $this->pdf->SetX(162);
        $this->pdf->Cell(8, 4, $today->format('Y'), 0, 0, 'C');
        $this->pdf->Cell(6, 4, $today->format('m'), 0, 0, 'C');
        $this->pdf->Cell(6, 4, $today->format('d'), 0, 0, 'C');


        /**
         * linia x
         */
        $this->pdf->SetY(90);
        $this->pdf->SetX(140);
        $this->pdf->Cell(80, 4, $this->empresa->getCalempDetalle(), 0, 0, 'L');


        /**
         * linia x
         */
        $this->pdf->SetY(96.5);
        $this->pdf->SetX(38);
        $this->pdf->Cell(80, 4, $this->empresa->getRazsoc(), 0, 0, 'L');

        /**
         * linia x
         */
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->SetY(109);
        $this->pdf->SetX(28);
        $this->pdf->Cell(14, 4, $today->format('d'), 0, 0, 'L');
        $this->pdf->Cell(12, 4, $today->format('m'), 0, 0, 'L');
        $this->pdf->Cell(10, 4, $today->format('Y'), 0, 0, 'L');

        /**
         * linia x
         */
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetY(131.5);
        $this->pdf->SetX(58);
        $this->pdf->Cell(180, 4, $this->empresa->getRazsoc(), 0, 0, 'L');


        /**
         * linia x
         */
        $this->pdf->SetY(141);
        $this->pdf->SetX(68);
        $coddorepleg = $this->empresa->getCoddocreplegArray();
        $this->pdf->Cell(180, 4, $coddorepleg[$this->empresa->getTipdoc()], 0, 0, 'L');
        $this->pdf->SetX(117);
        $this->pdf->Cell(180, 4, $this->empresa->getNit(), 0, 0, 'L');


        /**
         * linia x
         */
        $this->pdf->SetY(151);
        $this->pdf->SetX(75);
        $this->pdf->Cell(180, 4, $this->empresa->getDireccion(), 0, 0, 'L');


        /**
         * linia x
         */
        $this->pdf->SetY(160.5);
        $this->pdf->SetX(50);
        $this->pdf->Cell(61, 4, $this->empresa->getTelefono(), 0, 0, 'L');
        $this->pdf->Cell(100, 4, $this->empresa->getEmail(), 0, 0, 'L');


        /**
         * linia x
         */
        $this->pdf->SetY(170);
        $this->pdf->SetX(105);
        $this->pdf->Cell(61, 4, (!$this->empresa->getCodzon()) ? 'Florencia' : $_codciu[$this->empresa->getCodzon()], 0, 0, 'L');

        /**
         * linia x
         */
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->SetY(211);
        $this->pdf->SetX(105);
        $this->pdf->Cell(61, 4, capitalize($this->empresa->getRepleg()), 0, 0, 'L');

        /**
         * linia x
         */
        $this->pdf->SetY(218);
        $this->pdf->SetX(63);
        $this->pdf->Cell(61, 4, $this->empresa->getTelpri(), 0, 0, 'L');

        /**
         * linia x
         */
        $this->pdf->SetY(224);
        $this->pdf->SetX(65);
        $this->pdf->Cell(61, 4, $this->empresa->getEmailpri(), 0, 0, 'L');

        /**
         * linia x
         */
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetY(238);
        $this->pdf->SetX(43);
        $this->pdf->Cell(61, 4, $this->empresa->getCedrep(), 0, 0, 'L');

        return $this->pdf;
    }
}
