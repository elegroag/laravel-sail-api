<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio51;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio51Controller extends ApplicationController
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
        $html .= "<th scope='col'>Codigo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'>Categoria Padre</th>";
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $tipoDetalle = $mtable->tipo == 'P' ? 'Producto' : ($mtable->tipo == 'S' ? 'Servicio' : 'N/A');
            $estadoDetalle = $mtable->estado == 'A' ? 'Activo' : 'Inactivo';
            $parentDetalle = $mtable->parent->detalle ?? 'Principal';

            $html .= "<tr>";
            $html .= "<td>{$mtable->codcat}</td>";
            $html .= "<td>{$mtable->detalle}</td>";
            $html .= "<td>{$parentDetalle}</td>";
            $html .= "<td>{$tipoDetalle}</td>";
            $html .= "<td>{$estadoDetalle}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->codcat}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->codcat}' data-toggle='borrar'>";
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
        $this->query = $consultasOldServices->converQuery();
        return $this->buscarAction($request);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->cantidad_pagina = $request->input("numero");
        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = [
            "codcat" => "Codigo",
            "detalle" => "Detalle",
        ];
        $categorias = ['' => 'Principal'] + Mercurio51::pluck('detalle', 'codcat')->toArray();

        return view('cajas.mercurio51.index', [
            'title' => "Categorias",
            'campo_filtro' => $campo_field,
            'categorias' => $categorias
        ]);
    }

    public function nuevoAction()
    {
        try {
            $this->setResponse("ajax");
            $numero = (Mercurio51::max('codcat') ?? 0) + 1;
            $response = parent::successFunc("ok", $numero);
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo generar un nuevo código");
            return $this->renderObject($response, false);
        }
    }


    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == "") ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio51::with('parent')->whereRaw("{$this->query}")->get(),
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
            $codcat = $request->input('codcat');
            $mercurio51 = Mercurio51::where('codcat', $codcat)->first();
            if ($mercurio51 == false) {
                $mercurio51 = new Mercurio51();
            }
            return $this->renderObject($mercurio51->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("Error al obtener el registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $codcat = $request->input('codcat');

            $this->db->begin();
            Mercurio51::where('codcat', $codcat)->delete();
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
            $codcat = $request->input('codcat');
            $detalle = $request->input('detalle');
            $tipo = $request->input('tipo');
            $estado = $request->input('estado');
            $codcat_padre = $request->input('codcat_padre');

            $this->db->begin();
            $mercurio51 = Mercurio51::firstOrNew(['codcat' => $codcat]);

            $mercurio51->detalle = $detalle;
            $mercurio51->tipo = $tipo;
            $mercurio51->estado = $estado;
            $mercurio51->codcat_padre = $codcat_padre ?: null;

            if (!$mercurio51->save()) {
                parent::setLogger($mercurio51->getMessages());
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
            $codcat = $request->input('codcat');
            $response = parent::successFunc("");
            $exists = Mercurio51::where('codcat', $codcat)->exists();
            if ($exists) {
                $response = parent::errorFunc("El Registro ya se encuentra Digitado");
            }
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo validar la informacion");
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse("ajax");
        $_fields = array();
        $_fields["codcat"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["tipo"] = array('header' => "Tipo", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio51", $_fields, $this->query, "Categorias", $format);
        return $this->renderObject($file, false);
    }
}
