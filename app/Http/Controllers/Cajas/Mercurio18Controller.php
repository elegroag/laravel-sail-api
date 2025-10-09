<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio18;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio18Controller extends ApplicationController
{
    protected $query = '1=1';

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
        $html = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= '<tr>';
        $html .= "<th scope='col'>Codigo</th>";
        $html .= "<th scope='col'>Detalle</th>";
        $html .= "<th scope='col'></th>";
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= '<tr>';
            $html .= "<td>{$mtable->getCodigo()}</td>";
            $html .= "<td>{$mtable->getDetalle()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->getCodigo()}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= '</a>';
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->getCodigo()}' data-toggle='borrar'>";
            $html .= "<i class='fas fa-trash text-white'></i>";
            $html .= '</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    public function aplicarFiltroAction(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);

        return $this->buscarAction($request);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');

        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = [
            'codigo' => 'Codigo',
            'detalle' => 'Detalle',
        ];

        return view('cajas.mercurio18.index', [
            'title' => 'Preguntas Seguridad',
            'campo_filtro' => $campo_field,
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio18::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);

        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;

        return $this->renderObject($response, false);
    }

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codigo = $request->input('codigo');
            $mercurio18 = Mercurio18::where('codigo', $codigo)->first();
            if ($mercurio18 == false) {
                $mercurio18 = new Mercurio18;
            }

            return $this->renderObject($mercurio18->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codigo = $request->input('codigo');

            $this->db->begin();
            Mercurio18::where('codigo', $codigo)->delete();
            $this->db->commit();

            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Borrar el Registro');

            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codigo = $request->input('codigo');
            $detalle = $request->input('detalle');

            $this->db->begin();
            $mercurio18 = Mercurio18::where('codigo', $codigo)->first();

            if (! $mercurio18) {
                $mercurio18 = new Mercurio18;
                $mercurio18->setCodigo($codigo);
            }

            $mercurio18->setDetalle($detalle);

            if (! $mercurio18->save()) {
                parent::setLogger($mercurio18->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar/editar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codigo = $request->input('codigo');
            $response = parent::successFunc('');
            $l = Mercurio18::where('codigo', $codigo)->count();
            if ($l > 0) {
                $response = parent::errorFunc('El Registro ya se encuentra Digitado');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo validar la informacion');

            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['codigo'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio18', $_fields, $this->query, 'Preguntas Seguridad', $format);

        return $this->renderObject($file, false);
    }
}
