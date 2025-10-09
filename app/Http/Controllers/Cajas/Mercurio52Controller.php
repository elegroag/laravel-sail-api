<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio52;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio52Controller extends ApplicationController
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
        $html .= "<th scope='col'>Area</th>";
        $html .= "<th scope='col'>Url</th>";
        $html .= "<th scope='col'>Tipo</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $tipoDetalle = $mtable->tipo == 'P' ? 'Principal' : 'Secundario';
            $estadoDetalle = $mtable->estado == 'A' ? 'Activo' : 'Inactivo';

            $html .= '<tr>';
            $html .= "<td>{$mtable->codmen}</td>";
            $html .= "<td>{$mtable->detalle}</td>";
            $html .= "<td>{$mtable->area->detalle}</td>";
            $html .= "<td>{$mtable->url}</td>";
            $html .= "<td>{$tipoDetalle}</td>";
            $html .= "<td>{$estadoDetalle}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{$mtable->codmen}' data-toggle='editar'>";
            $html .= "<i class='fas fa-user-edit text-white'></i>";
            $html .= '</a>';
            $html .= "<a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' data-cid='{$mtable->codmen}' data-toggle='borrar'>";
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

    public function nuevoAction()
    {
        try {
            $this->setResponse('ajax');
            $numero = (Mercurio52::max('codmen') ?? 0) + 1;
            $response = parent::successFunc('ok', $numero);

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo generar un nuevo código');

            return $this->renderObject($response, false);
        }
    }

    public function indexAction()
    {
        $campo_field = [
            'codmen' => 'Codigo',
            'detalle' => 'Detalle',
        ];
        $areas = ['' => 'Seleccione un área...'] + Gener02::where('codigo', '06')->pluck('detalle', 'cod_hijo')->toArray();

        return view('cajas.mercurio52.index', [
            'title' => 'Menu',
            'campo_filtro' => $campo_field,
            'areas' => $areas,
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio52::with('area')->whereRaw("{$this->query}")->get(),
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
            $codmen = $request->input('codmen');
            $mercurio52 = Mercurio52::where('codmen', $codmen)->first();
            if ($mercurio52 == false) {
                $mercurio52 = new Mercurio52;
            }

            return $this->renderObject($mercurio52->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codmen = $request->input('codmen');

            $this->db->begin();
            Mercurio52::where('codmen', $codmen)->delete();
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
            $codmen = $request->input('codmen');
            $detalle = $request->input('detalle');
            $codare = $request->input('codare');
            $url = $request->input('url');
            $tipo = $request->input('tipo');
            $estado = $request->input('estado');

            $this->db->begin();
            $mercurio52 = Mercurio52::firstOrNew(['codmen' => $codmen]);

            $mercurio52->detalle = $detalle;
            $mercurio52->codare = $codare;
            $mercurio52->url = $url;
            $mercurio52->tipo = $tipo;
            $mercurio52->estado = $estado;

            if (! $mercurio52->save()) {
                parent::setLogger($mercurio52->getMessages());
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
            $codmen = $request->input('codmen');
            $response = parent::successFunc('');
            $exists = Mercurio52::where('codmen', $codmen)->exists();
            if ($exists) {
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
        $_fields['codmen'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $_fields['codare'] = ['header' => 'Area', 'size' => '31', 'align' => 'C'];
        $_fields['url'] = ['header' => 'Url', 'size' => '31', 'align' => 'C'];
        $_fields['tipo'] = ['header' => 'Tipo', 'size' => '31', 'align' => 'C'];
        $_fields['estado'] = ['header' => 'Estado', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio52', $_fields, $this->query, 'Menu', $format);

        return $this->renderObject($file, false);
    }
}
