<?php

namespace App\Services\Formularios\Declaration;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Gener18;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

class JuramentadaBeneficiario extends Documento
{

    private $parent;
    /**
     * $trabajador variable
     *
     * @var Mercurio31
     */
    private $trabajador;

    /**
     * $beneficiario variable
     *
     * @var Mercurio34
     */
    private $beneficiario;

    /**
     * main function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function main()
    {
        if (!$this->request->getParam('beneficiario')) {
            throw new DebugException("Error el beneficiario no esté disponible", 501);
        }
        $this->beneficiario = $this->request->getParam('beneficiario');
        $this->trabajador = $this->request->getParam('trabajador');
        $this->parent =  $this->beneficiario->getParent();

        switch ($this->parent) {
            case '1':
                $page = storage_path('public/docs/form/declaraciones/declaracion_jura_hijo.png');
                $this->pdf->Image($page, 0, 0, 210, 297, '');
                // es hijastro
                if ($this->beneficiario->getTiphij() == 2) {
                    $this->bloqueHijastro();
                } else {
                    $this->bloqueHijo();
                }
                $this->pdf->SetXY(13, 190);
                $this->bloqueDescoBio();
                break;
            case '4':
                $page = storage_path('public/docs/form/declaraciones/declaracion_jura_custodia.png');
                $this->pdf->Image($page, 0, 0, 210, 297, '');
                $this->bloqueCustodia();
                $this->pdf->SetXY(13, 175);
                $this->bloqueDescoBio();
                break;
            case '3': //padre
            case '2': //hermano
                $page = storage_path('public/docs/form/declaraciones/declaracion_jura_padres.png');
                $this->pdf->Image($page, 0, 0, 210, 297, '');
                $this->bloqueBeneficiarioPadre();
                break;
            case '5': //cuidador persona discapacitada
                $page = storage_path('public/docs/form/declaraciones/declaracion_jura_cuidador.png');
                $this->pdf->Image($page, 0, 0, 210, 297, '');
                $this->bloqueBeneficiarioCuidador();
                break;
            default:
                break;
        }

        $this->bloqueTrabajador();
        $page = storage_path('public/docs/sello-firma.png');
        $this->pdf->Image($page, 160, 275, 30, 20, '');
    }

    function bloqueBeneficiarioCuidador()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());
        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];

        $mtipdisca = ParamsBeneficiario::getTipoDiscapacidad();
        $discapacidad = ($this->beneficiario->getTipdis()) ? $mtipdisca[$this->beneficiario->getTipdis()] : 'No tiene';

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 168, 'y' => 62),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 142),
            array('lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 142),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 148),
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 148),
            array('lb' => 'Documento', 'texto' => ($this->beneficiario->getCaptra() == 'N') ? 'X' : '       X', 'x' => 44, 'y' => 154),
            array('lb' => 'Documento', 'texto' => $discapacidad, 'x' => 88, 'y' => 154)
        );
        $this->addBloq($datos);
    }

    function bloqueBeneficiarioPadre()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());
        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];

        $mtipdisca = ParamsBeneficiario::getTipoDiscapacidad();
        $discapacidad = ($this->beneficiario->getTipdis()) ? $mtipdisca[$this->beneficiario->getTipdis()] : 'No tiene';

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            $this->tipoNovedadPadre(),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 111),
            array('lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 111),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 116),
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 116),
            array('lb' => 'Email', 'texto' => capitalize($this->trabajador->getEmail()), 'x' => 44, 'y' => 123),
            array('lb' => 'Numero telefono', 'texto' => $this->trabajador->getTelefono(), 'x' => 160, 'y' => 123),
            array('lb' => 'Numero telefono', 'texto' => ($this->beneficiario->getCaptra() == 'N') ? '  X' : '        X', 'x' => 44, 'y' => 129),
            array('lb' => 'Numero telefono', 'texto' => $discapacidad, 'x' => 85, 'y' => 129)
        );
        $this->addBloq($datos);
    }

    function bloqueTrabajador()
    {
        $nomtra = capitalize($this->trabajador->getPrinom() . ' ' . $this->trabajador->getSegnom() . ' ' . $this->trabajador->getPriape() . ' ' . $this->trabajador->getSegape());
        $today = Carbon::now();
        $_codciu = ParamsBeneficiario::getCiudades();
        $ciudad = ($this->trabajador->getCodzon()) ? $_codciu[$this->trabajador->getCodzon()] : 'Florencia';
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            array('lb' => 'Nombre trabajador', 'texto' => capitalize($nomtra), 'x' => 20, 'y' => 35),
            array('lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 122, 'y' => 22),
            array('lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 134, 'y' => 22),
            array('lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 144, 'y' => 22),
            array('lb' => 'Ciudad', 'texto' => capitalize($ciudad), 'x' => 152, 'y' => 22),
            array('lb' => 'TipoDoc trabajador', 'texto' => capitalize($detdoc), 'x' => 72, 'y' => 42),
            array('lb' => 'Numero documento', 'texto' => capitalize($this->trabajador->getCedtra()), 'x' => 156, 'y' => 42)
        );
        $this->addBloq($datos);
    }

    function bloqueHijo()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());
        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            $this->tipoNovedadHijo(),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 105),
            array('lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 105),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 111),
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 111),
        );
        $this->addBloq($datos);
    }

    function bloqueHijastro()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());
        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];

        $mtipoDocumentos = ParamsTrabajador::getTiposDocumentos();
        if ($this->trabajador->getSexo() == 'M') {
            $pap_cedula = $this->trabajador->getCedtra();
            $pap_tipdoc = ($this->trabajador->getTipdoc()) ? $mtipoDocumentos[$this->trabajador->getTipdoc()] : 'CC';
            $mam_tipdoc = ($this->beneficiario->getBiotipdoc()) ? $mtipoDocumentos[$this->beneficiario->getBiotipdoc()] : 'CC';
            $mam_cedula = $this->beneficiario->getBiocedu();
        } else {
            $mam_cedula = $this->trabajador->getCedtra();
            $mam_tipdoc = ($this->trabajador->getTipdoc()) ? $mtipoDocumentos[$this->trabajador->getTipdoc()] : 'CC';
            $pap_tipdoc = ($this->beneficiario->getBiotipdoc()) ? $mtipoDocumentos[$this->beneficiario->getBiotipdoc()] : 'CC';
            $pap_cedula = $this->beneficiario->getBiocedu();
        }

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            $this->tipoNovedadHijo(),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 156),
            array('lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 156),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 162),
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 162),
            array('lb' => 'Numero documento papa', 'texto' => $pap_cedula, 'x' => 85, 'y' => 169),
            array('lb' => 'Tipo documento papa', 'texto' => $pap_tipdoc, 'x' => 70, 'y' => 175),
            array('lb' => 'Numero documento mama', 'texto' => $mam_cedula, 'x' => 162, 'y' => 169),
            array('lb' => 'Tipo documento mama', 'texto' => $mam_tipdoc, 'x' => 146, 'y' => 175)
        );
        $this->addBloq($datos);
    }

    function bloqueCustodia()
    {
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->beneficiario->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';
        $nombre = capitalize($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());
        $mparent =  ParamsBeneficiario::getParentesco();
        $parentesco = $mparent[$this->beneficiario->getParent()];

        $mtipoDocumentos = ParamsTrabajador::getTiposDocumentos();
        if ($this->trabajador->getSexo() == 'M') {
            $pap_cedula = $this->trabajador->getCedtra();
            $pap_tipdoc = ($this->trabajador->getTipdoc()) ? $mtipoDocumentos[$this->trabajador->getTipdoc()] : 'CC';
            $mam_tipdoc = ($this->beneficiario->getBiotipdoc()) ? $mtipoDocumentos[$this->beneficiario->getBiotipdoc()] : 'CC';
            $mam_cedula = $this->beneficiario->getBiocedu();
        } else {
            $mam_cedula = $this->trabajador->getCedtra();
            $mam_tipdoc = ($this->trabajador->getTipdoc()) ? $mtipoDocumentos[$this->trabajador->getTipdoc()] : 'CC';
            $pap_tipdoc = ($this->beneficiario->getBiotipdoc()) ? $mtipoDocumentos[$this->beneficiario->getBiotipdoc()] : 'CC';
            $pap_cedula = $this->beneficiario->getBiocedu();
        }

        $this->pdf->SetFont('Arial', '', 8.5);
        $datos = array(
            array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 160, 'y' => 62),
            array('lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 115),
            array('lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 165, 'y' => 115),
            array('lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 120),
            array('lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 120),
            array('lb' => 'Numero documento papa', 'texto' => $pap_cedula, 'x' => 85, 'y' => 127),
            array('lb' => 'Tipo documento papa', 'texto' => $pap_tipdoc, 'x' => 72, 'y' => 134),
            array('lb' => 'Numero documento mama', 'texto' => $mam_cedula, 'x' => 162, 'y' => 127),
            array('lb' => 'Tipo documento mama', 'texto' => $mam_tipdoc, 'x' => 147, 'y' => 134)
        );
        $this->addBloq($datos);
    }

    function tipoNovedadHijo()
    {
        if ($this->beneficiario->getParent() == 1) {
            $x = 89;
        } else {
            $x = 148;
        }
        return array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => $x, 'y' => 62);
    }

    function tipoNovedadPadre()
    {
        if ($this->beneficiario->getParent() == 3) {
            //padres
            $x = 84;
        } else {
            //hermanos
            $x = 154;
        }
        return array('lb' => 'Tipo novedad', 'texto' => 'X', 'x' => $x, 'y' => 61);
    }

    function bloqueDescoBio()
    {
        //N => Si conoce los datos del padre o madre biologico diferente al trabajador
        if ($this->beneficiario->getBiodesco() == 'N') {
            $bio_cedula = $this->beneficiario->getBiocedu();
            if ($bio_cedula || strlen($this->beneficiario->getBioprinom()) > 0) {
                $bio_nombre = strtoupper($this->beneficiario->getBioprinom() . ' ' . $this->beneficiario->getBiosegnom() . ' ' . $this->beneficiario->getBiopriape() . ' ' . $this->beneficiario->getBiosegape());
                $nombre = strtoupper($this->beneficiario->getPrinom() . ' ' . $this->beneficiario->getSegnom() . ' ' . $this->beneficiario->getPriape() . ' ' . $this->beneficiario->getSegape());
                $this->pdf->SetFont('helvetica', '', 9);
                $this->pdf->SetTextColor('65', '65', '65');

                $html = "Declaro que desconozco la ubicación del señor(a): " .
                    ($bio_nombre) . ", con identificación N: {$bio_cedula}, padre/madre biológico del menor: " .
                    ($nombre) . ", identificado con N: {$this->beneficiario->getNumdoc()}, por lo que no " .
                    "puedo aportar los datos relacionados con su certificación laboral y soy padre o madre unico responsable del menor.";

                $html_decode = mb_convert_encoding($html, "ISO-8859-1", "UTF-8");
                $this->pdf->MultiCell(184, 5, $html_decode, 0, 'L', 0);
            }
        } else {
            $html = "Declaro que desconozco la ubicación del señor(a): _____________________________" .
                ", con identificación N: __________________________, padre/madre biológico del menor: " .
                "______________________________________, identificado con N: _____________________________, por lo que no " .
                "puedo aportar los datos relacionados con su certificación laboral y soy padre o madre unico responsable del menor.";

            $html_decode = mb_convert_encoding($html, "ISO-8859-1", "UTF-8");
            $this->pdf->MultiCell(184, 5, $html_decode, 0, 'L', 0);
        }
    }
}
