<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio06;
use App\Models\Mercurio28;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio06Controller extends ApplicationController
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
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= "<tr>";
            $html .= "<td>{$mtable->tipo}</td>";
            $html .= "<td>{$mtable->detalle}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-warning' title='Campos' data-toggle='campo-view' data-cid='{$mtable->tipo}'>";
            $html .= "<i class='fas fa-shield-alt text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->tipo}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->tipo}' data-toggle='borrar'>";
            $html .= "<i class='fas fa-trash text-white'></i>";
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

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input("numero");
        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = array(
            "tipo" => "Codigo",
            "detalle" => "Detalle",
        );

        $this->setParamToView("campo_filtro", $campo_field);
        return view('cajas.mercurio06.index', [
            'title' => "Gestión de Tipos Acceso",
            'campo_filtro' => $campo_field
        ]);
    }


    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == "") ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio06::whereRaw("{$this->query}")->get(),
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
            $tipo = $request->input('tipo');
            $mercurio06 = Mercurio06::where('tipo', $tipo)->first();
            if ($mercurio06 == false) {
                $mercurio06 = new Mercurio06();
            }
            return $this->renderObject($mercurio06->toArray(), false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc("Error al obtener el registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');

            $this->db->begin();
            Mercurio06::where('tipo', $tipo)->delete();
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
            $tipo = $request->input('tipo');
            $detalle = $request->input('detalle');

            $this->db->begin();

            // Buscar si ya existe un registro con el mismo tipo
            $mercurio06 = Mercurio06::where('tipo', $tipo)->first();

            if (!$mercurio06) {
                $mercurio06 = new Mercurio06();
                $mercurio06->tipo = $tipo;
            }

            $mercurio06->detalle = $detalle;

            if (!$mercurio06->save()) {
                parent::setLogger($mercurio06->getMessages());
                $this->db->rollback();
                throw new DebugException("Error al guardar el registro");
            }

            $this->db->commit();
            $response = parent::successFunc("Operación exitosa");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se pudo guardar el registro: " . $e->getMessage());
            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $response = parent::successFunc("");
            $l = Mercurio06::where('tipo', $tipo)->count();
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function validePkCampoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $campo = $request->input('campo_28');
            $response = parent::successFunc("");
            $l = Mercurio28::where('tipo', $tipo)->where('campo', $campo)->count();
            if ($l > 0) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function campo_viewAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $response = "";
            $mercurio28_collection = Mercurio28::where('tipo', $tipo)->get();
            foreach ($mercurio28_collection as $mmercurio28) {
                $response .= "<tr>";
                $response .= "<td>" . $mmercurio28->getCampo() . "</td>";
                $response .= "<td>" . $mmercurio28->getDetalle() . "</td>";
                $response .= "<td>" . $mmercurio28->getOrden() . "</td>";
                $response .= "<td class='table-actions'>";
                $response .= "<a href='#!' class='table-action btn btn-xs btn-primary' data-toggle='campo-editar' data-tipo='{$tipo}' data-campo='{$mmercurio28->getCampo()}'>";
                $response .= "<i class='fas fa-user-edit text-white'></i>";
                $response .= "</a>";
                $response .= "<a href='#!' class='table-action btn btn-xs btn-danger' data-toggle='campo-borrar' data-tipo='{$tipo}' data-campo='{$mmercurio28->getCampo()}'>";
                $response .= "<i class='fas fa-trash text-white'></i>";
                $response .= "</a>";
                $response .= "</td>";
                $response .= "</tr>";
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function guardarCampoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $campo = $request->input('campo');
            $detalle = $request->input('detalle');
            $orden = $request->input('orden');

            $this->db->begin();

            $mercurio28 = Mercurio28::where('tipo', $tipo)->where('campo', $campo)->first();

            if (!$mercurio28) {
                $mercurio28 = new Mercurio28();
                $mercurio28->setTipo($tipo);
                $mercurio28->setCampo($campo);
            }

            $mercurio28->setDetalle($detalle);
            $mercurio28->setOrden($orden);

            if (!$mercurio28->save()) {
                parent::setLogger($mercurio28->getMessages());
                $this->db->rollback();
                throw new DebugException("Error al guardar el campo");
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

    public function editarCampoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $campo = $request->input('campo');

            $mercurio28 = Mercurio28::where('tipo', $tipo)->where('campo', $campo)->first();
            if ($mercurio28 == false) {
                $mercurio28 = new Mercurio28();
            }
            return $this->renderObject($mercurio28->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function borrarCampoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $tipo = $request->input('tipo');
            $campo = $request->input('campo');

            $this->db->begin();
            Mercurio28::where('tipo', $tipo)->where('campo', $campo)->delete();
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
        $_fields["tipo"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");

        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio06", $_fields, $this->query, "Tipos Acceso", $format);
        return $this->renderObject($file, false);
    }
}
