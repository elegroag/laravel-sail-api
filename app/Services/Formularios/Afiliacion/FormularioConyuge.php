<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsConyuge;
use App\Models\Gener18;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

class FormularioConyuge extends Documento
{
    /**
     * conyuge variable
     *
     * @var Mercurio32
     */
    private $conyuge;

    /**
     * trabajador variable
     *
     * @var Mercurio31
     */
    private $trabajador;

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
        if (! $this->request->getParam('conyuge')) {
            throw new DebugException('Error la empresa no esté disponible', 501);
        }
        $this->conyuge = $this->request->getParam('conyuge');
        $this->trabajador = $this->request->getParam('trabajador');

        $this->pdf->SetTitle("Formulario adición del cónyuge {$this->trabajador->getCedtra()}, COMFACA");
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->pdf->SetAuthor("{$this->trabajador->getPriape()} {$this->trabajador->getSegape()} {$this->trabajador->getPrinom()} {$this->trabajador->getSegnom()}, COMFACA");
        $this->pdf->SetSubject('Formulario de adición a COMFACA');
        $this->pdf->SetCreator('Plataforma Web: comfacaenlinea.com.co, COMFACA');
        $this->pdf->SetKeywords('COMFACA');

        $this->pdf->SetXY(148, 50);
        $this->pdf->Cell(15, 4, 'R-UI: ' . $this->conyuge->ruuid, 0, 0, 'C');

        $this->headerForm(22);
        $this->dataTrabajador(45);
        $this->dataConyuge(80);
        $this->dataMedioPago(152);
        $this->addBloq(
            [
                ['lb' => 'Acepta politica', 'texto' => 'X', 'x' => 168, 'y' => 239],
            ]
        );

        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 250, 30, 20, '', '', '', false, 300, '', false, false, 0);

        return $this;
    }

    public function headerForm($y)
    {
        $_codciu = ParamsConyuge::getCiudades();
        $ciudad = ($this->conyuge->getCodzon()) ? $_codciu[$this->conyuge->getCodzon()] : 'Florencia';
        $today = Carbon::now();
        $this->pdf->SetFont('helvetica', '', 9);
        $datos = [
            ['lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 116, 'y' => $y],
            ['lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 130, 'y' => $y],
            ['lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 140, 'y' => $y],
            ['lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 150, 'y' => $y],
        ];
        $this->addBloq($datos);
    }

    public function dataTrabajador($y)
    {
        $mtipoDocumentos = new Gener18;
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de ciudadania';

        $nombtra = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            ['lb' => 'Adicion personas', 'texto' => 'X', 'x' => 45, 'y' => $y],
            ['lb' => 'Cedula trabajador', 'texto' => $this->trabajador->getCedtra(), 'x' => 11, 'y' => $y + 20],
            ['lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 35, 'y' => $y + 20],
            ['lb' => 'Nombre trabajador', 'texto' => substr($nombtra, 0, 50), 'x' => 93, 'y' => $y + 20],
            ['lb' => 'NIT', 'texto' => $this->trabajador->getNit(), 'x' => 175, 'y' => $y + 20],
        ];

        $this->addBloq($datos);
    }

    public function dataConyuge($y)
    {
        $mciudad = ParamsConyuge::getCiudades();
        $ciudad = ($this->conyuge->getCodzon()) ? $mciudad[$this->conyuge->getCodzon()] : ' FLORENCIA';

        $mresguardos = ParamsConyuge::getResguardos();
        $resguardo = ($this->conyuge->getResguardo_id()) ? $mresguardos[$this->conyuge->getResguardo_id()] : 'NO APLICA';

        $metnica = ParamsConyuge::getPertenenciaEtnicas();
        $etnica = ($this->conyuge->getPeretn()) ? $metnica[$this->conyuge->getPeretn()] : 'NO APLICA';

        $mpueblos = ParamsConyuge::getPueblosIndigenas();
        $pueblo = ($this->conyuge->getPub_indigena_id()) ? $mpueblos[$this->conyuge->getPub_indigena_id()] : 'NO APLICA';

        $ocupaciones = ParamsConyuge::getOcupaciones();
        $ocupation = ($this->conyuge->getCodocu()) ? $ocupaciones[$this->conyuge->getCodocu()] : 'NINGUNA';

        $mtipoDocumentos = new Gener18;
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->conyuge->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de ciudadania';

        $mtipdisca = ParamsConyuge::getTipoDiscapacidad();
        $discapacidad = ($this->conyuge->getTipdis()) ? $mtipdisca[$this->conyuge->getTipdis()] : 'No tiene';

        $salario = ($this->conyuge->getSalario()) ? '$' . $this->conyuge->getSalario() : '$0';

        $empresalab = ($this->conyuge->getEmpresalab()) ? $this->conyuge->getEmpresalab() : 'NO APLICA';

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            ['lb' => 'Cedula', 'texto' => $this->conyuge->getCedcon(), 'x' => 11, 'y' => $y],
            ['lb' => 'Tipo documento', 'texto' => $detdoc, 'x' => 35, 'y' => $y],
            ['lb' => 'Celular', 'texto' => $this->conyuge->getTelefono(), 'x' => 94, 'y' => $y],
            ['lb' => 'Email', 'texto' => $this->conyuge->getEmail(), 'x' => 125, 'y' => $y],
            ['lb' => 'Primer apellido', 'texto' => $this->conyuge->getPriape(), 'x' => 10, 'y' => $y + 9],
            ['lb' => 'Segundo apellido', 'texto' => $this->conyuge->getSegape(), 'x' => 60, 'y' => $y + 9],
            ['lb' => 'Primer nombre', 'texto' => $this->conyuge->getPrinom(), 'x' => 110, 'y' => $y + 9],
            ['lb' => 'Segundo nombre', 'texto' => $this->conyuge->getSegnom(), 'x' => 154, 'y' => $y + 9],
            ['lb' => 'Dirección recidencia', 'texto' => $this->conyuge->getDireccion(), 'x' => 10, 'y' => $y + 18],
            ['lb' => 'Ciudad', 'texto' => $ciudad, 'x' => 119, 'y' => $y + 18],
            ['lb' => 'Zona urbana', 'texto' => 'X', 'x' => 194.5, 'y' => $y + 14],
            ['lb' => 'Pertenencia etnica', 'texto' => $etnica, 'x' => 10, 'y' => $y + 30],
            ['lb' => 'Resguardo', 'texto' => $resguardo, 'x' => 78, 'y' => $y + 30],
            ['lb' => 'Pueblo indigena', 'texto' => $pueblo, 'x' => 125, 'y' => $y + 30],
            $this->posTieneDiscapacidad($y + 40),
            ['lb' => 'Discapacidad', 'texto' => $discapacidad, 'x' => 40, 'y' => $y + 40],
            $this->posSexo($y + 40),
            ['lb' => 'Fecha nacimiento año', 'texto' => $this->conyuge->getFecnac(), 'x' => 170, 'y' => $y + 40],
            ['lb' => 'Empresa labora', 'texto' => $empresalab, 'x' => 10, 'y' => $y + 51],
            ['lb' => 'Ingresos', 'texto' => $salario, 'x' => 132, 'y' => $y + 51],
            ['lb' => 'Recibe subsidio', 'texto' => 'X', 'x' => 192, 'y' => $y + 51],
            ['lb' => 'Ocupación', 'texto' => $ocupation, 'x' => 11, 'y' => $y + 61],
        ];

        $this->addBloq($datos);
    }

    public function dataMedioPago($y)
    {
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor('65', '65', '65');
        $mbanco = ParamsConyuge::getBancos();

        if ($this->conyuge->getTippag() == 'T') {
            $nombre = '____________________';
            $detdoc = '____________________';
            $banco = '____________________';
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

            $mtipoDocumentos = new Gener18;
            $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->conyuge->getTipdoc()}'");
            $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
            $numerocedula = $this->conyuge->getCedcon();
        }

        $numcue = $this->conyuge->getNumcue();
        $mtippga = ParamsConyuge::getTipoPago();
        $tippag = ($this->conyuge->getTippag()) ? $mtippga[$this->conyuge->getTippag()] : '__________________';

        $html = 'El cónyuge ' . $nombre . ', con ' . $detdoc . ' y número <b>' . $numerocedula .
            '</b>, solicita que el pago del subsidio cuota monetaria se realice a la cuenta <b>' . $numcue . '</b> del ' . $banco . ', ' .
            'que corresponde al medio de pago <b>' . $tippag . '</b>.';

        $this->pdf->MultiCell(190, 5, $html, 0, 'L', 0, 1, 10, $y, null, null, true);
    }

    public function posTipoAfiliado()
    {
        if ($this->conyuge->getTipafi() == '3') {
            $x = 150;
            // 2%
        } elseif ($this->conyuge->getTipafi() == '65') {
            // 0.6%
            $x = 155;
        } else {
            // 4%
            $x = 120;
        }

        return ['lb' => 'Tipo afiliado', 'texto' => 'X', 'x' => $x, 'y' => 42];
    }

    public function posSexo($y)
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

        return ['lb' => 'Sexo', 'texto' => 'X', 'x' => $x, 'y' => $y];
    }

    public function posEstadoCivil()
    {
        switch ($this->conyuge->getEstciv()) {
            case '4':
                // union libre
                $x = 93;
                break;
            case '2':
                // casado
                $x = 105;
                break;
            case '6':
                // divorciado
                $x = 117;
                break;
            case '5':
                // separado
                $x = 128;
                break;
            case '3':
                // viudo
                $x = 139;
                break;
            default:
                // soltero
                $x = 150;
                break;
        }

        return ['lb' => 'Estado civil', 'texto' => 'X', 'x' => $x, 'y' => 92];
    }

    public function posZonaLaboral()
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

        return ['lb' => 'Zona laboral', 'texto' => 'X', 'x' => $x, 'y' => 188];
    }

    public function posFactorVulnera()
    {
        switch ($this->conyuge->getFacvul()) {
            case '1':
                // "Desplazado",
                $y = 118;
                break;
            case '2':
                // "Víctima del conflicto armado (No desplazado)",
                $y = 122;
                break;
            case '3':
                // "Desmovilizado o reinsertado",
                $y = 126;
                break;
            case '4':
                // "Hijo (as) de desmovilizados o reisertados",
                $y = 130;
                break;
            case '5':
                // "Damnificado desastre natural",
                $y = 134;
                break;
            case '6':
                // "Cabeza de familia",
                $y = 138;
                break;
            case '7':
                // "Hijo (as) de madres cabeza de familia",
                $y = 142;
                break;
            case '8':
                // "En condición de discapacidad",
                $y = 146;
                break;
            case '9':
                // "Población migrante",
                $y = 150;
                break;
            case '10':
                // "Población zonas frontera (Nacionales)",
                $y = 154;
                break;
            case '11':
                // "Ejercicio del trabajo sexual",
                $y = 159;
                break;
            default:
                // "No aplica",
                // "No disponible"
                $y = 163;
                break;
        }

        return ['lb' => 'Factor vulnerabilidad', 'texto' => 'X', 'x' => 10, 'y' => $y];
    }

    public function posPertenenciaEtnica()
    {
        switch ($this->conyuge->getPeretn()) {
            case '1':
                // "Afrocolombiano",
                $x = 45;
                break;
            case '2':
                // "Comunidad negra",
                $x = 62;
                break;
            case '3':
                // "Indígena",
                $x = 140;
                break;
            case '4':
                // "Palanquero",
                $x = 84;
                break;
            case '5':
                // "Raizal del archipiélago de San Andrés, Providencia",
                $x = 105;
                break;
            case '6':
                // "Room/gitano",
                $x = 120;
                break;
            case '7':
                // "No se auto reconoce en ninguno de los anteriores",
                $x = 194;
                break;
            default:
                // "No Disponible"
                $x = 156;
                break;
        }

        return ['lb' => 'Pertenencia etnica', 'texto' => 'X', 'x' => $x, 'y' => 122];
    }

    public function posNivelEscolar()
    {
        switch ($this->conyuge->getNivedu()) {
            case '1':
                // "PREESCOLAR",
                $y = 118;
                $x = 128;
                break;
            case '2':
                // "BASICA",
                $y = 122;
                $x = 128;
                break;
            case '3':
                // "SECUNDARIA",
                $y = 126;
                $x = 128;
                break;
            case '4':
                // "MEDIA",
                $y = 130;
                $x = 128;
                break;
            case '6':
                // "BÁSICA ADULTOS",
                $y = 134;
                $x = 128;
                break;
            case '7':
                // "SECUNDARIA ADULTO",
                $y = 138;
                $x = 128;
                break;
            case '8':
                // "MEDIA ADULTO",
                $y = 142;
                $x = 128;
                break;
            case '10':
                // "TECNICO/TEGNOLOGO",
                $y = 146;
                $x = 128;
                break;
            case '11':
                // "UNIVERSITARIO",
                $y = 118;
                $x = 165;
                break;
            case '12':
                // "POSGRADO/MAESTRÍA",
                $y = 122;
                $x = 165;
                break;
            case '13':
                // "NINGUNO",
                $y = 138;
                $x = 165;
                break;
            case '14':
                // "INFORMACION NO DISPONIBLE"
                $y = 146;
                $x = 165;
                break;
        }

        return ['lb' => 'Nivel escolaridad', 'texto' => 'X', 'x' => $x, 'y' => $y];
    }

    public function posOriSexual()
    {
        switch ($this->conyuge->getOrisex()) {
            case '2':
                // "Homosexual",
                $y = 158;
                break;
            case '3':
                // "Bisexual",
                $y = 162;
                break;
            case '4':
                // "Información no disponible"
                $y = 167;
                break;
            default:
                // "Heterosexual",
                $y = 155;
                break;
        }

        return ['lb' => 'Orientacion sexual', 'texto' => 'X', 'x' => 65, 'y' => $y];
    }

    public function posTipoVivienda()
    {
        switch ($this->conyuge->getVivienda()) {
            case 'P':
                // "PROPIA",
                $x = 128;
                $y = 155;
                break;
            case 'F':
                // "FAMILIAR",
                $x = 165;
                $y = 155;
                break;
            case 'A':
                // "ARRENDADA",
                $x = 128;
                $y = 159;

                break;
            case 'H':
                // "HIPOTECA"
                $x = 165;
                $y = 159;
                break;
            default:
                $x = 128;
                $y = 159;
                break;
        }

        return ['lb' => 'Tipo vivienda', 'texto' => 'X', 'x' => $x, 'y' => $y];
    }

    public function posTieneDiscapacidad($y)
    {
        if ($this->conyuge->getTipdis() == null || $this->conyuge->getTipdis() == '00') {
            $x = 30;
        } else {
            $x = 15;
        }

        return ['lb' => 'Discapacidad', 'texto' => 'X', 'x' => $x, 'y' => $y];
    }

    public function posTipoPago($y)
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

        return ['lb' => 'Tipo medio pago', 'texto' => ($x) ? 'X' : '', 'x' => $x, 'y' => $y];
    }
}
