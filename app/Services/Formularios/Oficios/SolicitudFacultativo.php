<?php

namespace App\Services\Formularios\Oficios;

use App\Services\Formularios\Documento;
use Carbon\Carbon;
use App\Exceptions\DebugException;
use App\Library\Collections\ParamsFacultativo;

class SolicitudFacultativo extends Documento
{

    /**
     * $facultativo variable
     *
     * @var Mercurio36
     */
    private $facultativo;

    /**
     * main function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('facultativo')) {
            throw new DebugException("Error el facultativo no esté disponible", 501);
        }

        $this->facultativo = $this->request->getParam('facultativo');
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
        $_codciu = ParamsFacultativo::getCiudades();
        $nombre = capitalize($this->facultativo->getPrinom() . ' ' . $this->facultativo->getSegnom() . ' ' . $this->facultativo->getPriape() . ' ' . $this->facultativo->getSegape());

        $today = Carbon::now();
        $ciudad = ($this->facultativo->getCodciu()) ? $_codciu[$this->facultativo->getCodciu()] : 'Florencia';
        $coddorepleg = $this->facultativo->getCoddocreplegArray();
        $tipo_documento = ($this->facultativo->getTipdoc()) ? $coddorepleg[$this->facultativo->getTipdoc()] : 'CC';
        $ciudad_labora = (!$this->facultativo->getCodzon()) ? 'Florencia' : $_codciu[$this->facultativo->getCodzon()];

        $fecini = Carbon::parse($this->facultativo->getFecini());
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = [
            ['lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 36, 'y' => 38],
            ['lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 165, 'y' => 38],
            ['lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 174, 'y' => 38],
            ['lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 181, 'y' => 38],
            ['lb' => 'Tipo afiliacion', 'texto' => 'Trabajador ' . capitalize($this->facultativo->getCalempDetalle()), 'x' => 118, 'y' => 101],
            ['lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 22, 'y' => 107],
            ['lb' => 'Dia', 'texto' => $fecini->format('d'), 'x' => 28, 'y' => 119],
            ['lb' => 'Mes', 'texto' => $fecini->format('m'), 'x' => 42, 'y' => 119],
            ['lb' => 'Año', 'texto' => $fecini->format('Y'), 'x' => 54, 'y' => 119],
            $this->posAportes(118),
            ['lb' => 'Tipo', 'texto' => 'Trabajador Independiente', 'x' => 38, 'y' => 141],
            ['lb' => 'Nombre', 'texto' => 'Nombre Trabajador: ' . capitalize($nombre), 'x' => 35, 'y' => 150],
            ['lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 68, 'y' => 158],
            ['lb' => 'Cedula', 'texto' => $this->facultativo->getCedtra(), 'x' => 118, 'y' => 158],
            ['lb' => 'Dirección', 'texto' => $this->facultativo->getDireccion(), 'x' => 65, 'y' => 164],
            ['lb' => 'Celular', 'texto' => $this->facultativo->getCelular(), 'x' => 60, 'y' => 175],
            ['lb' => 'Telefono', 'texto' => $this->facultativo->getTelefono(), 'x' => 52, 'y' => 182],
            ['lb' => 'Email', 'texto' => $this->facultativo->getEmail(), 'x' => 111, 'y' => 182],
            ['lb' => 'Ciudad laboral', 'texto' => $ciudad_labora, 'x' => 96, 'y' => 190],
            $this->posAfiliaPrevius(190),
            $this->posPazYsalvo(190)
        ];
        $this->addBloq($datos);
        return $this->pdf;
    }

    function posAfiliaPrevius($y)
    {
        $x = 53;
        if ($this->request->getParam('previus')) {
            $x = 43.5;
        }
        return array('lb' => 'Afiliacíon previa', 'texto' => 'X', 'x' => $x, 'y' => $y + 15);
    }

    function posPazYsalvo($y)
    {
        $x = 140;
        if ($this->request->getParam('previus')) {
            $x = 129;
        }
        return array('lb' => 'Paz y salvo', 'texto' => 'X', 'x' => $x, 'y' => $y + 25);
    }

    function posAportes($y)
    {
        if ($this->facultativo->getTipafi() == '63') {
            $v = 'FACULTATIVO 2%';
        } else {
            $v = 'FACULTATIVO 4%';
        }
        return array('lb' => 'Paz y salvo', 'texto' => "PORCENTAJE DE COTIZACIÓN: {$v}", 'x' => 22, 'y' => $y + 35);
    }
}
