<?php

namespace App\Services\Formularios\Politica;

use App\Services\Formularios\DocumentoAdapter;

class EmpresaDatosPersonales extends DocumentoAdapter
{

    /**
     * empresa variable
     *
     * @var Mercurio30
     */
    private $empresa;

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
        if (!$this->request->getParam('empresa')) {
            throw new DebugException("Error la facultativo no esté disponible", 501);
        }
        $this->empresa = $this->request->getParam('empresa');
        $this->pdf->SetTitle("Oficio de tratamiento de datos personales, NIT {$this->empresa->getNit()}, COMFACA");
        $this->pdf->SetAuthor("{$this->empresa->getPriape()} {$this->empresa->getSegape()} {$this->empresa->getPrinom()} {$this->empresa->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Oficio de tratamiento de datos personales");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $imagen = Core::getInitialPath() . 'public/docs/formulario_mercurio/datos_personales_empresas_p0.jpg';
        $this->addBackground($imagen);
        $selloFirma = Core::getInitialPath() . 'public/docs/sello-firma.png';
        $this->pdf->Image($selloFirma, 160, 275, 30, 20, '', '', '', false, 300, '', false, false, 0);

        $this->pdf->AddPage();
        $imagen = Core::getInitialPath() . 'public/docs/formulario_mercurio/datos_personales_empresas_p1.jpg';
        $this->addBackground($imagen);
        $this->bloqueEmpresa($this->empresa);
        $selloFirma = Core::getInitialPath() . 'public/docs/sello-firma.png';
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
    }


    /**
     * bloqueEmpresa function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio30 $empresa
     * @param TCPDF $this->pdf
     * @return void
     */
    function bloqueEmpresa()
    {
        $_codciu = ParamsEmpresa::getCiudades();
        $repleg = $this->empresa->getRepleg();
        $razon_social = $this->empresa->getRazsoc();

        $ciudad = (!$this->empresa->getCodzon()) ? 'Florencia' : $_codciu[$this->empresa->getCodzon()];
        $coddorepleg = $this->empresa->getCoddocreplegArray();
        $tipo_documento = $coddorepleg[$this->empresa->getTipdoc()];
        $today = new Date();

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Razon social', 'texto' => capitalize($razon_social), 'x' => 75, 'y' => 86),
            array('lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 75, 'y' => 95),
            array('lb' => 'Nit', 'texto' => $this->empresa->getNit(), 'x' => 148, 'y' => 95),
            array('lb' => 'Nombre representante', 'texto' => capitalize($repleg), 'x' => 75, 'y' => 103),
            array('lb' => 'Tipo documento representante', 'texto' => $this->empresa->getCoddocrepleg(), 'x' => 75, 'y' => 112),
            array('lb' => 'Documento representante', 'texto' => $this->empresa->getCedrep(), 'x' => 148, 'y' => 112),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 55, 'y' => 120),
            array('lb' => 'Fecha', 'texto' => $today->getUsingFormatDefault(), 'x' => 148, 'y' => 120),
            array('lb' => 'Dirección', 'texto' => $this->empresa->getDireccion(), 'x' => 75, 'y' => 130),
            array('lb' => 'Telefono', 'texto' => $this->empresa->getTelefono(), 'x' => 75, 'y' => 136),
            array('lb' => 'Celular', 'texto' => $this->empresa->getCelular(), 'x' => 148, 'y' => 136),
            array('lb' => 'Email', 'texto' => $this->empresa->getEmail(), 'x' => 75, 'y' => 147),
        );

        $this->addBloq($datos);
        return $this->pdf;
    }
}
