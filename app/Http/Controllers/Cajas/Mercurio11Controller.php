<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio11;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio11Controller extends ApplicationController
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

    public function showTabla($paginate)
    {
        return view('cajas.mercurio11._table', compact('paginate'))->render();
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
            'codest' => 'Codest',
            'detalle' => 'Detalle',
        ];

        return view('cajas.mercurio11.index', [
            'title' => 'Motivos Rechazo',
            'campo_filtro' => $campo_field,
        ]);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio11::whereRaw("{$this->query}")->get(),
            $pagina,
            $this->cantidad_pagina
        );

        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService;
        $html_paginate = $consultasOldServices->showPaginate($paginate);

        $response = [
            'consulta' => $html,
            'query' => $this->query,
            'paginate' => $html_paginate,
        ];

        return $this->renderObject($response, false);
    }

    public function editar(Request $request)
    {
        try {
            $codest = $request->input('codest');
            $mercurio11 = Mercurio11::where('codest', $codest)->first();
            if ($mercurio11 == false) {
                $mercurio11 = new Mercurio11;
            }
            $response = [
                'success' => true,
                'data' => $mercurio11->toArray(),
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function borrar(Request $request)
    {
        try {
            $codest = $request->input('codest');
            $this->db->begin();
            Mercurio11::where('codest', $codest)->delete();
            $this->db->commit();

            $response = [
                'success' => true,
                'msj' => 'Proceso completado con éxito.',
            ];
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function guardar(Request $request)
    {
        try {
            $codest = $request->input('codest');
            $detalle = $request->input('detalle');

            $this->db->begin();
            $mercurio11 = Mercurio11::where('codest', $codest)->first();

            if (! $mercurio11) {
                $mercurio11 = new Mercurio11;
                $mercurio11->setCodest($codest);
                $mercurio11->setDetalle($detalle);
            } else {
                $mercurio11->setDetalle($detalle);
            }
            $mercurio11->save();
            $this->db->commit();

            $response = [
                'success' => true,
                'msj' => 'Proceso completado con éxito.',
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function validePk(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codest = $request->input('codest');
            $response = parent::successFunc('');
            $l = Mercurio11::where('codest', $codest)->count();
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
        $_fields['codest'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio11', $_fields, $this->query, 'Motivos Rechazo', $format);

        return $this->renderObject($file, false);
    }

    public function borrarFiltro()
    {
        set_flashdata('filter_mercurio11', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_mercurio11'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }
}
