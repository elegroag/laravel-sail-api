<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio04;
use App\Models\Mercurio05;
use App\Models\Mercurio08;
use App\Models\Mercurio09;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Paginate;
use Illuminate\Http\Request;

class Mercurio04Controller extends ApplicationController
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
        return view('cajas.mercurio04._table', compact('paginate'))->render();
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
            'codofi' => 'Codigo',
            'detalle' => 'Detalle',
        ];
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_ciudades_departamentos',
                'params' => false,
            ]
        );
        $out = $ps->toArray();
        $_codciu = [];
        foreach ($out['ciudades'] as $mcodciu) {
            $_codciu[$mcodciu['codciu']] = $mcodciu['detciu'];
        }
        return view('cajas.mercurio04.index', [
            'campo_filtro' => $campo_field,
            'title' => 'Oficinas',
            'ciudades' => $_codciu,
            'principal' => ['S' => 'Sí', 'N' => 'No'],
            'estados' => ['A' => 'Activo', 'I' => 'Inactivo'],
        ]);
    }

    public function buscarAction(Request $request)
    {
        $pagina = ($request->input('pagina') == '') ? 1 : $request->input('pagina');

        $paginate = Paginate::execute(
            Mercurio04::whereRaw("{$this->query}")->get(),
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

    public function editarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codofi = $request->input('codofi');
            $mercurio04 = Mercurio04::whereRaw("codofi = '$codofi'")->first();
            if ($mercurio04 == false) {
                $mercurio04 = new Mercurio04;
            }

            return $this->renderObject($mercurio04->toArray(), false);
        } catch (DebugException $e) {
            $this->db->rollback();
        }
    }

    public function borrarAction(Request $request)
    {
        try {
            try {
                $this->setResponse('ajax');
                $codofi = $request->input('codofi');

                $response = $this->db->begin();
                Mercurio04::whereRaw("codofi = '$codofi'")->delete();
                $this->db->commit();
                $response = 'Borrado Con Exito';

                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = 'No se puede Borrar el Registro ' . $e->getMessage();
            return $this->renderObject($response, false);
        }
    }

    public function guardarAction(Request $request)
    {
        try {
            try {
                $this->setResponse('ajax');
                $codofi = $request->input('codofi');
                $detalle = $request->input('detalle');
                $principal = $request->input('principal');
                $estado = $request->input('estado');

                $response = $this->db->begin();
                $mercurio04 = new Mercurio04;

                $mercurio04->setCodofi($codofi);
                $mercurio04->setDetalle($detalle);
                $mercurio04->setPrincipal($principal);
                $mercurio04->setEstado($estado);
                if (! $mercurio04->save()) {
                    $this->db->rollback();
                }
                $this->db->commit();
                $response = 'Creacion Con Exito';

                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = 'No se puede guardar/editar el Registro ' . $e->getMessage();
            return $this->renderObject($response, false);
        }
    }

    public function validePkAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codofi = $request->input('codofi');
            $response = 'El Registro ya se encuentra Digitado';
            $l = $this->Mercurio04->count('*', "conditions: codofi = '$codofi'");
            if ($l > 0) {
                $response = 'El Registro ya se encuentra Digitado';
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se pudo validar la informacion ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function reporteAction(Request $request, $format = 'P')
    {
        $this->setResponse('ajax');
        $_fields = [];
        $_fields['codofi'] = ['header' => 'Codigo', 'size' => '15', 'align' => 'C'];
        $_fields['detalle'] = ['header' => 'Detalle', 'size' => '31', 'align' => 'C'];
        $_fields['principal'] = ['header' => 'Principal', 'size' => '31', 'align' => 'C'];
        $_fields['estado'] = ['header' => 'Estado', 'size' => '31', 'align' => 'C'];
        $consultasOldServices = new GeneralService;
        $file = $consultasOldServices->createReport('mercurio04', $_fields, $this->query, 'Oficinas', $format);

        return $this->renderObject($file, false);
    }

    public function validePkCiudadAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codofi = $request->input('codofi');
            $codciu = $request->input('codciu');
            $response = 'El Registro ya se encuentra Digitado';
            $l = Mercurio05::whereRaw("codofi = '$codofi' and codciu='$codciu'")->count();
            if ($l > 0) {
                $response = 'El Registro ya se encuentra Digitado';
            }

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se pudo validar la informacion ' . $e->getMessage();
            return $this->renderObject($response, false);
        }
    }

    public function ciudadViewAction(Request $request)
    {
        try {
            $codofi = $request->input('codofi');
            $mercurio05 = Mercurio05::whereRaw("codofi='{$codofi}'")->get();
            $response = [
                'success' => true,
                'data' => $mercurio05->toArray()
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($response, false);
    }

    public function guardarCiudadAction(Request $request)
    {
        try {

            $this->setResponse('ajax');
            $codofi = $request->input('codofi');
            $codciu = $request->input('codciu');

            $response = $this->db->begin();
            $mercurio05 = new Mercurio05;

            $mercurio05->setCodofi($codofi);
            $mercurio05->setCodciu($codciu);
            if (! $mercurio05->save()) {
                $this->db->rollback();
            }
            $this->db->commit();
            $response = 'Movimiento Realizado Con Exito';

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = 'No se pudo realizar el movimiento ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function editarCiudadAction(Request $request)
    {
        try {
            try {
                $this->setResponse('ajax');
                $codofi = $request->input('codofi');
                $codciu = $request->input('codciu');

                $response = $this->db->begin();
                $mercurio05 = Mercurio05::whereRaw("codofi='$codofi' and codciu = '$codciu'")->first();
                if ($mercurio05 == false) {
                    $mercurio05 = new Mercurio05;
                }

                return $this->renderObject($mercurio05->toArray(), false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = 'No se pudo realizar el movimiento ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function borrarCiudadAction(Request $request)
    {
        try {
            try {
                $this->setResponse('ajax');
                $codofi = $request->input('codofi');
                $codciu = $request->input('codciu');

                $response = $this->db->begin();
                Mercurio05::whereRaw("codofi='$codofi' and codciu='$codciu'")->delete();
                $this->db->commit();
                $response = 'Movimiento Realizado Con Exito';

                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = 'No se pudo realizar el movimiento ' . $e->getMessage();

            return $this->renderObject($response, false);
        }
    }

    public function validePkOpcionAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $codofi = $request->input('codofi');
            $tipopc = $request->input('tipopc');
            $usuario = $request->input('usuario');

            $l = Mercurio08::whereRaw("codofi='{$codofi}' AND tipopc='{$tipopc}' AND usuario='{$usuario}'")->count();
            if ($l > 0) {
                throw new DebugException('El Registro ya se encuentra digitado', 501);
            }
            $response = [
                'flag' => true,
                'msg' => 'Ok',
                'success' => true,
            ];
        } catch (DebugException $e) {
            $response = [
                'flag' => false,
                'msg' => $e->getMessage(),
                'success' => false,
            ];
        }

        return $this->renderObject($response, false);
    }

    public function opcionViewAction(Request $request)
    {
        try {
            $codofi = $request->input('codofi');
            $mercurio08 = Mercurio08::whereRaw("codofi='{$codofi}'")->get();
            $data = $mercurio08->map(function ($row) {
                $arr = $row->toArray();
                $mercurio09 = Mercurio09::where('tipopc', $row->tipopc)->first();
                $gener02 = Gener02::where('usuario', $row->usuario)->first();

                $arr['tipopc_detalle'] = ($mercurio09) ? $mercurio09->detalle : '';
                $arr['usuario_nombre'] = ($gener02) ? $gener02->usuario : '';
                return $arr;
            });

            $response = [
                'success' => true,
                'data' => $data->toArray()
            ];
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
            return $this->renderObject($response, false);
        }
    }

    public function guardarOpcionAction(Request $request)
    {
        try {
            try {
                $this->setResponse('ajax');
                $codofi = $request->input('codofi');
                $tipopc = $request->input('tipopc');
                $usuario = $request->input('usuario');

                $response = $this->db->begin();
                $mercurio08 = Mercurio08::whereRaw("codofi='{$codofi}' and tipopc='{$tipopc}' and usuario='{$usuario}'")->first();
                if ($mercurio08 == false) {
                    $mercurio08 = new Mercurio08;
                    $orden = Mercurio08::max('orden')->whereRaw(" codofi='$codofi' and tipopc='$tipopc'") + 1;
                    $mercurio08->setOrden($orden);
                }

                $mercurio08->setCodofi($codofi);
                $mercurio08->setTipopc($tipopc);
                $mercurio08->setUsuario($usuario);
                if (! $mercurio08->save()) {
                    $this->db->rollback();
                }
                $this->db->commit();
                $response = 'Movimiento Realizado Con Exito';

                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = 'No se pudo realizar el movimiento ' . $e->getMessage();
            return $this->renderObject($response, false);
        }
    }

    public function borrarOpcionAction(Request $request)
    {
        try {
            try {
                $this->setResponse('ajax');
                $codofi = $request->input('codofi');
                $tipopc = $request->input('tipopc');
                $usuario = $request->input('usuario');

                $response = $this->db->begin();
                Mercurio08::whereRaw("codofi='$codofi' and tipopc='$tipopc' and usuario='$usuario'")->delete();
                $this->db->commit();
                $response = 'Movimiento Realizado Con Exito';

                return $this->renderObject($response, false);
            } catch (DebugException $e) {
                $this->db->rollback();
            }
        } catch (DebugException $e) {
            $response = 'No se pudo realizar el movimiento ' . $e->getMessage();
            return $this->renderObject($response, false);
        }
    }
}
