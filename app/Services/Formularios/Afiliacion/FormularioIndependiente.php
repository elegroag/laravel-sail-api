<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsIndependiente;
use App\Library\Collections\ParamsTrabajador;
use App\Services\Formularios\Documento;

class FormularioIndependiente extends Documento
{
    /**
     * independiente variable
     * @var Mercurio41
     */
    private $independiente;


    /**
     * main function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('independiente')) {
            throw new DebugException("Error la empresa no esté disponible", 501);
        }
        $this->independiente = $this->request->getParam('independiente');

        $this->pdf->SetTitle("Formulario afiliación del independiente {$this->independiente->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->independiente->getNombreCompleto()}, COMFACA");
        $this->pdf->SetSubject("Formulario de afiliación a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');
        $this->pdf->SetAutoPageBreak(false, 0);

        $this->tipoAfiliado();
        $this->dataEmpleador();
        $this->dataTrabajador();
        $this->dataLaboral();

        if ($this->independiente->getTippag() != 'T') {
            $this->dataMedioPago();
        }
        $this->addBloq([
            ['lb' => 'Autoriza datos', 'texto' => 'X', 'x' => 70, 'y' => 253]
        ]);
        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
        return $this;
    }

    function tipoAfiliado()
    {
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 38, 'y' => 36.5),
            $this->posTipoAfiliado()
        );
        $this->addBloq($datos);
    }

    function dataEmpleador()
    {
        $this->pdf->SetFont('helvetica', '', 7);
        $razon_social = capitalize($this->independiente->getNombreCompleto());
        $datos = array(
            array('lb' => 'Razon social', 'texto' => $razon_social, 'x' => 9, 'y' => 48),
            array('lb' => 'nit', 'texto' => $this->independiente->getCedtra(), 'x' => 158, 'y' => 48),
            array('lb' => 'Sucursal', 'texto' => $razon_social, 'x' => 9, 'y' => 56),
            array('lb' => 'Telefono', 'texto' => $this->independiente->getTelefono(), 'x' => 170, 'y' => 56),
        );
        $this->addBloq($datos);
    }

    function dataTrabajador()
    {
        $mciudad = ParamsIndependiente::getCiudades();
        $ciudad = ($this->independiente->getCodciu()) ? $mciudad[$this->independiente->getCodciu()] : ' FLORENCIA';

        $mresguardos = ParamsTrabajador::getResguardos();
        $resguardo = ($this->independiente->getResguardo_id()) ? $mresguardos[$this->independiente->getResguardo_id()] : 'NO APLICA';

        $mpueblos = ParamsTrabajador::getPueblosIndigenas();
        $pueblo = ($this->independiente->getPub_indigena_id()) ? $mpueblos[$this->independiente->getPub_indigena_id()] : 'NO APLICA';

        $discapacidad = '';
        if ($tipdis = $this->independiente->getTipdis()) {
            $mtidis = ParamsTrabajador::getTipoDiscapacidad();
            $discapacidad = $mtidis[$tipdis];
        }

        $this->pdf->SetFont('helvetica', '', 8);
        $datos = array(
            array('lb' => 'Cedula trabajador', 'texto' => $this->independiente->getCedtra(), 'x' => 10, 'y' => 66),
            $this->posTipoDocumento(66),
            array('lb' => 'Celular', 'texto' => $this->independiente->getTelefono(), 'x' => 82, 'y' => 66),
            array('lb' => 'Email', 'texto' => $this->independiente->getEmail(), 'x' => 125, 'y' => 66),
            array('lb' => 'Primer apellido', 'texto' => $this->independiente->getPriape(), 'x' => 10, 'y' => 74),
            array('lb' => 'Segundo apellido', 'texto' => $this->independiente->getSegape(), 'x' => 54, 'y' => 74),
            array('lb' => 'Primer nombre', 'texto' => $this->independiente->getPrinom(), 'x' => 105, 'y' => 74),
            array('lb' => 'Segundo nombre', 'texto' => $this->independiente->getSegnom(), 'x' => 155, 'y' => 74),
            array('lb' => 'Fecha nacimiento año', 'texto' => substr($this->independiente->getFecnac(), 0, 4), 'x' => 15, 'y' => 83),
            array('lb' => 'Fecha nacimiento mes', 'texto' => substr($this->independiente->getFecnac(), 5, 2), 'x' => 31, 'y' => 83),
            array('lb' => 'Fecha nacimiento día', 'texto' => substr($this->independiente->getFecnac(), 8, 2), 'x' => 45, 'y' => 83),
            $this->posSexo(82),
            $this->posEstadoCivil(82),
            $this->posZonaResidencial(82),
            array('lb' => 'Dirección recidencia', 'texto' => $this->independiente->getDireccion(), 'x' => 10, 'y' => 91),
            array('lb' => 'Barrio', 'texto' => $this->independiente->getBarrio(), 'x' => 70, 'y' => 91),
            array('lb' => 'Ciudad', 'texto' => capitalize($ciudad), 'x' => 153, 'y' => 91),
            array('lb' => 'Resguardo', 'texto' => capitalize($resguardo), 'x' => 10, 'y' => 98),
            array('lb' => 'Pueblo indigena', 'texto' => capitalize($pueblo), 'x' => 110, 'y' => 98),
            $this->posFactorVulnera(98),
            $this->posPertenenciaEtnica(98),
            $this->posNivelEscolar(98),
            $this->posOriSexual(98),
            $this->posTipoVivienda(98),
            $this->posTieneDiscapacidad(151),
            array('lb' => 'Discapacidad', 'texto' => capitalize($discapacidad), 'x' => 136, 'y' => 151),
        );

        $this->addBloq($datos);
    }

    function dataLaboral()
    {
        $this->pdf->SetFont('helvetica', '', 8);
        $mcargos = ParamsTrabajador::getOcupaciones();
        $cargo = ($this->independiente->getCargo()) ? $mcargos[$this->independiente->getCargo()] : '';

        $datos = array(
            array('lb' => 'Fecha inicia año', 'texto' => substr($this->independiente->getFecini(), 0, 4), 'x' => 15, 'y' => 163),
            array('lb' => 'Fecha inicia mes', 'texto' => substr($this->independiente->getFecini(), 5, 2), 'x' => 31, 'y' => 163),
            array('lb' => 'Fecha inicia día', 'texto' => substr($this->independiente->getFecini(), 8, 2), 'x' => 45, 'y' => 163),
            array('lb' => 'Tipo contrato', 'texto' => 'X', 'x' => 80, 'y' => 163),
            array('lb' => 'Jornada', 'texto' => 'X', 'x' => 92, 'y' => 163),
            array('lb' => 'Salario', 'texto' => $this->independiente->getSalario(), 'x' => 135, 'y' => 163),
            array('lb' => 'Comisión', 'texto' => 'X', 'x' => 182, 'y' => 163),
            array('lb' => 'Sector agro', 'texto' => 'X', 'x' => 195, 'y' => 163),
            array('lb' => 'Direccion laboral', 'texto' => $this->independiente->getDireccion(), 'x' => 10, 'y' => 171),
            $this->posZonaLaboral(171),
            array('lb' => 'Otra empresa', 'texto' => 'X', 'x' => 25, 'y' => 179),
            array('lb' => 'Ocupación', 'texto' => substr($cargo, 0, 64), 'x' => 117, 'y' => 179),
        );
        $this->addBloq($datos);
    }

    function dataMedioPago()
    {
        $mbanco = ParamsTrabajador::getBancos();
        $banco = ($this->independiente->getCodban()) ? $mbanco[$this->independiente->getCodban()] : '';
        $nombre = capitalize($this->independiente->getNombreCompleto());
        $datos = array(
            array('lb' => 'Nombre beneficio giro', 'texto' => $nombre, 'x' => 10, 'y' => 189),
            array('lb' => 'Documento beneficio giro', 'texto' => $this->independiente->getCedtra(), 'x' => 112, 'y' => 189),
            array('lb' => 'Tipo afiliado beneficio', 'texto' => 'X', 'x' => 164, 'y' => 189),
            $this->posTipoPago(198),
            array('lb' => 'Número cuenta', 'texto' => $this->independiente->getNumcue(), 'x' => 52, 'y' => 198),
            array('lb' => 'Banco', 'texto' => capitalize($banco), 'x' => 112, 'y' => 197),
        );
        $this->addBloq($datos);
    }


    function posTipoAfiliado()
    {
        if ($this->independiente->getTipafi() == '3') {
            $x = 155;
            //2%
        } elseif ($this->independiente->getTipafi() == '65') {
            //0.6%
            $x = 150;
        } else {
            //4%
            $x = 120;
        }
        return array('lb' => 'Tipo afiliado', 'texto' => 'X', 'x' => $x, 'y' => 36);
    }

    function posTipoDocumento($y)
    {
        switch ($this->independiente->getTipdoc()) {
            case '1':
                //CEDULA
                $x = 53;
                $y = 70;
                break;
            case '4';
                //CEDULA EXTRANJERIA
                $x = 68;
                $y = 70;
                break;
            case '13';
                //VISA
                $x = 74;
                $y = 70;
                break;
            case '6';
                //PASAPORTE
                $x = 53;
                $y = 70;
                break;
            case '8':
            case '14':
            case '10':
                //PERMISO ESPECIAL PERMANECIA
                $x = 61;
                $y = 74;
                break;
            case '9':
                //CABILDOS
                $x = 67;
                $y = 74;
                break;
            case '11':
                //CARNET DIPLOMATICO
                $x = 68;
                $y = 74;
                break;
            default:
                $x = 74;
                $y = 74;
                break;
        }
        return array('lb' => 'Tipo documento', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posSexo($y)
    {
        switch ($this->independiente->getSexo()) {
            case 'M':
                $x = 62;
                break;
            case 'F':
                $x = 67;
                break;
            default:
                $x = 74;
                break;
        }
        return array('lb' => 'Sexo', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posEstadoCivil($y)
    {
        switch ($this->independiente->getEstciv()) {
            case '4':
                //union libre
                $x = 93;
                break;
            case '2':
                //casado
                $x = 105;
                break;
            case '6':
                //divorciado
                $x = 117;
                break;
            case '5':
                //separado
                $x = 128;
                break;
            case '3':
                //viudo
                $x = 139;
                break;
            default:
                //soltero
                $x = 150;
                break;
        }

        return  array('lb' => 'Estado civil', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posZonaResidencial($y)
    {
        switch ($this->independiente->getRural()) {
            case 'N':
                // RURAl NO
                $x = 181;
                break;
            case 'S':
                // RURAL SI
                $x = 188;
                break;
            default:
                $x = 181;
                break;
        }
        return array('lb' => 'Zona recidencial', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posZonaLaboral($y)
    {
        switch ($this->independiente->getRural()) {
            case 'N':
                // RURAL No
                $x = 180;
                break;
            case 'S':
                // RURAl SI
                $x = 190;
                break;
            default:
                $x = 185;
                break;
        }
        return array('lb' => 'Zona laboral', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posFactorVulnera($y)
    {
        switch ($this->independiente->getFacvul()) {
            case "1":
                //"Desplazado",
                $y += 3;
                break;
            case "2":
                //"Víctima del conflicto armado (No desplazado)",
                $y += 8;
                break;
            case "3":
                //"Desmovilizado o reinsertado",
                $y += 12;
                break;
            case "4":
                //"Hijo (as) de desmovilizados o reisertados",
                $y += 17;
                break;
            case "5":
                //"Damnificado desastre natural",
                $y += 23;
                break;
            case "6":
                //"Cabeza de familia",
                $y += 28;
                break;
            case "7":
                //"Hijo (as) de madres cabeza de familia",
                $y += 33;
                break;
            case "8":
                //"En condición de discapacidad",
                $y += 38;
                break;
            case "9":
                //"Población migrante",
                $y += 43;
                break;
            case "10":
                //"Población zonas frontera (Nacionales)",
                $y += 48;
                break;
            case "11":
                //"Ejercicio del trabajo sexual",
                $y += 53;
                break;
            default:
                //"No aplica",
                //"No disponible"
                $y += 58;
                break;
        }

        return array('lb' => 'Factor vulnerabilidad', 'texto' => 'X', 'x' => 10, 'y' => $y);
    }

    function posPertenenciaEtnica($y)
    {
        switch ($this->independiente->getPeretn()) {
            case "1":
                //"Afrocolombiano",
                $y += 4;
                break;
            case "2":
                //"Comunidad negra",
                $y += 9;
                break;
            case "3":
                //"Indígena",
                $y += 14;
                break;
            case "4":
                //"Palanquero",
                $y += 19;
                break;
            case "5":
                //"Raizal del archipiélago de San Andrés, Providencia",
                $y += 24;
                break;
            case "6":
                //"Room/gitano",
                $y += 28;
                break;
            case "7":
                //"No se auto reconoce en ninguno de los anteriores",
                $y += 33;
                break;
            default:
                //"No Disponible"
                $y += 37;
                break;
        }
        return array('lb' => 'Pertenencia etnica', 'texto' => 'X', 'x' => 65, 'y' => $y);
    }

    function posNivelEscolar($y)
    {
        switch ($this->independiente->getNivedu()) {
            case "1":
                //"PREESCOLAR",
                $y += 4;
                $x = 128;
                break;
            case "2":
                //"BASICA",
                $y += 9;
                $x = 128;
                break;
            case "3":
                //"SECUNDARIA",
                $y += 14;
                $x = 128;
                break;
            case "4":
                //"MEDIA",
                $y += 19;
                $x = 128;
                break;
            case "6":
                //"BÁSICA ADULTOS",
                $y += 24;
                $x = 128;
                break;
            case "7":
                //"SECUNDARIA ADULTO",
                $y += 29;
                $x = 128;
                break;
            case "8":
                //"MEDIA ADULTO",
                $y += 34;
                $x = 128;
                break;
            case "10":
                //"TECNICO/TEGNOLOGO",
                $y += 39;
                $x = 128;
                break;
            case "11":
                //"UNIVERSITARIO",
                $y += 4;
                $x = 165;
                break;
            case "12":
                //"POSGRADO/MAESTRÍA",
                $y += 9;
                $x = 165;
                break;
            case "13":
                //"NINGUNO",
                $y += 14;
                $x = 165;
                break;
            case "14":
                //"INFORMACION NO DISPONIBLE"
                $y += 19;
                $x = 165;
                break;
        }
        return array('lb' => 'Nivel escolaridad', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posOriSexual($y)
    {
        switch ($this->independiente->getOrisex()) {
            case "1":
                //"Heterosexual",
                $y += 38;
                break;
            case "2":
                //"Homosexual",
                $y += 42;
                break;
            case "3":
                //"Bisexual",
                $y += 46;
                break;
            case "4":
                //"Información no disponible"
                $y += 50;
                break;
        }
        return array('lb' => 'Orientacion sexual', 'texto' => 'X', 'x' => 65, 'y' => $y);
    }

    function posTipoVivienda()
    {
        switch ($this->independiente->getVivienda()) {
            case "P":
                //"PROPIA",
                $x = 128;
                $y = 155;
                break;
            case "F":
                //"FAMILIAR",
                $x = 165;
                $y = 155;
                break;
            case "A":
                //"ARRENDADA",
                $x = 128;
                $y = 159;

                break;
            case "H":
                //"HIPOTECA"
                $x = 165;
                $y = 159;
                break;
            default:
                $x = 128;
                $y = 159;
                break;
        }
        return array('lb' => 'Tipo vivienda', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posTieneDiscapacidad($y)
    {
        if ($this->independiente->getTipdis() == null || $this->independiente->getTipdis() == '00') {
            $x = 183;
        } else {
            $x = 174;
        }
        return array('lb' => 'Discapacidad', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posTipoPago($y)
    {
        switch ($this->independiente->getTippag()) {
            case 'D':
                $x = 20;
                break;
            case 'A':
                $x = 43;
                break;
            default:
                $x = null;
                break;
        }
        return array('lb' => 'Tipo medio pago', 'texto' => ($x) ? 'X' : '', 'x' => $x, 'y' => $y);
    }
}
