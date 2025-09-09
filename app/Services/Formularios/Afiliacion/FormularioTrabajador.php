<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsTrabajador;
use App\Services\Formularios\Documento;

class FormularioTrabajador extends Documento
{

    /**
     * trabajador variable
     * @var Mercurio31
     */
    private $trabajador;

    /**
     * empresa variable
     * @var Mercurio30
     */
    private $empresa;

    /**
     * main function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('empresa')) {
            throw new DebugException("Error la empresa no esté disponible", 501);
        }

        if (!$this->request->getParam('trabajador')) {
            throw new DebugException("Error el trabajador no esté disponible", 501);
        }

        $this->empresa = $this->request->getParam('empresa');
        $this->trabajador = $this->request->getParam('trabajador');

        $this->pdf->SetTitle("Formulario afiliación del trabajador {$this->trabajador->getCedtra()}, COMFACA");
        // Desactivar salto automático de página para evitar páginas extra
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->pdf->SetAuthor("{$this->trabajador->getPriape()} {$this->trabajador->getSegape()} {$this->trabajador->getPrinom()} {$this->trabajador->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Formulario de afiliación a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $this->tipoAfiliado();
        $this->dataEmpleador();
        $this->dataTrabajador();
        $this->dataLaboral(163);
        if ($this->trabajador->getTippag() != 'T') {
            $this->dataMedioPago(170);
        }
        $this->autorizaDatos(251);

        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
        return $this;
    }

    function autorizaDatos($y)
    {
        if ($this->trabajador->getAutoriza() == 'S') {
            $x = 70;
        } else {
            $x = 82;
        }
        $this->addBloq(
            array(
                array('lb' => 'Banco', 'texto' => 'X', 'x' => $x, 'y' => $y),
            )
        );
    }

    function dataLaboral($y)
    {
        $this->pdf->SetFont('helvetica', '', 8);
        $mcargos = ParamsTrabajador::getOcupaciones();
        $cargo = ($this->trabajador->getCargo()) ? $mcargos[$this->trabajador->getCargo()] : '';

        $datos = [
            ['lb' => 'Fecha inicia año', 'texto' => substr($this->trabajador->getFecing(), 0, 4), 'x' => 15, 'y' => $y],
            ['lb' => 'Fecha inicia mes', 'texto' => substr($this->trabajador->getFecing(), 5, 2), 'x' => 31, 'y' => $y],
            ['lb' => 'Fecha inicia día', 'texto' => substr($this->trabajador->getFecing(), 8, 2), 'x' => 45, 'y' => $y],
            ['lb' => 'Tipo contrato', 'texto' => 'X', 'x' => 80, 'y' => $y],
            ['lb' => 'Jornada', 'texto' => 'X', 'x' => 92, 'y' => $y],
            ['lb' => 'Salario', 'texto' => $this->trabajador->getSalario(), 'x' => 135, 'y' => $y],
            ['lb' => 'Comisión', 'texto' => 'X', 'x' => 182, 'y' => $y],
            ['lb' => 'Sector agro', 'texto' => 'X', 'x' => 195, 'y' => $y],
            ['lb' => 'Direccion laboral', 'texto' => $this->trabajador->getDireccion(), 'x' => 10, 'y' => $y + 8],
            $this->posZonaLaboral($y + 10),
            ['lb' => 'Otra empresa', 'texto' => 'X', 'x' => 25, 'y' => $y + 14],
            ['lb' => 'Ocupación', 'texto' => $cargo, 'x' => 117, 'y' => $y + 14]
        ];
        $this->addBloq($datos);
    }

    function dataMedioPago($y)
    {
        $mbanco = ParamsTrabajador::getBancos();
        $banco = ($this->trabajador->getCodban()) ? $mbanco[$this->trabajador->getCodban()] : '';
        $nombre = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());
        $datos = [
            ['lb' => 'Nombre beneficio giro', 'texto' => $nombre, 'x' => 10, 'y' => $y],
            ['lb' => 'Documento beneficio giro', 'texto' => $this->trabajador->getCedtra(), 'x' => 112, 'y' => $y],
            ['lb' => 'Tipo afiliado beneficio', 'texto' => 'X', 'x' => 164, 'y' => $y],
            $this->posTipoPago(),
            ['lb' => 'Número cuenta', 'texto' => $this->trabajador->getNumcue(), 'x' => 52, 'y' => $y + 8],
            ['lb' => 'Banco', 'texto' => capitalize($banco), 'x' => 112, 'y' => $y + 8],
        ];
        $this->addBloq($datos);
    }

    function posZonaLaboral($y)
    {
        switch ($this->trabajador->getRural()) {
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


    function posTipoPago()
    {
        switch ($this->trabajador->getTippag()) {
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

    function tipoAfiliado()
    {
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = array(
            array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 38, 'y' => 36),
            $this->posTipoAfiliado()
        );
        $this->addBloq($datos);
    }

    function posTipoAfiliado()
    {
        if ($this->trabajador->getTipafi() == '3') {
            $x = 150;
            //2%
        } elseif ($this->trabajador->getTipafi() == '65') {
            //0.6%
            $x = 155;
        } else {
            //4%
            $x = 120;
        }
        return array('lb' => 'Tipo afiliado', 'texto' => 'X', 'x' => $x, 'y' => 36);
    }

    function dataEmpleador()
    {
        $this->pdf->SetFont('helvetica', '', 7);
        $razon_social = capitalize($this->empresa->getRazsoc());
        $datos = array(
            array('lb' => 'Razon social', 'texto' => $razon_social, 'x' => 9, 'y' => 47),
            array('lb' => 'nit', 'texto' => $this->empresa->getNit(), 'x' => 158, 'y' => 47),
            array('lb' => 'Sucursal', 'texto' => $razon_social, 'x' => 9, 'y' => 55),
            array('lb' => 'Telefono', 'texto' => $this->empresa->getTelefono(), 'x' => 150, 'y' => 55),
        );
        $this->addBloq($datos);
    }

    function dataTrabajador()
    {
        $mciudad = ParamsTrabajador::getCiudades();
        $ciudad = ($this->trabajador->getCodciu()) ? $mciudad[$this->trabajador->getCodciu()] : ' FLORENCIA';

        $mresguardos = ParamsTrabajador::getResguardos();
        $resguardo = ($this->trabajador->getResguardo_id()) ? $mresguardos[$this->trabajador->getResguardo_id()] : 'NO APLICA';

        $mpueblos = ParamsTrabajador::getPueblosIndigenas();
        $pueblo = ($this->trabajador->getPub_indigena_id()) ? $mpueblos[$this->trabajador->getPub_indigena_id()] : 'NO APLICA';

        $discapacidad = '';
        if ($tipdis = $this->trabajador->getTipdis()) {
            $mtidis = ParamsTrabajador::getTipoDiscapacidad();
            $discapacidad = $mtidis[$tipdis];
        }

        $this->pdf->SetFont('helvetica', '', 8);
        $datos = array(
            array('lb' => 'Cedula trabajador', 'texto' => $this->trabajador->getCedtra(), 'x' => 10, 'y' => 67),
            $this->posTipoDocumento(),
            array('lb' => 'Celular', 'texto' => $this->trabajador->getTelefono(), 'x' => 82, 'y' => 67),
            array('lb' => 'Email', 'texto' => $this->trabajador->getEmail(), 'x' => 125, 'y' => 67),
            array('lb' => 'Primer apellido', 'texto' => $this->trabajador->getPriape(), 'x' => 10, 'y' => 75),
            array('lb' => 'Segundo apellido', 'texto' => $this->trabajador->getSegape(), 'x' => 54, 'y' => 75),
            array('lb' => 'Primer nombre', 'texto' => $this->trabajador->getPrinom(), 'x' => 105, 'y' => 75),
            array('lb' => 'Segundo nombre', 'texto' => $this->trabajador->getSegnom(), 'x' => 155, 'y' => 75),
            array('lb' => 'Fecha nacimiento año', 'texto' => substr($this->trabajador->getFecnac(), 0, 4), 'x' => 15, 'y' => 83),
            array('lb' => 'Fecha nacimiento mes', 'texto' => substr($this->trabajador->getFecnac(), 5, 2), 'x' => 31, 'y' => 83),
            array('lb' => 'Fecha nacimiento día', 'texto' => substr($this->trabajador->getFecnac(), 8, 2), 'x' => 45, 'y' => 83),
            $this->posSexo(83),
            $this->posEstadoCivil(83),
            $this->posZonaResidencial(83),
            array('lb' => 'Dirección recidencia', 'texto' => $this->trabajador->getDireccion(), 'x' => 10, 'y' => 90),
            array('lb' => 'Barrio', 'texto' => $this->trabajador->getBarrio(), 'x' => 118, 'y' => 90),
            array('lb' => 'Ciudad', 'texto' => capitalize($ciudad), 'x' => 153, 'y' => 90),
            array('lb' => 'Resguardo', 'texto' => capitalize($resguardo), 'x' => 10, 'y' => 98),
            array('lb' => 'Pueblo indigena', 'texto' => capitalize($pueblo), 'x' => 110, 'y' => 98),
            $this->posFactorVulnera(104),
            $this->posPertenenciaEtnica(104),
            $this->posNivelEscolar(104),
            $this->posOriSexual(135),
            $this->posTipoVivienda(),
            $this->posTieneDiscapacidad(147),
            array('lb' => 'Discapacidad', 'texto' => capitalize($discapacidad), 'x' => 136, 'y' => 151),
        );

        $this->addBloq($datos);
    }

    function posTieneDiscapacidad($y)
    {
        if ($this->trabajador->getTipdis() == null || $this->trabajador->getTipdis() == '00') {
            $x = 183;
        } else {
            $x = 174;
        }
        return array('lb' => 'Discapacidad', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posTipoVivienda()
    {
        switch ($this->trabajador->getVivienda()) {
            case "P":
                //"PROPIA",
                $x = 128;
                $y = 150;
                break;
            case "F":
                //"FAMILIAR",
                $x = 165;
                $y = 150;
                break;
            case "A":
                //"ARRENDADA",
                $x = 128;
                $y = 154;

                break;
            case "H":
                //"HIPOTECA"
                $x = 165;
                $y = 152;
                break;
            default:
                $x = 128;
                $y = 154;
                break;
        }
        return array('lb' => 'Tipo vivienda', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posOriSexual($y)
    {
        switch ($this->trabajador->getOrisex()) {
            case "1":
                //"Heterosexual",
                $y += 5;
                break;
            case "2":
                //"Homosexual",
                $y += 10;
                break;
            case "3":
                //"Bisexual",
                $y += 15;
                break;
            case "4":
                //"Información no disponible"
                $y += 20;
                break;
        }
        return array('lb' => 'Orientacion sexual', 'texto' => 'X', 'x' => 65, 'y' => $y);
    }

    function posNivelEscolar($y)
    {
        switch ($this->trabajador->getNivedu()) {
            case "1":
                //"PREESCOLAR",
                $y += 1;
                $x = 128;
                break;
            case "2":
                //"BASICA",
                $y += 4;
                $x = 128;
                break;
            case "3":
                //"SECUNDARIA",
                $y += 9;
                $x = 128;
                break;
            case "4":
                //"MEDIA",
                $y += 14;
                $x = 128;
                break;
            case "6":
                //"BÁSICA ADULTOS",
                $y += 19;
                $x = 128;
                break;
            case "7":
                //"SECUNDARIA ADULTO",
                $y += 24;
                $x = 128;
                break;
            case "8":
                //"MEDIA ADULTO",
                $y += 29;
                $x = 128;
                break;
            case "10":
                //"TECNICO/TEGNOLOGO",
                $y += 34;
                $x = 128;
                break;
            case "11":
                //"UNIVERSITARIO",
                $y += 39;
                $x = 165;
                break;
            case "12":
                //"POSGRADO/MAESTRÍA",
                $y += 44;
                $x = 165;
                break;
            case "13":
                //"NINGUNO",
                $y += 49;
                $x = 165;
                break;
            case "14":
                //"INFORMACION NO DISPONIBLE"
                $y += 54;
                $x = 165;
                break;
        }
        return array('lb' => 'Nivel escolaridad', 'texto' => 'X', 'x' => $x, 'y' => $y);
    }

    function posPertenenciaEtnica($y)
    {
        switch ($this->trabajador->getPeretn()) {
            case "1":
                //"Afrocolombiano",
                $y += 3;
                break;
            case "2":
                //"Comunidad negra",
                $y += 8;
                break;
            case "3":
                //"Indígena",
                $y += 12;
                break;
            case "4":
                //"Palanquero",
                $y += 16;
                break;
            case "5":
                //"Raizal del archipiélago de San Andrés, Providencia",
                $y += 20;
                break;
            case "6":
                //"Room/gitano",
                $y += 24;
                break;
            case "7":
                //"No se auto reconoce en ninguno de los anteriores",
                $y += 28;
                break;
            default:
                //"No Disponible"
                $y += 32;
                break;
        }
        return array('lb' => 'Pertenencia etnica', 'texto' => 'X', 'x' => 65, 'y' => $y);
    }

    function posFactorVulnera($y)
    {
        switch ($this->trabajador->getFacvul()) {
            case "1":
                //"Desplazado",
                $y += 1;
                break;
            case "2":
                //"Víctima del conflicto armado (No desplazado)",
                $y += 5;
                break;
            case "3":
                //"Desmovilizado o reinsertado",
                $y += 10;
                break;
            case "4":
                //"Hijo (as) de desmovilizados o reisertados",
                $y += 14;
                break;
            case "5":
                //"Damnificado desastre natural",
                $y += 18;
                break;
            case "6":
                //"Cabeza de familia",
                $y += 23;
                break;
            case "7":
                //"Hijo (as) de madres cabeza de familia",
                $y += 27;
                break;
            case "8":
                //"En condición de discapacidad",
                $y += 31;
                break;
            case "9":
                //"Población migrante",
                $y += 35;
                break;
            case "10":
                //"Población zonas frontera (Nacionales)",
                $y += 38;
                break;
            case "11":
                //"Ejercicio del trabajo sexual",
                $y += 43;
                break;
            default:
                //"No aplica",
                //"No disponible"
                $y += 45;
                break;
        }

        return array('lb' => 'Factor vulnerabilidad', 'texto' => 'X', 'x' => 10, 'y' => $y);
    }

    function posZonaResidencial($y)
    {
        switch ($this->trabajador->getRural()) {
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

    function posEstadoCivil($y)
    {
        switch ($this->trabajador->getEstciv()) {
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

    function posSexo($y)
    {
        switch ($this->trabajador->getSexo()) {
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

    function posTipoDocumento()
    {
        switch ($this->trabajador->getTipdoc()) {
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
        return array('lb' => 'Tipo documento', 'texto' => 'X', 'x' => $x, 'y' => $y - 6);
    }

    function bloqueEmpresa()
    {
        $zonas = ParamsTrabajador::getZonas();
        $this->pdf->setY(43 - 6);
        $this->pdf->setX(1);
        $this->pdf->Cell(105, 4, $this->empresa->getRazsoc(), 0, 0, 'C');
        $this->pdf->setX(155);
        $this->pdf->Cell(32, 4, $this->empresa->getNit(), 0, 0, 'C');
        $this->pdf->setY(48.5 - 6);
        $this->pdf->setX(20);
        $this->pdf->Cell(3, 4, $this->trabajador->getCodsuc(), 0, 0, 'L');
        $this->pdf->setY(48.8 - 6);
        $this->pdf->setX(60);
        $this->pdf->Cell(3, 4, $this->empresa->getTelefono(), 0, 0, 'L');
        $this->pdf->setY(48.5 - 6);
        $this->pdf->setX(110);
        if ($this->empresa->getCodzon()) {
            $this->pdf->Cell(3, 4, $zonas["{$this->empresa->getCodzon()}"], 0, 0, 'L');
        }
        $this->pdf->SetFont('helvetica', '', 6);
        $this->pdf->setY(48.8 - 6);
        $this->pdf->setX(160);
        $this->pdf->Cell(3, 4, $this->empresa->getEmail(), 0, 0, 'L');
        return $this->pdf;
    }
}
