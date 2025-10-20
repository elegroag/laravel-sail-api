<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio12;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio12Controller extends ApplicationController
{
    protected $query = '1=1';

    protected $cantidad_pagina = 10;

    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipfun = session('tipfun') ?? null;
    }

    public function showTabla($paginate)
    {
        return view('cajas.mercurio12._table', compact('paginate'))->render();
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

    public function index()
    {
        $campo_field = [
            'coddoc' => 'Codigo',
            'detalle' => 'Detalle',
        ];

        return view('cajas.mercurio12.index', [
            'title' => 'Documentos',
            'campo_filtro' => $campo_field,
        ]);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio12::whereRaw("{$this->query}")->get(),
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
            $this->setResponse('ajax');
            $coddoc = $request->input('coddoc');
            $mercurio12 = Mercurio12::where('coddoc', $coddoc)->first();
            if ($mercurio12 == false) {
                $mercurio12 = new Mercurio12;
            }

            return $this->renderObject($mercurio12->toArray(), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $coddoc = $request->input('coddoc');

            $this->db->begin();
            Mercurio12::where('coddoc', $coddoc)->delete();
            $this->db->commit();

            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Borrar el Registro');

            return $this->renderObject($response, false);
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $coddoc = $request->input('coddoc');
            $detalle = $request->input('detalle');

            $this->db->begin();
            $mercurio12 = Mercurio12::where('coddoc', $coddoc)->first();

            if (! $mercurio12) {
                $mercurio12 = new Mercurio12;
                $mercurio12->setCoddoc($coddoc);
            }

            $mercurio12->setDetalle($detalle);

            if (! $mercurio12->save()) {
                parent::setLogger($mercurio12->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = parent::successFunc('Creacion Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar/editar el Registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function validePk(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $coddoc = $request->input('coddoc');
            $response = parent::successFunc('');
            $l = Mercurio12::where('coddoc', $coddoc)->count();
            if ($l > 0) {
                $response = parent::errorFunc('El Registro ya se encuentra Digitado');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo validar la informacion');

            return $this->renderObject($response, false);
        }
    }

    public function reporte($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['coddoc'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio12', $_fields, $this->query, 'Documentos', $format);

        return $this->renderObject($file, false);
    }
}
