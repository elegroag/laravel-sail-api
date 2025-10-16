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
        return view('cajas.mercurio06._table', compact('paginate'))->render();
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
            'tipo' => 'Codigo',
            'detalle' => 'Detalle',
        ];

        return view('cajas.mercurio06.index', [
            'title' => 'Gestión de Tipos Acceso',
            'campo_filtro' => $campo_field,
        ]);
    }

    public function buscar(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio06::whereRaw("{$this->query}")->get(),
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
            $tipo = $request->input('tipo');
            $mercurio06 = Mercurio06::where('tipo', $tipo)->first();
            if ($mercurio06 == false) {
                $mercurio06 = new Mercurio06;
            }

            return $this->renderObject($mercurio06->toArray(), false);
        } catch (DebugException $e) {
            parent::setLogger($e->getMessage());
            $response = parent::errorFunc('Error al obtener el registro');

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipo = $request->input('tipo');

            $this->db->begin();
            Mercurio06::where('tipo', $tipo)->delete();
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
            $tipo = $request->input('tipo');
            $detalle = $request->input('detalle');

            $this->db->begin();

            // Buscar si ya existe un registro con el mismo tipo
            $mercurio06 = Mercurio06::where('tipo', $tipo)->first();

            if (! $mercurio06) {
                $mercurio06 = new Mercurio06;
                $mercurio06->tipo = $tipo;
            }

            $mercurio06->detalle = $detalle;

            if (! $mercurio06->save()) {
                parent::setLogger($mercurio06->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = parent::successFunc('Operación exitosa');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se pudo guardar el registro: ' . $e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function validePk(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipo = $request->input('tipo');
            $response = parent::successFunc('');
            $l = Mercurio06::where('tipo', $tipo)->count();
            if ($l > 0) {
                $response = parent::errorFunc('El Registro ya se encuentra Digitado');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo validar la informacion');

            return $this->renderObject($response, false);
        }
    }

    public function validePkCampo(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipo = $request->input('tipo');
            $campo = $request->input('campo_28');
            $response = parent::successFunc('');
            $l = Mercurio28::where('tipo', $tipo)->where('campo', $campo)->count();
            if ($l > 0) {
                $response = parent::errorFunc('El Registro ya se encuentra Digitado');
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = parent::errorFunc('No se pudo validar la informacion');

            return $this->renderObject($response, false);
        }
    }

    public function campoView(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $response = '';
            $mercurio28_collection = Mercurio28::where('tipo', $tipo)->get();
            $response = [
                'collection' => $mercurio28_collection,
                'success' => true,
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => 'No se pudo obtener la informacion'
            ];
        }
        return $this->renderObject($response, false);
    }

    public function guardarCampo(Request $request)
    {
        try {
            $this->db->begin();
            $tipo = $request->input('tipo');
            $campo = $request->input('campo');
            $detalle = $request->input('detalle');
            $orden = $request->input('orden');

            $mercurio28 = Mercurio28::where('tipo', $tipo)->where('campo', $campo)->first();

            if (! $mercurio28) {
                $mercurio28 = new Mercurio28;
                $mercurio28->setTipo($tipo);
                $mercurio28->setCampo($campo);
            }

            $mercurio28->setDetalle($detalle);
            $mercurio28->setOrden($orden);

            if (! $mercurio28->save()) {
                $this->db->rollback();
                throw new DebugException('Error al guardar el campo');
            }

            $this->db->commit();

            $response = [
                'success' => true,
                'msj' => 'Movimiento Realizado Con Exito'
            ];
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'success' => false,
                'msj' => 'No se pudo realizar el movimiento: ' . $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }

    public function editarCampo(Request $request)
    {
        try {
            $tipo = $request->input('tipo');
            $campo = $request->input('campo');

            $mercurio28 = Mercurio28::where('tipo', $tipo)->where('campo', $campo)->first();
            if ($mercurio28 == false) {
                $mercurio28 = new Mercurio28;
            }
            $response = [
                'success' => true,
                'data' => $mercurio28->toArray()
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }

    public function borrarCampo(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipo = $request->input('tipo');
            $campo = $request->input('campo');

            $this->db->begin();
            Mercurio28::where('tipo', $tipo)->where('campo', $campo)->delete();
            $this->db->commit();
            $response = parent::successFunc('Movimiento Realizado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se pudo realizar el movimiento');

            return $this->renderObject($response, false);
        }
    }

    public function reporte($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['tipo'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];

        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio06', $_fields, $this->query, 'Tipos Acceso', $format);

        return $this->renderObject($file, false);
    }
}
