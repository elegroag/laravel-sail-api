<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsConyuge;
use App\Models\Gener18;
use App\Services\Formularios\Documento;

class FormularioConyuge extends Documento
{
    /**
     * conyuge variable
     * @var Mercurio32
     */
    private $conyuge;

    /**
     * trabajador variable
     * @var Mercurio31
     */
    private $trabajador;

    /**
     * main function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('conyuge')) {
            throw new DebugException("Error la empresa no esté disponible", 501);
        }
        $this->conyuge = $this->request->getParam('conyuge');
        $this->trabajador = $this->request->getParam('trabajador');

        $this->pdf->SetTitle("Formulario adición del cónyuge {$this->trabajador->getCedtra()}, COMFACA");
        $this->pdf->SetAuthor("{$this->trabajador->getPriape()} {$this->trabajador->getSegape()} {$this->trabajador->getPrinom()} {$this->trabajador->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Formulario de adición a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $page1 = storage_path('public/docs/form/conyuge/formulario_adicion_conyuge.png');
        $this->pdf->Image($page1, 0, 0, 210, 297);
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->headerForm();
        $this->dataTrabajador();
        $this->dataConyuge();
        $this->dataMedioPago();
        $this->addBloq(
            array(
                array('lb' => 'Acepta politica', 'texto' => 'X', 'x' => 168, 'y' => 263.5)
            )
        );
        $page = storage_path('public/docs/sello-firma.png');
        $this->pdf->Image($page, 160, 275, 30, 20, '');
        return $this;
    }

    public function headerForm()
    {
        $_codciu = ParamsConyuge::getCiudades();
        $ciudad = ($this->conyuge->getCodzon()) ? $_codciu[$this->conyuge->getCodzon()] : 'Florencia';
        $today = Carbon::now();
        $this->pdf->SetFont('helvetica', '', 8.6);
        $datos = array(
            array('lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 116, 'y' => 25),
            array('lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 130, 'y' => 25),
            array('lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 140, 'y' => 25),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 150, 'y' => 25),
        );
        $this->addBloq($datos);
    }

    public function dataTrabajador()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de ciudadania';

        $nombtra = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            array('lb' => 'Adicion personas', 'texto' => 'X', 'x' => 45, 'y' => 52),
            array('lb' => 'Cedula trabajador', 'texto' => $this->trabajador->getCedtra(), 'x' => 11, 'y' => 74),
            array('lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 35, 'y' => 74),
            array('lb' => 'Nombre trabajador', 'texto' => substr($nombtra, 0, 50), 'x' => 93, 'y' => 74),
            array('lb' => 'NIT', 'texto' => $this->trabajador->getNit(), 'x' => 175, 'y' => 74),
        );

        $this->addBloq($datos);
    }

    function dataConyuge()
    {
        $mciudad = ParamsConyuge::getCiudades();
        $ciudad = ($this->conyuge->getCodzon()) ? $mciudad[$this->conyuge->getCodzon()] : ' FLORENCIA';

        $mresguardos = ParamsConyuge::getResguardos();
        $resguardo = ($this->conyuge->getResguardo_id()) ? $mresguardos[$this->conyuge->getResguardo_id()] : 'NO APLICA';

        $metnica =  ParamsConyuge::getPertenenciaEtnicas();
        $etnica = ($this->conyuge->getPeretn()) ? $metnica[$this->conyuge->getPeretn()] : 'NO APLICA';

        $mpueblos = ParamsConyuge::getPueblosIndigenas();
        $pueblo = ($this->conyuge->getPub_indigena_id()) ? $mpueblos[$this->conyuge->getPub_indigena_id()] : 'NO APLICA';

        $ocupaciones = ParamsConyuge::getOcupaciones();
        $ocupation = ($this->conyuge->getCodocu()) ? $ocupaciones[$this->conyuge->getCodocu()] : 'NINGUNA';

        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->conyuge->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de ciudadania';

        $mtipdisca = ParamsConyuge::getTipoDiscapacidad();
        $discapacidad = ($this->conyuge->getTipdis()) ? $mtipdisca[$this->conyuge->getTipdis()] : 'No tiene';

        $salario = ($this->conyuge->getSalario()) ? '$' . $this->conyuge->getSalario() : '$0';

        $empresalab = ($this->conyuge->getEmpresalab()) ? $this->conyuge->getEmpresalab() : 'NO APLICA';

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            array('lb' => 'Cedula', 'texto' => $this->conyuge->getCedcon(), 'x' => 11, 'y' => 90),
            array('lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 35, 'y' => 90),
            array('lb' => 'Celular', 'texto' => $this->conyuge->getTelefono(), 'x' => 94, 'y' => 90),
            array('lb' => 'Email', 'texto' => $this->conyuge->getEmail(), 'x' => 125, 'y' => 90),
            array('lb' => 'Primer apellido', 'texto' => $this->conyuge->getPriape(), 'x' => 10, 'y' => 100),
            array('lb' => 'Segundo apellido', 'texto' => $this->conyuge->getSegape(), 'x' => 60, 'y' => 100),
            array('lb' => 'Primer nombre', 'texto' => $this->conyuge->getPrinom(), 'x' => 110, 'y' => 100),
            array('lb' => 'Segundo nombre', 'texto' => $this->conyuge->getSegnom(), 'x' => 154, 'y' => 100),
            array('lb' => 'Dirección recidencia', 'texto' => $this->conyuge->getDireccion(), 'x' => 10, 'y' => 110),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 119, 'y' => 110),
            array('lb' => 'Zona urbana', 'texto' => 'X', 'x' => 194.5, 'y' => 105),
            array('lb' => 'Pertenencia etnica', 'texto' => $etnica, 'x' => 10, 'y' => 122),
            array('lb' => 'Resguardo', 'texto' => $resguardo, 'x' => 78, 'y' => 122),
            array('lb' => 'Pueblo indigena', 'texto' => $pueblo, 'x' => 125, 'y' => 122),
            $this->posTieneDiscapacidad(),
            array('lb' => 'Discapacidad', 'texto' => $discapacidad, 'x' => 40, 'y' => 134),
            $this->posSexo(),
            array('lb' => 'Fecha nacimiento año', 'texto' => $this->conyuge->getFecnac(), 'x' => 172, 'y' => 134),
            array('lb' => 'Empresa labora', 'texto' => $empresalab, 'x' => 10, 'y' => 145),
            array('lb' => 'Ingresos', 'texto' => $salario, 'x' => 132, 'y' => 145),
            array('lb' => 'Recibe subsidio', 'texto' => 'X', 'x' => 192, 'y' => 145),
            array('lb' => 'Ocupación', 'texto' =>  $ocupation, 'x' => 11, 'y' => 156),
        );

        $this->addBloq($datos);
    }

    function dataMedioPago()
    {
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor('65', '65', '65');
        $mbanco = ParamsConyuge::getBancos();

        if ($this->conyuge->getTippag() == 'T') {
            $nombre = '____________________';
            $detdoc = '____________________';
            $banco  = '____________________';
            $numerocedula = '_________________';
        } else {
            $banco = ($this->conyuge->getCodban()) ? $mbanco[$this->conyuge->getCodban()] : '______________';
            $nombre = strtoupper(substr(
                $this->conyuge->getPrinom() . ' ' .
                    $this->conyuge->getSegnom() . ' ' .
                    $this->conyuge->getPriape() . ' ' .
                    $this->conyuge->getSegape(),
                0,
                140
            ));

            $mtipoDocumentos = new Gener18();
            $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->conyuge->getTipdoc()}'");
            $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
            $numerocedula = $this->conyuge->getCedcon();
        }


        $numcue =  $this->conyuge->getNumcue();
        $mtippga = ParamsConyuge::getTipoPago();
        $tippag = ($this->conyuge->getTippag()) ? $mtippga[$this->conyuge->getTippag()] : '__________________';

        $html = "El cónyuge " . $nombre . ", con " . $detdoc . " y número " . $numerocedula .
            ', solicita que el pago del subsidio cuota monetaria se realice a la cuenta ' . $numcue . ' del ' . $banco . ', ' .
            'que corresponde al medio de pago ' . $tippag . '.';
        $html_decode = mb_convert_encoding($html, "ISO-8859-1", "UTF-8");
        $this->pdf->SetXY(10, 168);
        $this->pdf->MultiCell(190, 5, $html_decode, 0, 'L', 0);
    }

    function posTipoAfiliado()
    {
        if ($this->conyuge->getTipafi() == '3') {
            $x = 150;
            //2%
        } elseif ($this->conyuge->getTipafi() == '65') {
            //0.6%
            $x = 155;
        } else {
            //4%
            $x = 120;
        }
        return array('lb' => 'Tipo afiliado', 'texto' => 'X', 'x' => $x, 'y' => 42);
    }

    function posSexo()
    {
        switch ($this->conyuge->getSexo()) {
            case 'M':
                $x = 159;
                break;
            case 'F':
                $x = 161;
                break;
            default:
                $x = 164;
                break;
        }
        return array('lb' => 'Sexo', 'texto' => 'X', 'x' => $x, 'y' => 134);
    }

    function posEstadoCivil()
    {
        switch ($this->conyuge->getEstciv()) {
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

    function posZonaLaboral()
    {
        switch ($this->conyuge->getRural()) {
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
        switch ($this->conyuge->getFacvul()) {
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
        switch ($this->conyuge->getPeretn()) {
            case "1":
                //"Afrocolombiano",
                $x = 45;
                break;
            case "2":
                //"Comunidad negra",
                $x = 62;
                break;
            case "3":
                //"Indígena",
                $x = 140;
                break;
            case "4":
                //"Palanquero",
                $x = 84;
                break;
            case "5":
                //"Raizal del archipiélago de San Andrés, Providencia",
                $x = 105;
                break;
            case "6":
                //"Room/gitano",
                $x = 120;
                break;
            case "7":
                //"No se auto reconoce en ninguno de los anteriores",
                $x = 194;
                break;
            default:
                //"No Disponible"
                $x = 156;
                break;
        }
        return array('lb' => 'Pertenencia etnica', 'texto' => 'X', 'x' => $x, 'y' => 122);
    }

    function posNivelEscolar()
    {
        switch ($this->conyuge->getNivedu()) {
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
        switch ($this->conyuge->getOrisex()) {
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
        switch ($this->conyuge->getVivienda()) {
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
        if ($this->conyuge->getTipdis() == null || $this->conyuge->getTipdis() == '00') {
            $x = 30;
        } else {
            $x = 15;
        }
        return array('lb' => 'Discapacidad', 'texto' => 'X', 'x' => $x, 'y' => 133.5);
    }

    function posTipoPago()
    {
        switch ($this->conyuge->getTippag()) {
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
        return array('lb' => 'Tipo medio pago', 'texto' => ($x) ? 'X' : '', 'x' => $x, 'y' => 190);
    }
}
