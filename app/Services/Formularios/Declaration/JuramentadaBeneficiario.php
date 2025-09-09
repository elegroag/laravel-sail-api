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

        $this->bloqueTrabajador();

        $this->parent =  $this->beneficiario->getParent();
        switch ($this->parent) {
            case '1':
                // es hijastro
                if ($this->beneficiario->getTiphij() == 2) {
                    $this->bloqueHijastro();
                } else {
                    $this->bloqueHijo();
                }
                $this->bloqueDescoBio(173);
                break;
            case '4':
                $this->bloqueCustodia();
                $this->bloqueDescoBio(173);
                break;
            case '3': //padre
            case '2': //hermano
                $this->bloqueBeneficiarioPadre();
                break;
            case '5': //cuidador persona discapacitada
                $this->bloqueBeneficiarioCuidador();
                break;
            default:
                break;
        }

        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
        return $this;
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

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            ['lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 168, 'y' => 62],
            ['lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 142],
            ['lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 142],
            ['lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 148],
            ['lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 148],
            ['lb' => 'Documento', 'texto' => ($this->beneficiario->getCaptra() == 'N') ? 'X' : '       X', 'x' => 44, 'y' => 154],
            ['lb' => 'Documento', 'texto' => $discapacidad, 'x' => 88, 'y' => 154]
        ];
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

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            $this->tipoNovedadPadre(),
            ['lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 94],
            ['lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 94],
            ['lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 100],
            ['lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 100],
            ['lb' => 'Email', 'texto' => capitalize($this->trabajador->getEmail()), 'x' => 44, 'y' => 113],
            ['lb' => 'Numero telefono', 'texto' => $this->trabajador->getTelefono(), 'x' => 160, 'y' => 113],
            ['lb' => 'Numero telefono', 'texto' => ($this->beneficiario->getCaptra() == 'N') ? '  X' : '        X', 'x' => 44, 'y' => 119],
            ['lb' => 'Numero telefono', 'texto' => $discapacidad, 'x' => 85, 'y' => 119]
        ];
        $this->addBloq($datos);
    }

    function bloqueTrabajador()
    {
        $nomtra = capitalize($this->trabajador->getNombreCompleto());
        $today = Carbon::now();
        $_codciu = ParamsBeneficiario::getCiudades();
        $ciudad = ($this->trabajador->getCodzon()) ? $_codciu[$this->trabajador->getCodzon()] : 'Florencia';
        $mtipoDocumentos = new Gener18();
        $mtidocs = $mtipoDocumentos->findFirst(" coddoc='{$this->trabajador->getTipdoc()}'");
        $detdoc = ($mtidocs) ? $mtidocs->getDetdoc() : 'Cedula de Ciudadania';

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            ['lb' => 'Año', 'texto' => $today->format('Y'), 'x' => 122, 'y' => 18],
            ['lb' => 'Mes', 'texto' => $today->format('m'), 'x' => 134, 'y' => 18],
            ['lb' => 'Dia', 'texto' => $today->format('d'), 'x' => 144, 'y' => 18],
            ['lb' => 'Ciudad', 'texto' => capitalize($ciudad), 'x' => 152, 'y' => 18],
            ['lb' => 'Nombre trabajador', 'texto' => capitalize($nomtra), 'x' => 20, 'y' => 29],
            ['lb' => 'TipoDoc trabajador', 'texto' => capitalize($detdoc), 'x' => 72, 'y' => 35],
            ['lb' => 'Numero documento', 'texto' => capitalize($this->trabajador->getCedtra()), 'x' => 156, 'y' => 35]
        ];
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

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            $this->tipoNovedadHijo(),
            ['lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 94],
            ['lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 94],
            ['lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 100],
            ['lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 100],
        ];
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

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            $this->tipoNovedadHijo(),
            ['lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 94],
            ['lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 164, 'y' => 94],
            ['lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 100],
            ['lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 100],
            ['lb' => 'Numero documento papa', 'texto' => $pap_cedula, 'x' => 85, 'y' => 107],
            ['lb' => 'Tipo documento papa', 'texto' => $pap_tipdoc, 'x' => 70, 'y' => 113],
            ['lb' => 'Numero documento mama', 'texto' => $mam_cedula, 'x' => 162, 'y' => 107],
            ['lb' => 'Tipo documento mama', 'texto' => $mam_tipdoc, 'x' => 146, 'y' => 113]
        ];
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

        $this->pdf->SetFont('helvetica', '', 8.5);
        $datos = [
            ['lb' => 'Tipo novedad', 'texto' => 'X', 'x' => 160, 'y' => 54],
            ['lb' => 'Nombre beneficiario', 'texto' => substr($nombre, 0, 63), 'x' => 44, 'y' => 94],
            ['lb' => 'Parentesco', 'texto' => capitalize($parentesco), 'x' => 165, 'y' => 94],
            ['lb' => 'Tipo documento', 'texto' => capitalize($detdoc), 'x' => 44, 'y' => 100],
            ['lb' => 'Documento', 'texto' => $this->beneficiario->getNumdoc(), 'x' => 146, 'y' => 100],
            ['lb' => 'Numero documento papa', 'texto' => $pap_cedula, 'x' => 85, 'y' => 107],
            ['lb' => 'Tipo documento papa', 'texto' => $pap_tipdoc, 'x' => 72, 'y' => 113],
            ['lb' => 'Numero documento mama', 'texto' => $mam_cedula, 'x' => 162, 'y' => 107],
            ['lb' => 'Tipo documento mama', 'texto' => $mam_tipdoc, 'x' => 147, 'y' => 113]
        ];
        $this->addBloq($datos);
    }

    function tipoNovedadHijo()
    {
        if ($this->beneficiario->getParent() == 1) {
            $x = 89;
        } else {
            $x = 148;
        }
        return ['lb' => 'Tipo novedad', 'texto' => 'X', 'x' => $x, 'y' => 54];
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
        return ['lb' => 'Tipo novedad', 'texto' => 'X', 'x' => $x, 'y' => 54];
    }

    function bloqueDescoBio($y)
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

                $this->pdf->MultiCell(185, 5, $html, 0, 'L', 0, 1, 13, $y, null, null, true);
            }
        } else {
            $html = "Declaro que desconozco la ubicación del señor(a): _____________________________" .
                ", con identificación N: __________________________, padre/madre biológico del menor: " .
                "______________________________________, identificado con N: _____________________________, por lo que no " .
                "puedo aportar los datos relacionados con su certificación laboral y soy padre o madre unico responsable del menor.";


            $this->pdf->MultiCell(185, 5, $html, 0, 'L', 0, 1, 13, $y, null, null, true);
        }
    }
}
