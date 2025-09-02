<?php

namespace App\Services\Formularios\Politica;

use App\Services\Formularios\DocumentoAdapter;

class TrabajadorDatosPersonales extends DocumentoAdapter
{

    /**
     * empresa variable
     * @var Mercurio30
     */
    private $empresa;

    /**
     * trabajador variable
     * @var Mercurio31
     */
    private $trabajador;

    /**
     * main function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('trabajador')) {
            throw new DebugException("Error el trabajador no esté disponible", 501);
        }
        $this->trabajador = $this->request->getParam('trabajador');

        $this->pdf->SetTitle("Oficio de tratamiento de datos personales, NIT {$this->trabajador->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->trabajador->getPriape()} {$this->trabajador->getSegape()} {$this->trabajador->getPrinom()} {$this->trabajador->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Oficio de tratamiento de datos personales");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $imagen = Core::getInitialPath() . 'public/docs/form/datos-personales/datos-personales-trabajador-01.jpg';
        $this->addBackground($imagen);
        $selloFirma = Core::getInitialPath() . 'public/docs/sello-firma.png';
        $this->pdf->Image($selloFirma, 160, 275, 30, 20, '', '', '', false, 300, '', false, false, 0);

        $this->pdf->AddPage();
        $imagen = Core::getInitialPath() . 'public/docs/form/datos-personales/datos-personales-trabajador-02.jpg';
        $this->addBackground($imagen);
        $this->bloqueEmpresa();
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
        $_codciu = ParamsTrabajador::getCiudades();
        $nombre = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());

        $ciudad =  ($this->trabajador->getCodzon()) ? $_codciu[$this->trabajador->getCodzon()] : 'Florencia';

        $mcoddoc = $this->trabajador->getCoddocArray();
        $tipo_documento = $mcoddoc[$this->trabajador->getCoddoc()];

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 70, 'y' => 94),
            array('lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 70, 'y' => 100),
            array('lb' => 'Cedula', 'texto' => $this->trabajador->getCedtra(), 'x' => 151, 'y' => 100),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 50, 'y' => 108),

            array('lb' => 'Fecha', 'texto' => $this->trabajador->getFecsol(), 'x' => 151, 'y' => 108),
            array('lb' => 'Dirección', 'texto' => $this->trabajador->getDireccion(), 'x' => 70, 'y' => 118),

            array('lb' => 'Telefono', 'texto' => $this->trabajador->getTelefono(), 'x' => 70, 'y' => 128),
            array('lb' => 'Celular', 'texto' => $this->trabajador->getCelular(), 'x' => 151, 'y' => 128),
            array('lb' => 'Email', 'texto' => $this->trabajador->getEmail(), 'x' => 70, 'y' => 138),
        );

        $this->addBloq($datos);
        return $this->pdf;
    }
}
