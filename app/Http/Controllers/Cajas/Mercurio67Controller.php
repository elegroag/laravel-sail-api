<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio67;
use App\Services\Tag;
use App\Services\Utils\GeneralService;
use Illuminate\Http\Request;
use App\Services\Utils\Paginate;

class Mercurio67Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 0;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio67._table', [
            'paginate' => $paginate,
        ]);
    }

    public function aplicarFiltroAction(Request $request)
    {
        $this->setResponse('ajax');
        $consultasOldServices = new GeneralService;
        $this->query = $consultasOldServices->converQuery($request);
        return $this->buscarAction($request);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->setResponse('ajax');
        $this->cantidad_pagina = $request->input('numero');
        return $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = [
            'codcla' => 'Codigo',
            'detalle' => 'Detalle',
        ];

        return view('cajas.mercurio67.index', [
            'title' => 'Clasificaciones',
            'campo_filtro' => $campo_field
        ]);
    }

    public function nuevoAction()
    {
        $this->setResponse('ajax');
        $numero = Mercurio67::max('codcla') + 1;
        $response = parent::successFunc('ok', $numero);
        $this->renderObject($response, false);
    }

    public function buscarAction(Request $request)
    {
        $this->setResponse('ajax');
        $pagina = $request->input('pagina');
        if ($pagina == '') {
            $pagina = 1;
        }
        $paginate = Paginate::execute(Mercurio67::whereRaw("$this->query"), $pagina, $this->cantidad_pagina);
        $html = self::showTabla($paginate);
        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);
        $response['consulta'] = $html;
        $response['paginate'] = $html_paginate;
        $this->renderObject($response, false);
    }

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codcla = $request->input('codcla');
            $mercurio67 = Mercurio67::where('codcla', $codcla)->first();
            if ($mercurio67 == false) {
                $mercurio67 = new Mercurio67;
            }
            $this->renderObject($mercurio67->toArray(), false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $this->db->rollback();
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codcla = $request->input('codcla');

            $response = $this->db->begin();
            Mercurio67::where('codcla', $codcla)->delete();
            $this->db->commit();
            $response = 'Borrado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede Borrar el Registro';
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codcla = $request->input('codcla');
            $detalle = $request->input('detalle');

            $response = $this->db->begin();
            $mercurio67 = new Mercurio67;

            $mercurio67->setCodcla($codcla);
            $mercurio67->setDetalle($detalle);
            if (! $mercurio67->save()) {
                parent::setLogger($mercurio67->getMessages());
                $this->db->rollback();
            }
            $this->db->commit();
            $response = 'Creacion Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se puede guardar/editar el Registro';

            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codcla = $request->input('codcla');
            $response = '';
            $l = Mercurio67::where('codcla', $codcla)->count();
            if ($l > 0) {
                $response = 'El Registro ya se encuentra Digitado';
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se pudo validar la informacion';
            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['codcla'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio67', $_fields, $this->query, 'Clasificaciones', $format);

        return $this->renderObject($file, false);
    }
}
