<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio13Controller extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipfun;

    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function index()
    {
        $tipopc = ['' => 'Selecciona aquí...'] + Mercurio09::pluck('detalle', 'tipopc')->toArray();
        $coddoc = ['' => 'Selecciona aquí...'] + Mercurio12::pluck('detalle', 'coddoc')->toArray();

        return view('cajas.mercurio13.index', [
            'title' => 'Documentos requeridos trabajadores',
            'campo_filtro' => [
                'tipopc' => 'Tipo servicio afiliación',
                'coddoc' => 'Tipo documento',
            ],
            'tipopc' => $tipopc,
            'coddoc' => $coddoc,
        ]);
    }

    public function aplicarFiltro(Request $request)
    {
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);
        return $this->buscar($request);
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->cantidad_pagina = $request->input('numero');
        return $this->buscar($request);
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio13._table', compact('paginate'))->render();
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio13::whereRaw("{$this->query}")->get(),
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

    public function editar(Request $request)
    {
        try {
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $mercurio13 = Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->first();
            if (! $mercurio13) {
                $mercurio13 = new Mercurio13;
            }
            $response = [
                'success' => true,
                'data' => $mercurio13->toArray()
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');
            $auto_generado = $request->input('auto_generado');
            $nota = $request->input('nota');

            $this->db->begin();

            $mercurio13 = Mercurio13::firstOrNew(['tipopc' => $tipopc, 'coddoc' => $coddoc]);
            $mercurio13->obliga = $obliga;
            $mercurio13->auto_generado = $auto_generado;
            $mercurio13->nota = $nota;

            if (! $mercurio13->save()) {
                parent::setLogger($mercurio13->getMessages());
                $this->db->rollback();
                throw new DebugException('Error no se puede guardar el registro');
            }

            $this->db->commit();
            $response = parent::successFunc('El registro se completo con éxito.');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc($e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');

            $this->db->begin();
            $deleted = Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->delete();

            if ($deleted == 0) {
                throw new DebugException('Error no se puede borrar el registro, no está disponible.');
            }

            $this->db->commit();
            $response = parent::successFunc('El registro se borro con éxito.');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc($e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function infor(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $mercurio13 = Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->first();
            if (! $mercurio13) {
                $mercurio13 = new Mercurio13;
            }
            $response = [
                'success' => true,
                'data' => $mercurio13->toArray()
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }
}
