<?php

namespace App\Services\Formularios\Afiliacion;

use App\Library\Collections\ParamsEmpresa;
use App\Services\Formularios\Documento;


class FormularioActualizadatos extends Documento
{

    public function main()
    {
        $empresa = $this->request->getParam('empresa');
        $campos = $this->request->getParam('campos');

        $this->pdf->SetTitle("Formulario afiliación de empresa con NIT {$empresa['nit']}, COMFACA");
        $this->pdf->SetAuthor("{$empresa['repleg']}, COMFACA");
        $this->pdf->SetSubject("Formulario de afiliación a COMFACA");
        $this->pdf->SetCreator("Plataforma Web: comfacaenlinea.com.co, COMFACA");
        $this->pdf->SetKeywords('COMFACA');

        $this->pdf->Image('public/docs/formulario_mercurio/fomulario_actualizacion_empresa_parte_1.jpg', 0, 0, "216", "280");
        $this->bloqueEmpresa($empresa, $campos);
        $page = storage_path('public/docs/sello-firma.png');
        $this->pdf->Image($page, 160, 275, 30, 20, '');

        $this->pdf->AddPage();
        $this->pdf->Image('public/docs/formulario_mercurio/fomulario_actualizacion_empresa_parte_2.jpg', 0, 0, "216", "280");
        $page = storage_path('public/docs/sello-firma.png');
        $this->pdf->Image($page, 160, 275, 30, 20, '');
    }

    function bloqueEmpresa($empresa, $campos)
    {
        $_codciu = ParamsEmpresa::getCiudades();
        $_codzon = ParamsEmpresa::getZonas();
        $_coddep = ParamsEmpresa::getDepartamentos();

        $tipos_documentos = $this->getTiposDocumentos();
        $this->pdf->setY(58);
        $this->pdf->setX(27);
        $this->pdf->Cell(53, 6, '(' . $tipos_documentos["{$empresa['coddoc']}"] . ') ' . $empresa['nit'], 0, 0, 'L');

        $this->pdf->setX(86);
        $this->pdf->Cell(35, 6, $empresa['digver'], 0, 0, 'L');

        $this->pdf->setY(62);
        $this->pdf->setX(126);
        $this->pdf->Cell(60, 5, $_codzon["{$campos['codzon']}"], 0, 0, 'L');

        $this->pdf->setY(73);
        $this->pdf->setX(27);
        $this->pdf->Cell(120, 5, @$campos['razsoc'], 0, 0, 'L');

        $this->pdf->setY(85);
        $this->pdf->setX(27);
        $this->pdf->Cell(180, 5, @$campos['prinom'] . ' ' . @$campos['segnom'] . ' ' . @$campos['priape'] . ' ' . @$campos['segape'], 0, 0, 'L');

        $this->pdf->setX(152);
        $this->pdf->Cell(60, 5, @$campos['coddocrepleg'] . '. ' . @$campos['cedrep'], 0, 0, 'L');

        //Datos de ubicación
        $this->pdf->setY(105);
        $this->pdf->setX(27);
        $this->pdf->Cell(60, 5, @$campos['dirpri'], 0, 0, 'L');

        $this->pdf->SetFont('helvetica', '', 7.7);
        $this->pdf->setX(85);
        $this->pdf->Cell(60, 5, @$campos['barrio_notificacion'], 0, 0, 'L');

        $this->pdf->setY(102);
        $this->pdf->setX(123);
        $this->pdf->Cell(60, 5, $_codciu["{$campos['codciu']}"], 0, 0, 'L');

        $departamento = substr($campos['codciu'], 0, 2);
        $this->pdf->setY(107);
        $this->pdf->setX(150);
        $this->pdf->Cell(60, 5, $_coddep["{$departamento}"], 0, 0, 'L');

        //telefono
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->setY(119);
        $this->pdf->setX(27);
        $this->pdf->Cell(60, 5, @$campos['telefono'], 0, 0, 'L');

        $this->pdf->setX(85);
        $this->pdf->Cell(60, 5, @$campos['celular'], 0, 0, 'L');

        $this->pdf->SetFont('helvetica', '', 7.5);
        $this->pdf->setX(123);
        $this->pdf->Cell(65, 5, @$campos['email'], 0, 0, 'L');

        $this->pdf->SetFont('helvetica', '', 9);

        //marcar x
        $this->pdf->setY(151);
        $this->pdf->setX(33);
        $this->pdf->Cell(60, 5, 'X', 0, 0, 'L');

        //direccion comercial
        $this->pdf->setY(165);
        $this->pdf->setX(26);
        $this->pdf->Cell(60, 5, @$campos['direccion'], 0, 0, 'L');

        $this->pdf->SetFont('helvetica', '', 7.7);
        $this->pdf->setX(77);
        $this->pdf->Cell(60, 5, @$campos['barrio_comercial'], 0, 0, 'L');

        $this->pdf->setY(163);
        $this->pdf->setX(110);
        $this->pdf->Cell(60, 5, $_codciu["{$campos['ciupri']}"], 0, 0, 'L');

        $departamento = substr($campos['ciupri'], 0, 2);
        $this->pdf->setY(167);
        $this->pdf->setX(146);
        $this->pdf->Cell(60, 5, $_coddep["{$departamento}"], 0, 0, 'L');

        //telefono fijo
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->setY(180);
        $this->pdf->setX(27);
        $this->pdf->Cell(60, 5, @$campos['telpri'], 0, 0, 'L');

        $this->pdf->setX(55);
        $this->pdf->Cell(50, 5, @$campos['celpri'], 0, 0, 'L');

        $this->pdf->setX(90);
        $this->pdf->Cell(80, 5, @$campos['emailpri'], 0, 0, 'L');


        $this->pdf->MultiCell(195, 5, "", 0, 'L', 0);
        return $this->pdf;
    }

    function getTiposDocumentos()
    {
        return array(
            1 => 'CC',
            10 => 'TMF',
            11 => 'CD',
            12 => 'ISE',
            13 => 'V',
            14 => 'PT',
            2 => 'TI',
            3 => 'NIT',
            4 => 'CE',
            5 => 'NU',
            6 => 'PA',
            7 => 'RC',
            8 => 'PEP',
            9 => 'CB'
        );
    }
}
