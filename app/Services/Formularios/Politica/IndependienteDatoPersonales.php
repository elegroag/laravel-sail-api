<?php

namespace App\Services\Formularios\Politica;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Services\Formularios\Documento;

class IndependienteDatoPersonales extends Documento
{

    /**
     * independiente variable
     *
     * @var Mercurio41
     */
    private $independiente;

    /**
     * main function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param TCPDF $pdf
     * @param [type] $params
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('independiente')) {
            throw new DebugException("Error la facultativo no esté disponible", 501);
        }
        $this->independiente = $this->request->getParam('independiente');
        $this->pdf->SetTitle("Oficio de tratamiento de datos personales, NIT {$this->independiente->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->independiente->getPriape()} {$this->independiente->getSegape()} {$this->independiente->getPrinom()} {$this->independiente->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Oficio de tratamiento de datos personales");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $imagen = public_path('img/form/datos-personales/datos-personales-trabajador-01.jpg');

        $this->addBackground($imagen);
        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 275, 30, 20, '', '', '', false, 300, '', false, false, 0);

        $this->pdf->AddPage();
        $imagen = public_path('img/form/datos-personales/datos-personales-trabajador-02.jpg');
        $this->addBackground($imagen);
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
        $nombre = capitalize($this->independiente->getPrinom() . ' ' . $this->independiente->getSegnom() . ' ' . $this->independiente->getPriape() . ' ' . $this->independiente->getSegape());
        $tipo_documento = $this->independiente->getCoddocrepleg();
        $ciudad =  ($this->independiente->getCodzon()) ? $_codciu[$this->independiente->getCodzon()] : 'Florencia';

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 70, 'y' => 94),
            array('lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 70, 'y' => 100),
            array('lb' => 'Cedula', 'texto' => $this->independiente->getCedtra(), 'x' => 151, 'y' => 100),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 50, 'y' => 108),

            array('lb' => 'Fecha', 'texto' => $this->independiente->getFecsol(), 'x' => 151, 'y' => 108),
            array('lb' => 'Dirección', 'texto' => $this->independiente->getDireccion(), 'x' => 70, 'y' => 118),

            array('lb' => 'Telefono', 'texto' => $this->independiente->getTelefono(), 'x' => 70, 'y' => 128),
            array('lb' => 'Celular', 'texto' => $this->independiente->getCelular(), 'x' => 151, 'y' => 128),
            array('lb' => 'Email', 'texto' => $this->independiente->getEmail(), 'x' => 70, 'y' => 138),
        );

        $this->addBloq($datos);
        return $this->pdf;
    }
}
