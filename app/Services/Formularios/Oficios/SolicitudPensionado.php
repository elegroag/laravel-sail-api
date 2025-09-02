<?php

namespace App\Services\Formularios\Oficios;

use App\Services\Formularios\Documento;

class SolicitudPensionado extends Documento
{

    /**
     * $pensionado variable
     *
     * @var Mercurio38
     */
    private $pensionado;

    /**
     * main function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('pensionado')) {
            throw new DebugException("Error el pensionado no esté disponible", 501);
        }

        $page = Core::getInitialPath() . 'public/docs/form/oficios/oficio_solicitud_afiliacion.jpg';
        $this->pdf->Image($page, 0, 0, 210, 297, '');

        $this->pensionado = $this->request->getParam('pensionado');
        $this->bloqueEmpresa();
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

        $today = new Date();
        $ciudad = ($this->pensionado->getCodciu()) ? $_codciu[$this->pensionado->getCodciu()] : 'Florencia';
        $coddorepleg = $this->pensionado->getCoddocreplegArray();
        $tipo_documento = ($this->pensionado->getTipdoc()) ? $coddorepleg[$this->pensionado->getTipdoc()] : 'CC';
        $ciudad_labora = (!$this->pensionado->getCodzon()) ? 'Florencia' : $_codciu[$this->pensionado->getCodzon()];

        $fecini = new Date($this->pensionado->getFecini());
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 36, 'y' => 40),
            array('lb' => 'Año', 'texto' => $today->getYear(), 'x' => 165, 'y' => 40),
            array('lb' => 'Mes', 'texto' => $today->getMonth(), 'x' => 174, 'y' => 40),
            array('lb' => 'Dia', 'texto' => $today->getDay(), 'x' => 181, 'y' => 40),
            array('lb' => 'Tipo afiliacion', 'texto' => 'Trabajador ' . capitalize($this->pensionado->getCalempDetalle()), 'x' => 118, 'y' => 103),
            array('lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 22, 'y' => 110),
            array('lb' => 'Dia', 'texto' => $fecini->getDay(), 'x' => 28, 'y' => 122),
            array('lb' => 'Mes', 'texto' => $fecini->getMonth(), 'x' => 42, 'y' => 122),
            array('lb' => 'Año', 'texto' => $fecini->getYear(), 'x' => 54, 'y' => 122),
            $this->posAportes(),
            array('lb' => 'Tipo', 'texto' => 'Trabajador Pensionado', 'x' => 38, 'y' => 143),
            array('lb' => 'Nombre', 'texto' => 'Nombre Trabajador: ' . capitalize($nombre), 'x' => 35, 'y' => 152),
            array('lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 68, 'y' => 160),
            array('lb' => 'Cedula', 'texto' => $this->pensionado->getCedtra(), 'x' => 118, 'y' => 160),
            array('lb' => 'Dirección', 'texto' => $this->pensionado->getDireccion(), 'x' => 65, 'y' => 168),
            array('lb' => 'Celular', 'texto' => $this->pensionado->getCelular(), 'x' => 60, 'y' => 177.5),
            array('lb' => 'Telefono', 'texto' => $this->pensionado->getTelefono(), 'x' => 52, 'y' => 184),
            array('lb' => 'Email', 'texto' => $this->pensionado->getEmail(), 'x' => 111, 'y' => 184),
            array('lb' => 'Ciudad laboral', 'texto' => $ciudad_labora, 'x' => 96, 'y' => 193),
            $this->posAfiliaPrevius(),
            $this->posPazYsalvo()
        );
        $this->addBloq($datos);
        return $this->pdf;
    }

    function posAfiliaPrevius()
    {
        $x = 53;
        if ($this->request->getParam('previus')) {
            $x = 43.5;
        }
        return array('lb' => 'Afiliacíon previa', 'texto' => 'X', 'x' => $x, 'y' => 207);
    }

    function posPazYsalvo()
    {
        $x = 140;
        if ($this->request->getParam('previus')) {
            $x = 129;
        }
        return array('lb' => 'Paz y salvo', 'texto' => 'X', 'x' => $x, 'y' => 217);
    }

    function posAportes()
    {
        if ($this->pensionado->getTipafi() == '10') {
            $v = 'PENSIONADO 2%';
            //2%
        } elseif ($this->pensionado->getTipafi() == '64') {
            //0.6%
            $v = 'PENSIONADO 0.6%';
        } elseif ($this->pensionado->getTipafi() == '66') {
            //0.0% FIDELIDAD
            $v = 'PENSIONADO FIDELIDAD 0%';
        } elseif ($this->pensionado->getTipafi() == '67') {
            //0.0%
            $v = 'PENSIONADO 0%';
        } else {
            $v = 'DEPENDIENTE 4%';
        }
        return array('lb' => 'Paz y salvo', 'texto' => "PORCENTAJE DE COTIZACIÓN: {$v}", 'x' => 22, 'y' => 136);
    }
}
