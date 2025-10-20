<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Subsi54;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio14Controller extends ApplicationController
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
    }

    public function index()
    {
        $tipopc = ['' => 'Selecciona aquí...'] + Mercurio09::pluck('detalle', 'tipopc')->toArray();
        $coddoc = ['' => 'Selecciona aquí...'] + Mercurio12::pluck('detalle', 'coddoc')->toArray();
        $tipsoc = ['' => 'Selecciona aquí...'] + Subsi54::pluck('detalle', 'tipsoc')->toArray();

        return view('cajas.mercurio14.index', [
            'title' => 'Documentos requeridos empleadores',
            'campo_filtro' => [
                'tipopc' => 'Tipo servicio afiliación',
                'tipsoc' => 'Tipo sociedad',
                'coddoc' => 'Tipo documento',
            ],
            'tipopc' => $tipopc,
            'coddoc' => $coddoc,
            'tipsoc' => $tipsoc,
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
        return view('cajas.mercurio14._table', compact('paginate'))->render();
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio14::whereRaw("{$this->query}")->get(),
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
            $tipsoc = $request->input('tipsoc');

            $mercurio14 = Mercurio14::where('tipopc', $tipopc)
                ->where('coddoc', $coddoc)
                ->where('tipsoc', $tipsoc)
                ->first();

            if (! $mercurio14) {
                $mercurio14 = new Mercurio14;
            }

            $response = [
                'success' => true,
                'data' => $mercurio14->toArray()
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
            $tipsoc = $request->input('tipsoc');
            $obliga = $request->input('obliga');
            $nota = $request->input('nota');
            $auto_generado = $request->input('auto_generado');

            $this->db->begin();

            $mercurio14 = Mercurio14::firstOrNew([
                'tipopc' => $tipopc,
                'coddoc' => $coddoc,
                'tipsoc' => $tipsoc,
            ]);
            $mercurio14->obliga = $obliga;
            $mercurio14->nota = $nota;
            $mercurio14->auto_generado = $auto_generado;

            if (! $mercurio14->save()) {
                parent::setLogger($mercurio14->getMessages());
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
            $tipsoc = $request->input('tipsoc');

            $this->db->begin();
            $deleted = Mercurio14::where('tipopc', $tipopc)
                ->where('coddoc', $coddoc)
                ->where('tipsoc', $tipsoc)
                ->delete();

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
}
