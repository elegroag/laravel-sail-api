<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio51;
use App\Models\Mercurio55;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio55Controller extends ApplicationController
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
        $html .= "<th scope='col'>Categoria</th>";
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $tipoDetalle = $mtable->tipo == 'E' ? 'Evento' : 'Informativa';
            $estadoDetalle = $mtable->estado == 'A' ? 'Activo' : 'Inactivo';

            $html .= "<tr>";
            $html .= "<td>{$mtable->codare}</td>";
            $html .= "<td>{$mtable->detalle}</td>";
            $html .= "<td>{$mtable->category->detalle}</td>";
            $html .= "<td>{$tipoDetalle}</td>";
            $html .= "<td>{$estadoDetalle}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='/mercurio56/index/{$mtable->codare}' class='table-action btn btn-xs btn-primary' title='Archivos'>";
            $html .= "<i class='fas fa-images'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->codare}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= "</a>";
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->codare}' data-toggle='borrar'>";
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

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->cantidad_pagina = $request->input("numero");
        return $this->buscarAction($request);
    }

    public function nuevoAction()
    {
        try {
            $this->setResponse("ajax");
            $numero = (Mercurio55::max('codare') ?? 0) + 1;
            $response = parent::successFunc("ok", $numero);
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo generar un nuevo código");
            return $this->renderObject($response, false);
        }
    }

    public function indexAction()
    {
        $campo_field = [
            "codare" => "Codigo",
            "detalle" => "Detalle",
        ];
        $categorias = ['' => 'Seleccione una categoría...'] + Mercurio51::pluck('detalle', 'codcat')->toArray();

        return view('cajas.mercurio55.index', [
            'title' => "Areas",
            'campo_filtro' => $campo_field,
            'categorias' => $categorias
        ]);
    }


    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == "") ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio55::with('category')->whereRaw("{$this->query}")->get(),
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
            $codare = $request->input('codare');
            $mercurio55 = Mercurio55::where('codare', $codare)->first();
            if ($mercurio55 == false) {
                $mercurio55 = new Mercurio55();
            }
            return $this->renderObject($mercurio55->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc("Error al obtener el registro");
            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $codare = $request->input('codare');

            $this->db->begin();
            Mercurio55::where('codare', $codare)->delete();
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
            $codare = $request->input('codare');
            $detalle = $request->input('detalle');
            $codcat = $request->input('codcat');
            $tipo = $request->input('tipo');
            $estado = $request->input('estado');

            $this->db->begin();
            $mercurio55 = Mercurio55::firstOrNew(['codare' => $codare]);

            $mercurio55->detalle = $detalle;
            $mercurio55->codcat = $codcat;
            $mercurio55->tipo = $tipo;
            $mercurio55->estado = $estado;

            if (!$mercurio55->save()) {
                parent::setLogger($mercurio55->getMessages());
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
            $codare = $request->input('codare');
            $response = parent::successFunc("");
            $exists = Mercurio55::where('codare', $codare)->exists();
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
        $_fields["codare"] = array('header' => "Codigo", 'size' => "15", 'align' => "C");
        $_fields["detalle"] = array('header' => "Detalle", 'size' => "31", 'align' => "C");
        $_fields["tipo"] = array('header' => "Tipo", 'size' => "31", 'align' => "C");
        $_fields["codcat"] = array('header' => "Categoria", 'size' => "31", 'align' => "C");
        $_fields["estado"] = array('header' => "Estado", 'size' => "31", 'align' => "C");
        $consultasOldServices = new GeneralService();
        $file = $consultasOldServices->createReport("mercurio55", $_fields, $this->query, "Areas", $format);
        return $this->renderObject($file, false);
    }
}
