<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsFacultativo;
use App\Library\Collections\ParamsTrabajador;
use App\Services\Formularios\Documento;

class FormularioFacultativo extends Documento
{
    /**
     * facultativo variable
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
            throw new DebugException("Error la empresa no esté disponible", 501);
        }
        $this->facultativo = $this->request->getParam('facultativo');

        $this->pdf->SetTitle("Formulario afiliación del facultativo {$this->facultativo->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->facultativo->getPriape()} {$this->facultativo->getSegape()} {$this->facultativo->getPrinom()} {$this->facultativo->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Formulario de afiliación a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $this->pdf->SetAutoPageBreak(false, 0);
        $this->tipoAfiliado();
        $this->dataEmpleador();
        $this->dataTrabajador();
        $this->dataLaboral();
        $this->dataMedioPago();
        $this->addBloq(
            array(
                array('lb' => 'Autoriza datos', 'texto' => 'X', 'x' => 70, 'y' => 277),
            )
        );

        $page = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($page, 160, 275, 30, 20, '');
        return $this;
    }

    function tipoAfiliado()
    {
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 38, 'y' => 42),
            $this->posTipoAfiliado()
        );
        $this->addBloq($datos);
    }

    function dataEmpleador()
    {
        $this->pdf->SetFont('helvetica', '', 7);
        $razon_social = capitalize($this->facultativo->getPriape() . ' ' . $this->facultativo->getSegape() . ' ' . $this->facultativo->getPrinom() . ' ' . $this->facultativo->getSegnom());
        $datos = array(
            array('lb' => 'Razon social', 'texto' => $razon_social, 'x' => 9, 'y' => 54),
            array('lb' => 'nit', 'texto' => $this->facultativo->getCedtra(), 'x' => 158, 'y' => 54),
            array('lb' => 'Sucursal', 'texto' => $razon_social, 'x' => 9, 'y' => 62),
            array('lb' => 'Telefono', 'texto' => $this->facultativo->getTelefono(), 'x' => 170, 'y' => 62),
        );
        $this->addBloq($datos);
    }

    function dataTrabajador()
    {
        $mciudad = ParamsFacultativo::getCiudades();
        $ciudad = ($this->facultativo->getCodciu()) ? $mciudad[$this->facultativo->getCodciu()] : ' FLORENCIA';

        $mresguardos = ParamsTrabajador::getResguardos();
        $resguardo = ($this->facultativo->getResguardo_id()) ? $mresguardos[$this->facultativo->getResguardo_id()] : 'NO APLICA';

        $mpueblos = ParamsTrabajador::getPueblosIndigenas();
        $pueblo = ($this->facultativo->getPub_indigena_id()) ? $mpueblos[$this->facultativo->getPub_indigena_id()] : 'NO APLICA';

        $discapacidad = '';
        if ($tipdis = $this->facultativo->getTipdis()) {
            $mtidis = ParamsTrabajador::getTipoDiscapacidad();
            $discapacidad = $mtidis[$tipdis];
        }

        $this->pdf->SetFont('helvetica', '', 8);
        $datos = array(
            array('lb' => 'Cedula trabajador', 'texto' => $this->facultativo->getCedtra(), 'x' => 10, 'y' => 76),
            $this->posTipoDocumento(),
            array('lb' => 'Celular', 'texto' => $this->facultativo->getTelefono(), 'x' => 82, 'y' => 76),
            array('lb' => 'Email', 'texto' => $this->facultativo->getEmail(), 'x' => 125, 'y' => 76),
            array('lb' => 'Primer apellido', 'texto' => $this->facultativo->getPriape(), 'x' => 10, 'y' => 83),
            array('lb' => 'Segundo apellido', 'texto' => $this->facultativo->getSegape(), 'x' => 54, 'y' => 83),
            array('lb' => 'Primer nombre', 'texto' => $this->facultativo->getPrinom(), 'x' => 105, 'y' => 83),
            array('lb' => 'Segundo nombre', 'texto' => $this->facultativo->getSegnom(), 'x' => 155, 'y' => 83),
            array('lb' => 'Fecha nacimiento año', 'texto' => substr($this->facultativo->getFecnac(), 0, 4), 'x' => 15, 'y' => 92),
            array('lb' => 'Fecha nacimiento mes', 'texto' => substr($this->facultativo->getFecnac(), 5, 2), 'x' => 31, 'y' => 92),
            array('lb' => 'Fecha nacimiento día', 'texto' => substr($this->facultativo->getFecnac(), 8, 2), 'x' => 45, 'y' => 92),
            $this->posSexo(),
            $this->posEstadoCivil(),
            $this->posZonaResidencial(),
            array('lb' => 'Dirección recidencia', 'texto' => $this->facultativo->getDireccion(), 'x' => 10, 'y' => 100),
            array('lb' => 'Barrio', 'texto' => $this->facultativo->getBarrio(), 'x' => 70, 'y' => 100),
            array('lb' => 'Ciudad', 'texto' => capitalize($ciudad), 'x' => 153, 'y' => 100),
            array('lb' => 'Resguardo', 'texto' => capitalize($resguardo), 'x' => 10, 'y' => 109),
            array('lb' => 'Pueblo indigena', 'texto' => capitalize($pueblo), 'x' => 110, 'y' => 109),
            $this->posFactorVulnera(),
            $this->posPertenenciaEtnica(),
            $this->posNivelEscolar(),
            $this->posOriSexual(),
            $this->posTipoVivienda(),
            $this->posTieneDiscapacidad(),
            array('lb' => 'Discapacidad', 'texto' => capitalize($discapacidad), 'x' => 136, 'y' => 167),
        );

        $this->addBloq($datos);
    }

    function dataLaboral()
    {
        $this->pdf->SetFont('helvetica', '', 8);
        $mcargos = ParamsTrabajador::getOcupaciones();
        $cargo = ($this->facultativo->getCargo()) ? $mcargos[$this->facultativo->getCargo()] : '';

        $datos = array(
            array('lb' => 'Fecha inicia año', 'texto' => substr($this->facultativo->getFecini(), 0, 4), 'x' => 15, 'y' => 180),
            array('lb' => 'Fecha inicia mes', 'texto' => substr($this->facultativo->getFecini(), 5, 2), 'x' => 31, 'y' => 180),
            array('lb' => 'Fecha inicia día', 'texto' => substr($this->facultativo->getFecini(), 8, 2), 'x' => 45, 'y' => 180),
            array('lb' => 'Tipo contrato', 'texto' => 'X', 'x' => 80, 'y' => 180),
            array('lb' => 'Jornada', 'texto' => 'X', 'x' => 92, 'y' => 180),
            array('lb' => 'Salario', 'texto' => $this->facultativo->getSalario(), 'x' => 135, 'y' => 180),
            array('lb' => 'Comisión', 'texto' => 'X', 'x' => 182, 'y' => 180),
            array('lb' => 'Sector agro', 'texto' => 'X', 'x' => 195, 'y' => 180),
            array('lb' => 'Direccion laboral', 'texto' => $this->facultativo->getDireccion(), 'x' => 10, 'y' => 188),
            $this->posZonaLaboral(),
            array('lb' => 'Otra empresa', 'texto' => 'X', 'x' => 25, 'y' => 197),
            array('lb' => 'Ocupación', 'texto' => $cargo, 'x' => 117, 'y' => 197),
        );
        $this->addBloq($datos);
    }

    function dataMedioPago()
    {
        $mbanco = ParamsTrabajador::getBancos();
        $banco = ($this->facultativo->getCodban()) ? $mbanco[$this->facultativo->getCodban()] : '';
        $nombre = capitalize($this->facultativo->getPrinom() . ' ' . $this->facultativo->getSegnom() . ' ' . $this->facultativo->getPriape() . ' ' . $this->facultativo->getSegape());
        $datos = array(
            array('lb' => 'Nombre beneficio giro', 'texto' => $nombre, 'x' => 10, 'y' => 209),
            array('lb' => 'Documento beneficio giro', 'texto' => $this->facultativo->getCedtra(), 'x' => 112, 'y' => 209),
            array('lb' => 'Tipo afiliado beneficio', 'texto' => 'X', 'x' => 164, 'y' => 209),
            $this->posTipoPago(),
            array('lb' => 'Número cuenta', 'texto' => $this->facultativo->getNumcue(), 'x' => 52, 'y' => 218),
            array('lb' => 'Banco', 'texto' => capitalize($banco), 'x' => 112, 'y' => 218),
        );
        $this->addBloq($datos);
    }


    function posTipoAfiliado()
    {
        if (!$this->facultativo->getTipafi() == '63') {
            //2%
            $x = 156;
            $y = 36;
        } else {
            //4%
            $x = 149;
            $y = 36;
        }
        return array('lb' => 'Tipo afiliado', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posTipoDocumento()
    {
        switch ($this->facultativo->getTipdoc()) {
            case '1':
                //CEDULA
                $x = 53;
                $y = 71;
                break;
            case '4';
                //CEDULA EXTRANJERIA
                $x = 68;
                $y = 71;
                break;
            case '13';
                //VISA
                $x = 74;
                $y = 71;
                break;
            case '6';
                //PASAPORTE
                $x = 53;
                $y = 76;
                break;
            case '8':
            case '14':
            case '10':
                //PERMISO ESPECIAL PERMANECIA
                $x = 61;
                $y = 76;
                break;
            case '9':
                //CABILDOS
                $x = 67;
                $y = 76;
                break;
            case '11':
                //CARNET DIPLOMATICO
                $x = 68;
                $y = 76;
                break;
            default:
                $x = 74;
                $y = 76;
                break;
        }
        return array('lb' => 'Tipo documento', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posSexo()
    {
        switch ($this->facultativo->getSexo()) {
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
        return array('lb' => 'Sexo', 'texto' => 'X', 'x' => $x, 'y' => 92);
    }

    function posEstadoCivil()
    {
        switch ($this->facultativo->getEstciv()) {
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

        return  array('lb' => 'Estado civil', 'texto' => 'X', 'x' => $x, 'y' => 92);
    }

    function posZonaResidencial()
    {
        switch ($this->facultativo->getRural()) {
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
        return array('lb' => 'Zona recidencial', 'texto' => 'X', 'x' => $x, 'y' => 90.5);
    }

    function posZonaLaboral()
    {
        switch ($this->facultativo->getRural()) {
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
        return array('lb' => 'Zona laboral', 'texto' => 'X', 'x' => $x, 'y' => 188);
    }

    function posFactorVulnera()
    {
        switch ($this->facultativo->getFacvul()) {
            case "1":
                //"Desplazado",
                $y = 118;
                break;
            case "2":
                //"Víctima del conflicto armado (No desplazado)",
                $y = 122;
                break;
            case "3":
                //"Desmovilizado o reinsertado",
                $y = 126;
                break;
            case "4":
                //"Hijo (as) de desmovilizados o reisertados",
                $y = 130;
                break;
            case "5":
                //"Damnificado desastre natural",
                $y = 134;
                break;
            case "6":
                //"Cabeza de familia",
                $y = 138;
                break;
            case "7":
                //"Hijo (as) de madres cabeza de familia",
                $y = 142;
                break;
            case "8":
                //"En condición de discapacidad",
                $y = 146;
                break;
            case "9":
                //"Población migrante",
                $y = 150;
                break;
            case "10":
                //"Población zonas frontera (Nacionales)",
                $y = 154;
                break;
            case "11":
                //"Ejercicio del trabajo sexual",
                $y = 159;
                break;
            default:
                //"No aplica",
                //"No disponible"
                $y = 163;
                break;
        }

        return array('lb' => 'Factor vulnerabilidad', 'texto' => 'X', 'x' => 10, 'y' => $y);
    }

    function posPertenenciaEtnica()
    {
        switch ($this->facultativo->getPeretn()) {
            case "1":
                //"Afrocolombiano",
                $y = 118;
                break;
            case "2":
                //"Comunidad negra",
                $y = 122;
                break;
            case "3":
                //"Indígena",
                $y = 126;
                break;
            case "4":
                //"Palanquero",
                $y = 130;
                break;
            case "5":
                //"Raizal del archipiélago de San Andrés, Providencia",
                $y = 134;
                break;
            case "6":
                //"Room/gitano",
                $y = 138;
                break;
            case "7":
                //"No se auto reconoce en ninguno de los anteriores",
                $y = 142;
                break;
            default:
                //"No Disponible"
                $y = 146;
                break;
        }
        return array('lb' => 'Pertenencia etnica', 'texto' => 'X', 'x' => 65, 'y' => $y);
    }

    function posNivelEscolar()
    {
        switch ($this->facultativo->getNivedu()) {
            case "1":
                //"PREESCOLAR",
                $y = 118;
                $x = 128;
                break;
            case "2":
                //"BASICA",
                $y = 122;
                $x = 128;
                break;
            case "3":
                //"SECUNDARIA",
                $y = 126;
                $x = 128;
                break;
            case "4":
                //"MEDIA",
                $y = 130;
                $x = 128;
                break;
            case "6":
                //"BÁSICA ADULTOS",
                $y = 134;
                $x = 128;
                break;
            case "7":
                //"SECUNDARIA ADULTO",
                $y = 138;
                $x = 128;
                break;
            case "8":
                //"MEDIA ADULTO",
                $y = 142;
                $x = 128;
                break;
            case "10":
                //"TECNICO/TEGNOLOGO",
                $y = 146;
                $x = 128;
                break;
            case "11":
                //"UNIVERSITARIO",
                $y = 118;
                $x = 165;
                break;
            case "12":
                //"POSGRADO/MAESTRÍA",
                $y = 122;
                $x = 165;
                break;
            case "13":
                //"NINGUNO",
                $y = 138;
                $x = 165;
                break;
            case "14":
                //"INFORMACION NO DISPONIBLE"
                $y = 146;
                $x = 165;
                break;
        }
        return array('lb' => 'Nivel escolaridad', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posOriSexual()
    {
        switch ($this->facultativo->getOrisex()) {
            case "2":
                //"Homosexual",
                $y = 158;
                break;
            case "3":
                //"Bisexual",
                $y = 162;
                break;
            case "4":
                //"Información no disponible"
                $y = 167;
                break;
            default:
                //"Heterosexual",
                $y = 155;
                break;
        }
        return array('lb' => 'Orientacion sexual', 'texto' => 'X', 'x' => 65, 'y' => $y);
    }

    function posTipoVivienda()
    {
        switch ($this->facultativo->getVivienda()) {
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

    function posTieneDiscapacidad()
    {
        if ($this->facultativo->getTipdis() == null || $this->facultativo->getTipdis() == '00') {
            $x = 183;
        } else {
            $x = 174;
        }
        return array('lb' => 'Discapacidad', 'texto' => 'X', 'x' => $x, 'y' => 163);
    }

    function posTipoPago()
    {
        switch ($this->facultativo->getTippag()) {
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
        return array('lb' => 'Tipo medio pago', 'texto' => ($x) ? 'X' : '', 'x' => $x, 'y' => 218);
    }
}
