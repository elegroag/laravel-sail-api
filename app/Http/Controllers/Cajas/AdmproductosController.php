<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\PinesAfiliado;
use App\Models\ServiciosCupos;
use App\Services\Utils\Comman;
use Illuminate\Http\Request;

class AdmproductosController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->setParamToView('instancePath', env('APP_URL').'Cajas/');
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function listaAction()
    {
        $this->setParamToView('hide_header', true);
        $this->setParamToView('title', 'Productos y Servicios');
    }

    public function buscarListaAction()
    {
        $this->setResponse('ajax');
        $serviciosCupos = new ServiciosCupos;
        $todosServicios = [];
        $collect = $serviciosCupos->find();
        $ai = 0;
        foreach ($collect as $servicioCupo) {
            $todosServicios[$ai] = $servicioCupo->getArray();
            $model = $this->db->fetchOne("SELECT count(DISTINCT cedtra) as numtra, count(DISTINCT docben) as numben
            FROM pines_afiliado
            WHERE codser='{$servicioCupo->getCodser()}'");

            $todosServicios[$ai]['cantidad_trabajadores'] = $model['numtra'];
            $todosServicios[$ai]['cantidad_beneficiarios'] = $model['numben'];
            $ai++;
        }

        return $this->renderObject(
            [
                'success' => true,
                'data' => $todosServicios,
            ]
        );
    }

    public function nuevoAction()
    {
        $this->setParamToView('hide_header', true);
        $this->setParamToView('title', 'Productos y Servicios');
    }

    public function guardarAction(Request $request, $id = '')
    {
        try {
            $this->setResponse('ajax');
            $codser = $request->input('codser');
            $cupos = $request->input('cupos');
            $servicio = $request->input('servicio');
            $estado = $request->input('estado');

            if ($id == '') {
                $serviciosCupos = new ServiciosCupos;
                $serviciosCupos->setId(null);
                $serviciosCupos->setCodser($codser);
                $serviciosCupos->setCupos($cupos);
                $serviciosCupos->setServicio($servicio);
                $serviciosCupos->setEstado($estado);
            } else {
                $model = new ServiciosCupos;
                $serviciosCupos = $model->findFirst(" id='{$id}'");
                if ($serviciosCupos == false) {
                    throw new DebugException('Error el servicio no es valido para continuar.', 501);
                }
                $serviciosCupos->setCodser($codser);
                $serviciosCupos->setCupos($cupos);
                $serviciosCupos->setServicio($servicio);
                $serviciosCupos->setEstado($estado);
            }

            if (! $serviciosCupos->save()) {
                $msj = '';
                foreach ($serviciosCupos->getMessages() as $message) {
                    $msj .= $message->getMessage()."\n";
                }
                throw new DebugException('Error al guardar el servicio.'.$msj, 501);
            }

            $salida = [
                'success' => true,
                'msj' => 'El proceso de guardado se completo con éxito.',
                'data' => $serviciosCupos->getArray(),
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function editarAction($id = '')
    {
        if ($id == '') {
            set_flashdata('error', [
                'msj' => 'El servicio no está disponible para editar.',
                'code' => '505',
            ]);

            return redirect('admproductos/lista');
            exit;
        }

        $model = new ServiciosCupos;
        $servicioCupo = $model->findFirst("id='{$id}'");
        if ($servicioCupo == false) {
            set_flashdata('error', [
                'msj' => 'El servicio no está disponible para editar.',
                'code' => '505',
            ]);

            return redirect('admproductos/lista');
            exit;
        }
        $this->setParamToView('servicio', $servicioCupo);
        $this->setParamToView('hide_header', true);
        $this->setParamToView('title', 'Productos y Servicios');
    }

    public function changeEstadoAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id');
            $estado = $request->input('estado');

            $model = new ServiciosCupos;
            $serviciosCupo = $model->findFirst(" id='{$id}'");
            $serviciosCupo->setEstado($estado);

            if (! $serviciosCupo->save()) {
                $msj = '';
                foreach ($serviciosCupo->getMessages() as $message) {
                    $msj .= $message->getMessage()."\n";
                }
                throw new DebugException('Error al guardar el servicio.'.$msj, 501);
            }

            $salida = [
                'success' => true,
                'msj' => 'El registro se actualizo con éxito.',
                'data' => $serviciosCupo->getArray(),
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function aplicadosAction($codser = '')
    {
        if ($codser == '') {
            set_flashdata('error', [
                'msj' => 'El servicio no está disponible.',
                'code' => '505',
            ]);

            return redirect('admproductos/lista');
            exit;
        }
        $servicioCupo = $this->ServiciosCupos->findFirst(" codser='{$codser}'");
        $pinesAfiliado = new PinesAfiliado;
        $collect = $pinesAfiliado->find(" codser='{$servicioCupo->getCodser()}'");

        $this->setParamToView('hide_header', true);
        $this->setParamToView('servicio', $servicioCupo);
        $this->setParamToView('codser', $codser);
        $this->setParamToView('aplicados', $collect);
        $this->setParamToView('title', 'Productos y Servicios');
    }

    public function buscarAfiliadosAplicadosAction(Request $request, $codser = '')
    {
        $this->setResponse('ajax');

        try {
            if ($codser == '') {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            $pinesAfiliado = new PinesAfiliado;
            $servicioCupo = $this->ServiciosCupos->findFirst(" codser='{$codser}'");

            $collect = $pinesAfiliado->find(" codser='{$servicioCupo->getCodser()}'");
            $ai = 0;
            $todosAplicados = [];
            foreach ($collect as $pinAfiliado) {
                $todosAplicados[$ai] = $pinAfiliado->getArray();
                $ai++;
            }

            $salida = [
                'success' => true,
                'data' => $todosAplicados,
            ];
        } catch (DebugException $err) {
            $salida = [
                'msj' => $err->getMessage(),
                'success' => false,
                'data' => false,
            ];
        }

        return $this->renderObject($salida);
    }

    public function cargue_pagosAction($codser = '')
    {
        if ($codser == '') {
            set_flashdata('error', [
                'msj' => 'El servicio no está disponible.',
                'code' => '505',
            ]);

            return redirect('admproductos/lista');
            exit;
        }
        $servicioCupo = $this->ServiciosCupos->findFirst(" codser='{$codser}'");
        $pinesAfiliado = new PinesAfiliado;
        $collect = $pinesAfiliado->find(" codser='{$servicioCupo->getCodser()}'");

        $this->setParamToView('hide_header', true);
        $this->setParamToView('servicio', $servicioCupo);
        $this->setParamToView('codser', $codser);
        $this->setParamToView('aplicados', $collect);
        $this->setParamToView('title', 'Productos y Servicios');
    }

    public function detalleAplicadoAction(Request $request, $id)
    {
        $this->setResponse('ajax');
        try {
            if ($id == '') {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            $model = new PinesAfiliado;
            $pineAfiliado = $model->findfirst(" id='{$id}'");
            $pinAfiliado = $pineAfiliado->getArray();
            $pinAfiliado['beneficiario'] = false;
            $pinAfiliado['trabajador'] = false;
            $pinAfiliado['estado_detalle'] = $model->getEstadoDetalle();

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                    'params' => true,
                ]
            );

            if ($procesadorComando->isJson()) {
                $datos_captura = $procesadorComando->toArray();
                $paramsTrabajador = new ParamsTrabajador;
                $paramsTrabajador->setDatosCaptura($datos_captura);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador',
                    'params' => [
                        'cedtra' => $pineAfiliado->getCedtra(),
                    ],
                ]
            );

            if ($procesadorComando->isJson()) {
                $out = $procesadorComando->toArray();
                if ($out['success']) {
                    $pinAfiliado['trabajador'] = $out['data'];
                    $zonas = ParamsTrabajador::getZonas();
                    $pinAfiliado['trabajador']['zona_detalle'] = $zonas[$pinAfiliado['trabajador']['codzon']];
                }
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $pineAfiliado->getDocben(),
                ]
            );

            if ($procesadorComando->isJson()) {
                $out = $procesadorComando->toArray();
                if ($out['success']) {
                    $pinAfiliado['beneficiario'] = $out['data'];
                }
            }

            $salida = [
                'success' => true,
                'data' => $pinAfiliado,
            ];
        } catch (DebugException $err) {
            $salida = [
                'msj' => $err->getMessage(),
                'success' => false,
                'data' => false,
            ];
        }

        return $this->renderObject($salida);
    }

    public function rechazarAction(Request $request, $id = '')
    {
        $this->setResponse('ajax');
        try {
            if ($id == '') {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            $model = new PinesAfiliado;
            $pineAfiliado = $model->findfirst(" id='{$id}'");
            $pineAfiliado->setEstado('R');
            $pineAfiliado->save();

            $servicioCupo = new ServiciosCupos;
            $servicioCupo->findFirst("codser='{$pineAfiliado->getCodser()}'");
            $servicioCupo->setCupos($servicioCupo->getCupos() + 1);
            $servicioCupo->save();

            $salida = [
                'success' => true,
                'msj' => 'El registro se rechazo con éxito',
                'data' => $pineAfiliado->getArray(),
            ];
        } catch (DebugException $err) {
            $salida = [
                'msj' => $err->getMessage(),
                'success' => false,
                'data' => false,
            ];
        }

        return $this->renderObject($salida);
    }
}
