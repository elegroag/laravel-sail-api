<?php
Core::importLibrary('DocumentoAdapter', 'Formularios');

class FacultativoDatosPersonales extends DocumentoAdapter
{

    /**
     * facultativo variable
     *
     * @var Mercurio36
     */
    private $facultativo;

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
        if (!$this->request->getParam('facultativo')) {
            throw new DebugException("Error la facultativo no esté disponible", 501);
        }
        $this->facultativo = $this->request->getParam('facultativo');
        $this->pdf->SetTitle("Oficio de tratamiento de datos personales, NIT {$this->facultativo->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->facultativo->getPriape()} {$this->facultativo->getSegape()} {$this->facultativo->getPrinom()} {$this->facultativo->getSegnom()}, COMFACA");
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
        $this->bloqueEmpresa($this->facultativo);
        $selloFirma = Core::getInitialPath() . 'public/docs/sello-firma.png';
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
        $_codciu = ParamsFacultativo::getCiudades();
        $nombre = capitalize($this->facultativo->getPrinom() . ' ' . $this->facultativo->getSegnom() . ' ' . $this->facultativo->getPriape() . ' ' . $this->facultativo->getSegape());
        $tipo_documento = $this->facultativo->getCoddocrepleg();
        $ciudad =  ($this->facultativo->getCodzon()) ? $_codciu[$this->facultativo->getCodzon()] : 'Florencia';

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 70, 'y' => 94),
            array('lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 70, 'y' => 100),
            array('lb' => 'Cedula', 'texto' => $this->facultativo->getCedtra(), 'x' => 151, 'y' => 100),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 50, 'y' => 108),

            array('lb' => 'Fecha', 'texto' => $this->facultativo->getFecsol(), 'x' => 151, 'y' => 108),
            array('lb' => 'Dirección', 'texto' => $this->facultativo->getDireccion(), 'x' => 70, 'y' => 118),

            array('lb' => 'Telefono', 'texto' => $this->facultativo->getTelefono(), 'x' => 70, 'y' => 128),
            array('lb' => 'Celular', 'texto' => $this->facultativo->getCelular(), 'x' => 151, 'y' => 128),
            array('lb' => 'Email', 'texto' => $this->facultativo->getEmail(), 'x' => 70, 'y' => 138),
        );

        $this->addBloq($datos);
        return $this->pdf;
    }
}
