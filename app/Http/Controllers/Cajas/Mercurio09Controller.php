<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio14;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio09Controller extends ApplicationController
{

    protected $query = "1=1";
    protected $cantidad_pagina = 10;
    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
        $this->cantidad_pagina = $this->numpaginate ?? 10;
    }

    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Tipopc</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'>Dias</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->getTipopc()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td>{$mtable->getDias()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a type='button' class='table-action btn btn-xs btn-warning' title='Documento' data-cid='{$mtable->getTipopc()}' data-toggle='archivos-view'>";
            $html .= "<i class='fas fa-file-image text-white'></i>";
            $html .= "</a>";
            if (!in_array($mtable->getTipopc(), array("1", "3", "4", "7"))) {
                $html .= "<a type='button' class='table-action btn btn-xs btn-success' title='Documento' data-cid='{$mtable->getTipopc()}' data-toggle='empresa-view'>";
                $html .= "<i class='fas fa-eye text-white'></i>";
                $html .= "</a>";
            }
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getTipopc()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        return $html;
    }

    public function aplicarFiltroAction(Request $request)
    {
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery($request);
        return $this->buscarAction($request);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->cantidad_pagina = $request->input("numero");
        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = array(
            "tipopc" => "Codigo",
            "detalle" => "Detalle",
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("title", "Tipos Opciones");

        $apiRest = Comman::Api();
        $apiRest->runCli(
            array(
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            )
        );
        $datos_captura = $apiRest->toArray();
        $_tipsoc = array();
        foreach ($datos_captura['tipo_sociedades'] as $data) {
            $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }
        $this->setParamToView("_tipsoc", $_tipsoc);

        return view('cajas.mercurio09.index', [
            'title' => "Tipos Opciones",
            'campo_filtro' => $campo_field,
            '_tipsoc' => $_tipsoc
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == "") ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio09::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate);

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        return $this->renderObject($response, false);
    }

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $mercurio09 = Mercurio09::where('tipopc', $tipopc)->first();
            if ($mercurio09 == false) {
                $mercurio09 = new Mercurio09();
            }
            return $this->renderObject($mercurio09->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("Error al obtener el registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');

            $this->db->begin();
            Mercurio09::where('tipopc', $tipopc)->delete();
            $this->db->commit();

            $response = parent::successFunc("Borrado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se puede Borrar el Registro");
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $detalle = $request->input('detalle');
            $dias = $request->input('dias');

            $this->db->begin();
            $mercurio09 = Mercurio09::where('tipopc', $tipopc)->first();

            if (!$mercurio09) {
                $mercurio09 = new Mercurio09();
                $mercurio09->setTipopc($tipopc);
            }

            $mercurio09->setDetalle($detalle);
            $mercurio09->setDias($dias);

            if (!$mercurio09->save()) {
                parent::setLogger($mercurio09->getMessages());
                $this->db->rollback();
                throw new DebugException("Error al guardar el registro");
            }

            $this->db->commit();
            $response = parent::successFunc("Creacion Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se puede guardar/editar el Registro: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $response = parent::successFunc("");
            $l = Mercurio09::where('tipopc', $tipopc)->count();
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function archivos_viewAction(Request $request)
    {
        try {
            $tipopc = $request->input('tipopc');
            $mercurio12 = Mercurio12::all();
            return view('cajas.mercurio09.tmp.archivos_view', [
                'tipopc' => $tipopc,
                'mercurio12' => $mercurio12,
                "Mercurio13" => new Mercurio13()
            ]);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderText($response);
        }
    }

    public function guardarArchivosAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $acc = $request->input('acc');

            $this->db->begin();
            if ($acc == "1") {
                $mercurio13 = new Mercurio13();
                $mercurio13->setTipopc($tipopc);
                $mercurio13->setCoddoc($coddoc);
                $mercurio13->setObliga("N");
                if (!$mercurio13->save()) {
                    parent::setLogger($mercurio13->getMessages());
                    $this->db->rollback();
                    throw new DebugException("Error al guardar archivo");
                }
            } else {
                Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->delete();
            }
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se pudo realizar el movimiento: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function obligaArchivosAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');

            $this->db->begin();
            Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->update(['obliga' => $obliga]);
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function archivos_empresa_viewAction(Request $request)
    {
        try {
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $mercurio12 = Mercurio12::all();
            return view('cajas.mercurio09.tmp.archivos_empresas', [
                'tipopc' => $tipopc,
                'tipsoc' => $tipsoc,
                'mercurio12' => $mercurio12,
                "Mercurio14" => new Mercurio14()
            ]);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderText($response);
        }
    }

    public function guardarEmpresaArchivosAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $coddoc = $request->input('coddoc');
            $acc = $request->input('acc');

            $this->db->begin();
            if ($acc == "1") {
                $mercurio14 = new Mercurio14();
                $mercurio14->setTipopc($tipopc);
                $mercurio14->setTipsoc($tipsoc);
                $mercurio14->setCoddoc($coddoc);
                $mercurio14->setObliga("N");
                if (!$mercurio14->save()) {
                    parent::setLogger($mercurio14->getMessages());
                    $this->db->rollback();
                    throw new DebugException("Error al guardar archivo de empresa");
                }
            } else {
                Mercurio14::where('tipopc', $tipopc)->where('tipsoc', $tipsoc)->where('coddoc', $coddoc)->delete();
            }
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se pudo realizar el movimiento: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function obligaEmpresaArchivosAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');

            $this->db->begin();
            Mercurio14::where('tipopc', $tipopc)
                ->where('tipsoc', $tipsoc)
                ->where('coddoc', $coddoc)
                ->update(['obliga' => $obliga]);
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse("ajax");
        $_fields = array();
        $_fields["tipopc"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["dias"] = array('header' => "Dias", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio09", $_fields, $this->query, "Tipos Opciones", $format);
        return $this->renderObject($file, false);
    }
}
