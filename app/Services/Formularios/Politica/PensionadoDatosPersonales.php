<?php
Core::importLibrary('DocumentoAdapter', 'Formularios');

class PensionadoDatosPersonales extends DocumentoAdapter
{

    /**
     * pensionado variable
     *
     * @var Mercurio38
     */
    private $pensionado;

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
        if (!$this->request->getParam('pensionado')) {
            throw new DebugException("Error la facultativo no esté disponible", 501);
        }
        $this->pensionado = $this->request->getParam('pensionado');
        $this->pdf->SetTitle("Oficio de tratamiento de datos personales, NIT {$this->pensionado->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->pensionado->getPriape()} {$this->pensionado->getSegape()} {$this->pensionado->getPrinom()} {$this->pensionado->getSegnom()}, COMFACA");
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
        $this->bloqueEmpresa($this->pensionado);
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
        $_codciu = ParamsPensionado::getCiudades();
        $nombre = capitalize($this->pensionado->getPrinom() . ' ' . $this->pensionado->getSegnom() . ' ' . $this->pensionado->getPriape() . ' ' . $this->pensionado->getSegape());
        $tipo_documento = $this->pensionado->getCoddocrepleg();
        $ciudad =  ($this->pensionado->getCodzon()) ? $_codciu[$this->pensionado->getCodzon()] : 'Florencia';

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 70, 'y' => 94),
            array('lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 70, 'y' => 100),
            array('lb' => 'Cedula', 'texto' => $this->pensionado->getCedtra(), 'x' => 151, 'y' => 100),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 50, 'y' => 108),

            array('lb' => 'Fecha', 'texto' => $this->pensionado->getFecsol(), 'x' => 151, 'y' => 108),
            array('lb' => 'Dirección', 'texto' => $this->pensionado->getDireccion(), 'x' => 70, 'y' => 118),

            array('lb' => 'Telefono', 'texto' => $this->pensionado->getTelefono(), 'x' => 70, 'y' => 128),
            array('lb' => 'Celular', 'texto' => $this->pensionado->getCelular(), 'x' => 151, 'y' => 128),
            array('lb' => 'Email', 'texto' => $this->pensionado->getEmail(), 'x' => 70, 'y' => 138),
        );

        $this->addBloq($datos);
        return $this->pdf;
    }
}
