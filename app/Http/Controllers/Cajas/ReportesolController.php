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

    public function __construct()
    {
        
        
       
        
        
        
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
        $tipo = $request->input("tipo");
        $estado = $request->input("estado");
        $fecha_solicitud = $request->input("fecha_solicitud");
        $fecha_aprueba = $request->input("fecha_aprueba");

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
