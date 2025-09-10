<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportesolController extends ApplicationController
{

    /**
     * pagination variable
     * @var Pagination
     */
    protected $pagination;

    public function initialize()
    {
        $this->setPersistance(false);
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        $this->setTemplateAfter('main');
        Services::Init();
        $this->pagination = new Pagination();
    }

    public function indexAction()
    {
        $m09 = (new Mercurio09)->find();
        $tipo_solicitudes = array();
        foreach ($m09 as $model09) {
            $tipo_solicitudes[$model09->getTipopc()] = $model09->getDetalle();
        }
        $this->setParamToView("tipo_solicitudes", $tipo_solicitudes);
        $this->setParamToView("title", "Reportes de Solicitudes");
    }

    public function procesarAction()
    {
        $this->setResponse('ajax');
        $tipo = $this->getPostParam("tipo");
        $estado = $this->getPostParam("estado");
        $fecha_solicitud = $this->getPostParam("fecha_solicitud");
        $fecha_aprueba = $this->getPostParam("fecha_aprueba");

        $reporte = new ReporteSolicitudes();

        $file = $reporte->main(
            new Request(
                array(
                    'estado' => $estado,
                    'fecha_solicitud' => $fecha_solicitud,
                    'fecha_aprueba' => $fecha_aprueba,
                    'tipo' => $tipo
                )
            )
        );

        $this->renderObject(array(
            'succcess' => true,
            'filename' => basename($file),
            'url' => 'principal/download_global',
            'file' => $file
        ));
    }
}
