<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Gener18;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

class FormularioBeneficiario extends Documento
{
    /**
     * beneficiario variable
     * @var Mercurio34
     */
    private $beneficiario;

    /**
     * trabajador variable
     * @var Mercurio31
     */
    private $trabajador;

    /**
     * bioconyu variable
     * @var Mercurio32
     */
    private $bioconyu;

    private $parent;


    /**
     * main function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('beneficiario')) {
            throw new DebugException("Error el beneficiario no esté disponible", 501);
        }
        $this->beneficiario = $this->request->getParam('beneficiario');
        $this->trabajador =  $this->request->getParam('trabajador');
        $this->bioconyu =  $this->request->getParam('bioconyu');

        $this->pdf->SetTitle("Formulario adición del beneficiario {$this->beneficiario->getNumdoc()}, COMFACA");
        $this->pdf->SetAuthor("{$this->trabajador->getPriape()} {$this->trabajador->getSegape()} {$this->trabajador->getPrinom()} {$this->trabajador->getSegnom()}, COMFACA");
        $this->pdf->SetSubject("Formulario de adición a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $this->parent =  $this->beneficiario->getParent();
        $this->pdf->SetFont('helvetica', '', 8.5);

        $this->pdf->Image(storage_path('public/docs/form/beneficiarios/form_adicion_beneficiario.png'), 0, 0, 210, 297, '');
        $this->pdf->SetAutoPageBreak(false, 0);

        $this->headerForm();
        $this->dataTrabajador();
        switch ($this->parent) {
            case '1':
            case '4':
                $this->dataMadreFueraUnion();
                $this->dataBeneficiarioHijo();
                break;
            case '3': //padre
                $this->dataBeneficiarioPadre();
                break;
            case '5': //cuidador persona discapacitada
            case '2': //hermano
                $this->dataBeneficiarioHijo();
                break;
            default:
                break;
        }

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
        $_codciu = ParamsTrabajador::getCiudades();
        $ciudad = ($this->trabajador->getCodzon()) ? $_codciu[$this->trabajador->getCodzon()] : 'Florencia';
        $today = Carbon::now();
        $this->pdf->SetFont('helvetica', '', 8.6);
        $datos = array(
            array('lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 116, 'y' => 26),
            array('lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 130, 'y' => 26),
            array('lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 140, 'y' => 26),
            array('lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 150, 'y' => 26),
        );
        $this->addBloq($datos);
    }

    function dataTrabajador()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de ciudadania';
        $nombtra = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());
        $y = 74;
        $datos = array(
            array('lb' => 'Adición', 'texto' => 'X', 'x' => 45, 'y' => 52),
            array('lb' => 'Cedula trabajador', 'texto' => $this->trabajador->getCedtra(), 'x' => 10, 'y' => $y),
            array('lb' => 'Tipo documento trabajador', 'texto' => $detdoc, 'x' => 35, 'y' => $y),
            array('lb' => 'Nombre trabajador', 'texto' => substr($nombtra, 0, 55), 'x' => 93, 'y' => $y),
            array('lb' => 'Empresa', 'texto' => $this->trabajador->getNit(), 'x' => 175, 'y' => $y),
        );
        $this->addBloq($datos);
    }

    function tipoAfiliado()
    {
        $datos = array(
            array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 38, 'y' => 42),
            $this->posTipoAfiliado()
        );
        $this->addBloq($datos);
    }

    function dataBeneficiarioHijo()
    {
        $mresguardos = ParamsBeneficiario::getResguardos();
        $resguardo = ($this->beneficiario->getResguardo_id()) ? $mresguardos[$this->beneficiario->getResguardo_id()] : 'NO APLICA';

        $mpueblos = ParamsBeneficiario::getPueblosIndigenas();
        $pueblo = ($this->beneficiario->getPub_indigena_id()) ? $mpueblos[$this->beneficiario->getPub_indigena_id()] : 'NO APLICA';

        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());

        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];
        $metnica =  ParamsBeneficiario::getPertenenciaEtnicas();
        $etnica = ($this->beneficiario->getPeretn()) ? $metnica[$this->beneficiario->getPeretn()] : 'No aplica';

        $mtipdisca = ParamsBeneficiario::getTipoDiscapacidad();
        $discapacidad = ($this->beneficiario->getTipdis()) ? $mtipdisca[$this->beneficiario->getTipdis()] : 'No tiene';

        $mtihij = ParamsBeneficiario::getTipoHijo();
        $tipo_hijo = $this->beneficiario->getTiphij() ? $mtihij[$this->beneficiario->getTiphij()] : 'Hijo normal';

        if ($this->beneficiario->getBiodesco() == 'S') {
            $desco_x = 101;
        } else {
            $desco_x = 112;
        }

        $this->pdf->SetFont('helvetica', '', 8);
        $datos = array(
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 10, 'y' => 125.5),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 37, 'y' => 125.5),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 60), 'x' => 86, 'y' => 125.5),
            array('lb' => 'Parentesco', 'texto' => $parentesco, 'x' => 173, 'y' => 125.5),
            array('lb' => 'Pertenencia etnica', 'texto' => capitalize($etnica), 'x' => 10, 'y' => 135),
            array('lb' => 'resguardo', 'texto' => capitalize(substr($resguardo, 0, 42)), 'x' => 65, 'y' => 135),
            array('lb' => 'pueblo', 'texto' => capitalize(substr($pueblo, 0, 42)), 'x' => 125, 'y' => 135),
            $this->posTieneDiscapacidad(),
            array('lb' => 'Discapacidad', 'texto' => capitalize($discapacidad), 'x' => 39, 'y' => 145),
            $this->posSexo(),
            array('lb' => 'Fecha nacimiento', 'texto' => $this->beneficiario->getFecnac(), 'x' => 132, 'y' => 145),
            array('lb' => 'Condición hijo', 'texto' => $tipo_hijo, 'x' => 163, 'y' => 145),
            array('lb' => 'Desconoce ubicacion biologico', 'texto' => 'X', 'x' => $desco_x, 'y' => 181),
        );
        $this->addBloq($datos);
    }

    function dataBeneficiarioPadre()
    {
        $mresguardos = ParamsBeneficiario::getResguardos();
        $resguardo = ($this->beneficiario->getResguardo_id()) ? $mresguardos[$this->beneficiario->getResguardo_id()] : 'NO APLICA';

        $mpueblos = ParamsBeneficiario::getPueblosIndigenas();
        $pueblo = ($this->beneficiario->getPub_indigena_id()) ? $mpueblos[$this->beneficiario->getPub_indigena_id()] : 'NO APLICA';

        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());

        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];
        $metnica =  ParamsBeneficiario::getPertenenciaEtnicas();
        $etnica = ($this->beneficiario->getPeretn()) ? $metnica[$this->beneficiario->getPeretn()] : 'No aplica';

        $mtipdisca = ParamsBeneficiario::getTipoDiscapacidad();
        $discapacidad = ($this->beneficiario->getTipdis()) ? $mtipdisca[$this->beneficiario->getTipdis()] : 'No tiene';

        $mtihij = ParamsBeneficiario::getTipoHijo();
        $tipo_hijo = $this->beneficiario->getTiphij() ? $mtihij[$this->beneficiario->getTiphij()] : '';

        $this->pdf->SetFont('helvetica', '', 8);
        $datos = array(
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 10, 'y' => 125.5),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 37, 'y' => 125.5),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 60), 'x' => 86, 'y' => 125.5),
            array('lb' => 'Parentesco', 'texto' => $parentesco, 'x' => 173, 'y' => 125.5),
            array('lb' => 'Pertenencia etnica', 'texto' => capitalize($etnica), 'x' => 10, 'y' => 135),
            array('lb' => 'resguardo', 'texto' => capitalize(substr($resguardo, 0, 42)), 'x' => 65, 'y' => 135),
            array('lb' => 'pueblo', 'texto' => capitalize(substr($pueblo, 0, 42)), 'x' => 125, 'y' => 135),
            $this->posTieneDiscapacidad(),
            array('lb' => 'Discapacidad', 'texto' => capitalize($discapacidad), 'x' => 39, 'y' => 145),
            $this->posSexo(),
            array('lb' => 'Fecha nacimiento', 'texto' => $this->beneficiario->getFecnac(), 'x' => 132, 'y' => 145),
            array('lb' => 'Condición padre', 'texto' => $tipo_hijo, 'x' => 163, 'y' => 145),
        );
        $this->addBloq($datos);
    }

    function dataMadreFueraUnion()
    {
        if ($this->bioconyu) {
            $mciudad = ParamsBeneficiario::getCiudades();
            $ciudad = ($this->bioconyu->getCiures()) ? $mciudad[$this->bioconyu->getCiures()] : ' FLORENCIA';
            $mtipoDocumentos = new Gener18();
            $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->bioconyu->getTipdoc()}'");
            $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de ciudadania';
            $telefono = $this->bioconyu->getTelefono();
            $email = $this->bioconyu->getEmail();
            $datos = array(
                array('lb' => 'Documento', 'texto' => $this->bioconyu->getCedcon(), 'x' => 10, 'y' => 90),
                array('lb' => 'TipoDoc', 'texto' => substr(capitalize($detdoc), 0, 44), 'x' => 34.5, 'y' => 90),
                array('lb' => 'Celular', 'texto' => $telefono, 'x' => 93, 'y' => 90),
                array('lb' => 'Email', 'texto' => $email, 'x' => 126, 'y' => 90),
                array('lb' => 'Primer apellido', 'texto' => $this->bioconyu->getPriape(), 'x' => 10, 'y' => 100),
                array('lb' => 'Segundo apellido', 'texto' => $this->bioconyu->getSegape(), 'x' => 60, 'y' => 100),
                array('lb' => 'Primer nombre', 'texto' => $this->bioconyu->getPrinom(), 'x' => 110, 'y' => 100),
                array('lb' => 'Segundo nombre', 'texto' => $this->bioconyu->getSegnom(), 'x' => 154, 'y' => 100),
                array('lb' => 'Dirección recidencia', 'texto' => $this->bioconyu->getDireccion(), 'x' => 10, 'y' => 109.5),
                array('lb' => 'Ciudad', 'texto' => capitalize($ciudad), 'x' => 118, 'y' => 109.5),
                $this->posZonaResidencial()
            );
            $this->addBloq($datos);
        }
    }

    function dataMedioPago()
    {
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor('65', '65', '65');
        $mbanco = ParamsTrabajador::getBancos();


        if ($this->beneficiario->getTippag() == 'T') {
            $nombre = '____________________';
            $detdoc = '____________________';
            $banco  = '____________________';
            $numerocedtra = '_________________';
        } else {
            $banco = ($this->beneficiario->getCodban()) ? $mbanco[$this->beneficiario->getCodban()] : '______________';
            $nombre = strtoupper(substr(
                $this->trabajador->getPrinom() . ' ' .
                    $this->trabajador->getSegnom() . ' ' .
                    $this->trabajador->getPriape() . ' ' .
                    $this->trabajador->getSegape(),
                0,
                140
            ));

            $mtipoDocumentos = new Gener18();
            $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
            $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
            $numerocedtra = $this->trabajador->getCedtra();
        }


        $numcue =  $this->beneficiario->getNumcue();
        $mtippga = ParamsBeneficiario::getTipoPago();
        $tippag = ($this->beneficiario->getTippag()) ? $mtippga[$this->beneficiario->getTippag()] : '__________________';

        $html = "El trabajador " . $nombre . ", con " . $detdoc . " y número " . $numerocedtra .
            ', solicita que el pago del subsidio cuota monetaria se realice a la cuenta ' . $numcue . ' del ' . $banco . ', ' .
            'que corresponde al medio de pago ' . $tippag . '.';
        $html_decode = mb_convert_encoding($html, "ISO-8859-1", "UTF-8");
        $this->pdf->SetXY(10, 155);
        $this->pdf->MultiCell(190, 5, $html_decode, 0, 'L', 0);
    }

    function posTipoAfiliado()
    {
        if ($this->trabajador->getTipo() == 'I') {
            $x = 150;
            //2%
        } elseif ($this->trabajador->getTipo() == 'P') {
            //0.6%
            $x = 155;
        } else {
            //4%
            $x = 120;
        }
        return array('lb' => 'Tipo afiliado trabajador', 'texto' => 'Trabajador', 'x' => 30, 'y' => 140);
    }

    function posTipoDocumento()
    {
        switch ($this->beneficiario->getTipdoc()) {
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
        switch ($this->beneficiario->getSexo()) {
            case 'M':
                $x = 116;
                break;
            case 'F':
                $x = 123;
                break;
            default:
                $x = 127;
                break;
        }
        return array('lb' => 'Sexo', 'texto' => 'X', 'x' => $x, 'y' => 145);
    }

    function posEstadoCivil()
    {
        switch ($this->beneficiario->getEstciv()) {
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
        switch ($this->trabajador->getRural()) {
            case 'N':
                // RURAl NO
                $y = 108.5;
                break;
            case 'S':
                // RURAL SI
                $y = 110;
                break;
            default:
                $y = 110;
                break;
        }
        return array('lb' => 'Zona recidencial', 'texto' => 'X', 'x' => 195, 'y' => $y);
    }

    function posZonaLaboral()
    {
        switch ($this->beneficiario->getRural()) {
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
        switch ($this->beneficiario->getFacvul()) {
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

    function posNivelEscolar()
    {
        switch ($this->beneficiario->getNivedu()) {
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
        switch ($this->beneficiario->getOrisex()) {
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
        switch ($this->beneficiario->getVivienda()) {
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
        if ($this->beneficiario->getCaptra() == 'I') {
            $x = 16;
        } else {
            $x = 30;
        }
        return array('lb' => 'Discapacidad', 'texto' => 'X', 'x' => $x, 'y' => 145);
    }

    function posTipoPago()
    {
        switch ($this->beneficiario->getTippag()) {
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
        return array('lb' => 'Tipo medio pago', 'texto' => ($x) ? 'X' : '', 'x' => $x, 'y' => 140);
    }
}
