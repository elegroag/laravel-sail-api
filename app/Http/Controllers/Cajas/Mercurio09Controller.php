<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio14;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio09Controller extends ApplicationController
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
        return view('cajas.mercurio09._table', compact('paginate'))->render();
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
        $apiRest = Comman::Api();
        $apiRest->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );
        $datos_captura = $apiRest->toArray();
        $_tipsoc = [];
        foreach ($datos_captura['tipo_sociedades'] as $data) {
            $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }

        return view('cajas.mercurio09.index', [
            'title' => 'Tipos Opciones',
            'campo_filtro' => [
                'tipopc' => 'Codigo',
                'detalle' => 'Detalle',
            ],
            '_tipsoc' => $_tipsoc,
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio09::whereRaw("{$this->query}")->get(),
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
            $tipopc = $request->input('tipopc');
            $mercurio09 = Mercurio09::where('tipopc', $tipopc)->first();
            if ($mercurio09 == false) {
                $mercurio09 = new Mercurio09;
            }

            $response = [
                'success' => true,
                'data' => $mercurio09->toArray(),
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => 'Error al obtener el registro ' . $e->getMessage(),
            ];
        }
        return $this->renderObject($response, false);
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');

            $this->db->begin();
            Mercurio09::where('tipopc', $tipopc)->delete();
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
            $tipopc = $request->input('tipopc');
            $detalle = $request->input('detalle');
            $dias = $request->input('dias');

            $this->db->begin();
            $mercurio09 = Mercurio09::where('tipopc', $tipopc)->first();

            if (! $mercurio09) {
                $mercurio09 = new Mercurio09;
                $mercurio09->setTipopc($tipopc);
            }

            $mercurio09->setDetalle($detalle);
            $mercurio09->setDias($dias);

            if (! $mercurio09->save()) {
                $this->db->rollback();
                throw new DebugException('Error al guardar el registro');
            }

            $this->db->commit();
            $response = 'Creacion Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se puede guardar/editar el Registro: ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');

            $response = 'Validacion Exitosa';
            $l = Mercurio09::where('tipopc', $tipopc)->count();
            if ($l > 0) {
                $response = 'El Registro ya se encuentra Digitado';
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se pudo validar la informacion';

            return $this->renderObject($response, false);
        }
    }

    public function archivosViewAction(Request $request)
    {
        try {
            $this->setResponse('view');
            $tipopc = $request->input('tipopc');
            $mercurio12 = Mercurio12::all();

            return view('cajas.mercurio09.tmp.archivos_view', [
                'tipopc' => $tipopc,
                'mercurio12' => $mercurio12,
                'mercurio13' => Mercurio13::where('tipopc', $tipopc)->get(),
            ]);
        } catch (DebugException $e) {
            return $this->renderObject([
                'flag' => false,
                'msg' => 'No se pudo validar la informacion',
            ]);
        }
    }

    public function guardarArchivosAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $acc = $request->input('acc');

            $this->db->begin();
            if ($acc == '1') {
                $mercurio13 = new Mercurio13;
                $mercurio13->setTipopc($tipopc);
                $mercurio13->setCoddoc($coddoc);
                $mercurio13->setObliga('N');
                if (! $mercurio13->save()) {
                    $this->db->rollback();
                    throw new DebugException('Error al guardar archivo');
                }
            } else {
                Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->delete();
            }
            $this->db->commit();
            $response = 'Movimiento Realizado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se pudo realizar el movimiento: ' . $e->getMessage();
            return $this->renderObject($response, false);
        }
    }

    public function obligaArchivosAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');

            $this->db->begin();
            Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->update(['obliga' => $obliga]);
            $this->db->commit();
            $response = 'Movimiento Realizado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se pudo realizar el movimiento: ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function archivosEmpresaViewAction(Request $request)
    {
        try {
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $mercurio12 = Mercurio12::all();

            return view('cajas.mercurio09.tmp.archivos_empresas', [
                'tipopc' => $tipopc,
                'tipsoc' => $tipsoc,
                'mercurio12' => $mercurio12,
                'mercurio14' => Mercurio14::all(),
            ]);
        } catch (DebugException $e) {
            return $this->renderObject([
                'flag' => false,
                'msg' => 'No se pudo validar la informacion',
            ]);
        }
    }

    public function guardarEmpresaArchivosAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $coddoc = $request->input('coddoc');
            $acc = $request->input('acc');

            $this->db->begin();
            if ($acc == '1') {
                $mercurio14 = new Mercurio14;
                $mercurio14->setTipopc($tipopc);
                $mercurio14->setTipsoc($tipsoc);
                $mercurio14->setCoddoc($coddoc);
                $mercurio14->setObliga('N');
                if (! $mercurio14->save()) {
                    $this->db->rollback();
                    throw new DebugException('Error al guardar archivo de empresa');
                }
            } else {
                Mercurio14::where('tipopc', $tipopc)->where('tipsoc', $tipsoc)->where('coddoc', $coddoc)->delete();
            }
            $this->db->commit();
            $response = 'Movimiento Realizado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se pudo realizar el movimiento: ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function obligaEmpresaArchivosAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $tipopc = $request->input('tipopc');
            $tipsoc = $request->input('tipsoc');
            $coddoc = $request->input('coddoc');
            $obliga = $request->input('obliga');

            $this->db->begin();
            Mercurio14::where('tipopc', $tipopc)
                ->where('tipsoc', $tipsoc)
                ->where('coddoc', $coddoc)
                ->update(['obliga' => $obliga]);
            $this->db->commit();
            $response = 'Movimiento Realizado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se pudo realizar el movimiento: ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function reporteAction($format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['tipopc'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $_fields['dias'] = ['header' => 'Dias', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio09', $_fields, $this->query, 'Tipos Opciones', $format);

        return $this->renderObject($file, false);
    }
}
