<?php

namespace App\Services\Formularios\Declaration;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsConyuge;
use App\Models\Gener18;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

class JuramentadaConyuge extends Documento
{

    /**
     * $trabajador variable
     *
     * @var Mercurio31
     */
    private $trabajador;

    /**
     * $conyuge variable
     *
     * @var Mercurio32
     */
    private $conyuge;

    /**
     * main function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('conyuge')) {
            throw new DebugException("Error el conyuge no esté disponible", 501);
        }

        $this->conyuge = $this->request->getParam('conyuge');
        $this->trabajador = $this->request->getParam('trabajador');
        $this->bloqueTrabajador();
        $this->bloqueConyuge();
        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
        return $this;
    }

    /**
     * bloqueConyuge function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    function bloqueTrabajador()
    {
        $_codciu = ParamsConyuge::getCiudades();
        $nomtra = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());
        $today = Carbon::now();
        $ciudad = ($this->conyuge->getCodzon()) ? $_codciu[$this->conyuge->getCodzon()] : 'Florencia';
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = [
            ['lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 122, 'y' => 17],
            ['lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 134, 'y' => 17],
            ['lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 144, 'y' => 17],
            ['lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 152, 'y' => 17],
            ['lb' => 'Nombre trabajador', 'texto' => capitalize($nomtra), 'x' => 20, 'y' => 25],
            ['lb' => 'TipoDoc trabajador', 'texto' => $detdoc, 'x' => 72, 'y' => 31],
            ['lb' => 'Numero documento', 'texto' => $this->trabajador->getCedtra(), 'x' => 156, 'y' => 31],
            $this->posCompaPermanente(),
            $this->postEstadoCivil()
        ];
        $this->addBloq($datos);
        return $this->pdf;
    }

    function bloqueConyuge()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->conyuge->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getCodrua() : 'CC';
        $nomcony = capitalize($this->conyuge->getPrinom() . ' ' . $this->conyuge->getSegnom() . ' ' . $this->conyuge->getPriape() . ' ' . $this->conyuge->getSegape());

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = [
            ['lb' => 'Nombre conyuge', 'texto' => substr($nomcony, 0, 53), 'x' => 57, 'y' => 88],
            ['lb' => 'Telefono conyuge', 'texto' => $this->conyuge->getTelefono(), 'x' => 172, 'y' => 88],
            ['lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 64, 'y' => 94],
            ['lb' => 'Cedula conyuge', 'texto' => $this->conyuge->getCedcon(), 'x' => 84, 'y' => 94],
            ['lb' => 'Email conyuge', 'texto' => $this->conyuge->getEmail(), 'x' => 142, 'y' => 94],
            ['lb' => 'Nombre conyuge', 'texto' => substr($nomcony, 0, 53), 'x' => 54, 'y' => 120],
            ['lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 33, 'y' => 125],
            ['lb' => 'Cedula conyuge', 'texto' => $this->conyuge->getCedcon(), 'x' => 60, 'y' => 125],
            ['lb' => 'Tiempo convive', 'texto' => $this->conyuge->getTiecon(), 'x' => 33, 'y' => 130],
            ['lb' => 'Meses convive', 'texto' => '1', 'x' => 53, 'y' => 130],
            $this->posOcupacion()
        ];
        $this->addBloq($datos);
        return $this->pdf;
    }

    function posOcupacion()
    {
        switch ($this->conyuge->getCodocu()) {
            case '01':
                #EMPLEADO
                $x = 54;
                break;
            case '04':
                #INDEPENDIENTE
                $x = 86;
                break;
            case '03':
                #PENSIONADO
                $x = 120;
                break;
            case '02':
                #ESTUDIANTE
                $x = 148;
                break;
            default:
                # Ninguna
                $x = 174;
                break;
        }
        return array('lb' => 'Ocupacion', 'texto' => 'X', 'x' => $x, 'y' => 142);
    }

    function posCompaPermanente()
    {
        $v = ($this->conyuge->getComper() == 'S') ? 'X' : '';
        return array('lb' => 'Compañera permanente', 'texto' => $v, 'x' => 88, 'y' => 49);
    }

    function postEstadoCivil()
    {
        $v = ($this->conyuge->getEstciv() == 2 || $this->conyuge->getEstciv() == 4) ? 'X' : '';
        return array('lb' => 'Compañera union libre', 'texto' => $v, 'x' => 158.5, 'y' => 50);
    }
}
