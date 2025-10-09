<?php

namespace App\Services\Formularios\Oficios;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsPensionado;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

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
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function main()
    {
        if (! $this->request->getParam('pensionado')) {
            throw new DebugException('Error el pensionado no esté disponible', 501);
        }

        $this->pensionado = $this->request->getParam('pensionado');
        $this->bloqueEmpresa();
        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
    }

    /**
     * bloqueEmpresa function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function bloqueEmpresa()
    {
        $_codciu = ParamsPensionado::getCiudades();
        $nombre = capitalize($this->pensionado->getPrinom().' '.$this->pensionado->getSegnom().' '.$this->pensionado->getPriape().' '.$this->pensionado->getSegape());

        $today = Carbon::now();
        $ciudad = ($this->pensionado->getCodciu()) ? $_codciu[$this->pensionado->getCodciu()] : 'Florencia';
        $coddorepleg = $this->pensionado->getCoddocreplegArray();
        $tipo_documento = ($this->pensionado->getTipdoc()) ? $coddorepleg[$this->pensionado->getTipdoc()] : 'CC';
        $ciudad_labora = (! $this->pensionado->getCodzon()) ? 'Florencia' : $_codciu[$this->pensionado->getCodzon()];

        $fecini = Carbon::parse($this->pensionado->getFecini());
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = [
            ['lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 36, 'y' => 38],
            ['lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 165, 'y' => 38],
            ['lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 174, 'y' => 38],
            ['lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 181, 'y' => 38],
            ['lb' => 'Tipo afiliacion', 'texto' => 'Trabajador '.capitalize($this->pensionado->getCalempDetalle()), 'x' => 118, 'y' => 101],
            ['lb' => 'Nombre', 'texto' => capitalize($nombre), 'x' => 22, 'y' => 107],
            ['lb' => 'Dia', 'texto' => $fecini->format('d'), 'x' => 28, 'y' => 119],
            ['lb' => 'Mes', 'texto' => $fecini->format('m'), 'x' => 42, 'y' => 119],
            ['lb' => 'Año', 'texto' => $fecini->format('Y'), 'x' => 54, 'y' => 119],
            $this->posAportes(118),
            ['lb' => 'Tipo', 'texto' => 'Trabajador Pensionado', 'x' => 38, 'y' => 141],
            ['lb' => 'Nombre', 'texto' => 'Nombre Trabajador: '.capitalize($nombre), 'x' => 35, 'y' => 150],
            ['lb' => 'Tipo documento', 'texto' => $tipo_documento, 'x' => 68, 'y' => 158],
            ['lb' => 'Cedula', 'texto' => $this->pensionado->getCedtra(), 'x' => 118, 'y' => 158],
            ['lb' => 'Dirección', 'texto' => $this->pensionado->getDireccion(), 'x' => 65, 'y' => 164],
            ['lb' => 'Celular', 'texto' => $this->pensionado->getCelular(), 'x' => 60, 'y' => 175],
            ['lb' => 'Telefono', 'texto' => $this->pensionado->getTelefono(), 'x' => 52, 'y' => 182],
            ['lb' => 'Email', 'texto' => $this->pensionado->getEmail(), 'x' => 111, 'y' => 182],
            ['lb' => 'Ciudad laboral', 'texto' => $ciudad_labora, 'x' => 96, 'y' => 190],
            $this->posAfiliaPrevius(190),
            $this->posPazYsalvo(190),
        ];
        $this->addBloq($datos);

        return $this->pdf;
    }

    public function posAfiliaPrevius($y)
    {
        $x = 53;
        if ($this->request->getParam('previus')) {
            $x = 43.5;
        }

        return ['lb' => 'Afiliacíon previa', 'texto' => 'X', 'x' => $x, 'y' => $y + 15];
    }

    public function posPazYsalvo($y)
    {
        $x = 140;
        if ($this->request->getParam('previus')) {
            $x = 129;
        }

        return ['lb' => 'Paz y salvo', 'texto' => 'X', 'x' => $x, 'y' => $y + 25];
    }

    public function posAportes()
    {
        if ($this->pensionado->getTipafi() == '10') {
            $v = 'PENSIONADO 2%';
            // 2%
        } elseif ($this->pensionado->getTipafi() == '64') {
            // 0.6%
            $v = 'PENSIONADO 0.6%';
        } elseif ($this->pensionado->getTipafi() == '66') {
            // 0.0% FIDELIDAD
            $v = 'PENSIONADO FIDELIDAD 0%';
        } elseif ($this->pensionado->getTipafi() == '67') {
            // 0.0%
            $v = 'PENSIONADO 0%';
        } else {
            $v = 'DEPENDIENTE 4%';
        }

        return ['lb' => 'Paz y salvo', 'texto' => "PORCENTAJE DE COTIZACIÓN: {$v}", 'x' => 22, 'y' => 136];
    }
}
