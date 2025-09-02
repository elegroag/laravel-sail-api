<?php

namespace App\Services\Formularios\Declaration;

use App\Services\Formularios\Documento;

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

        $page = Core::getInitialPath() . 'public/docs/form/declaraciones/declaracion_jura_conyuge.png';
        $this->pdf->ImageAlpha($page, 0, 0, 210, 297, '');

        $this->conyuge = $this->request->getParam('conyuge');
        $this->trabajador = $this->request->getParam('trabajador');
        $this->bloqueTrabajador();
        $this->bloqueConyuge();
        $page = Core::getInitialPath() . 'public/docs/sello-firma.png';
        $this->pdf->ImageAlpha($page, 160, 275, 30, 20, '');
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
        $today = new Date();
        $ciudad = ($this->conyuge->getCodzon()) ? $_codciu[$this->conyuge->getCodzon()] : 'Florencia';
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';

        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Nombre trabajador', 'texto' => capitalize($nomtra), 'x' => 20, 'y' => 30),
            array('lb' => 'Año', 'texto' => $today->getYear(), 'x' => 122, 'y' => 21),
            array('lb' => 'Mes', 'texto' => $today->getMonth(), 'x' => 134, 'y' => 21),
            array('lb' => 'Dia', 'texto' => $today->getDay(), 'x' => 144, 'y' => 21),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 152, 'y' => 21),
            array('lb' => 'TipoDoc trabajador', 'texto' => $detdoc, 'x' => 72, 'y' => 36),
            array('lb' => 'Numero documento', 'texto' => $this->trabajador->getCedtra(), 'x' => 156, 'y' => 36),
            $this->posCompaPermanente(),
            $this->postEstadoCivil()
        );
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
        $datos = array(
            array('lb' => 'Nombre conyuge', 'texto' => substr($nomcony, 0, 53), 'x' => 57, 'y' => 98),
            array('lb' => 'Telefono conyuge', 'texto' => $this->conyuge->getTelefono(), 'x' => 172, 'y' => 98),
            array('lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 64, 'y' => 105),
            array('lb' => 'Cedula conyuge', 'texto' => $this->conyuge->getCedcon(), 'x' => 84, 'y' => 105),
            array('lb' => 'Email conyuge', 'texto' => $this->conyuge->getEmail(), 'x' => 142, 'y' => 105),
            array('lb' => 'Nombre conyuge', 'texto' => substr($nomcony, 0, 53), 'x' => 54, 'y' => 134),
            array('lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 33, 'y' => 139),
            array('lb' => 'Cedula conyuge', 'texto' => $this->conyuge->getCedcon(), 'x' => 60, 'y' => 139),
            array('lb' => 'Tiempo convive', 'texto' => $this->conyuge->getTiecon(), 'x' => 33, 'y' => 144),
            array('lb' => 'Meses convive', 'texto' => '1', 'x' => 53, 'y' => 144),
            $this->posOcupacion()

        );
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
        return array('lb' => 'Ocupacion', 'texto' => 'X', 'x' => $x, 'y' => 157);
    }

    function posCompaPermanente()
    {
        $v = ($this->conyuge->getComper() == 'S') ? 'X' : '';
        return array('lb' => 'Compañera permanente', 'texto' => $v, 'x' => 88, 'y' => 56);
    }

    function postEstadoCivil()
    {
        $v = ($this->conyuge->getEstciv() == 2 || $this->conyuge->getEstciv() == 4) ? 'X' : '';
        return array('lb' => 'Compañera union libre', 'texto' => $v, 'x' => 158.5, 'y' => 56);
    }
}
