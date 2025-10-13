<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Services\Reportes\ReporteSolicitudes;
use App\Services\Srequest;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;

class ReportesolController extends ApplicationController
{
    /**
     * pagination variable
     *
     * @var Pagination
     */
    protected $pagination;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->pagination = new Pagination;
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        $m09 = (new Mercurio09)->find();
        $tipo_solicitudes = [];
        foreach ($m09 as $model09) {
            $tipo_solicitudes[$model09->getTipopc()] = $model09->getDetalle();
        }
        $this->setParamToView('tipo_solicitudes', $tipo_solicitudes);
        $this->setParamToView('title', 'Reportes de Solicitudes');
    }

    public function procesarAction(Request $request)
    {
        $this->setResponse('ajax');
        $tipo = $request->input('tipo');
        $estado = $request->input('estado');
        $fecha_solicitud = $request->input('fecha_solicitud');
        $fecha_aprueba = $request->input('fecha_aprueba');

        $reporte = new ReporteSolicitudes;

        $file = $reporte->main(
            new Srequest(
                [
                    'estado' => $estado,
                    'fecha_solicitud' => $fecha_solicitud,
                    'fecha_aprueba' => $fecha_aprueba,
                    'tipo' => $tipo,
                ]
            )
        );

        $this->renderObject([
            'succcess' => true,
            'filename' => basename($file),
            'url' => 'principal/download_global',
            'file' => $file,
        ]);
    }
}
