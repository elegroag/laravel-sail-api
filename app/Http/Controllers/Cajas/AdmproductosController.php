<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\PinesAfiliado;
use App\Models\ServiciosCupos;
use App\Services\Api\ApiSubsidio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdmproductosController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    public function lista()
    {
        return view('cajas.admproductos.lista', [
            'title' => 'Productos y Servicios',
            'campo_filtro' => [
                'codser' => 'Código',
                'servicio' => 'Servicio',
                'estado' => 'Estado',
            ],
        ]);
    }

    public function buscarLista()
    {
        $this->setResponse('ajax');

        try {
            Log::info('AdmproductosController@buscarLista - iniciando consulta');

            $serviciosCupos = new ServiciosCupos;
            $todosServicios = [];
            $collect = $serviciosCupos->getFind();
            $ai = 0;

            Log::info('AdmproductosController@buscarLista - servicios encontrados', ['count' => $collect->count()]);

            foreach ($collect as $servicioCupo) {
                $todosServicios[$ai] = $servicioCupo->getArray();
                $model = $this->db->fetchOne("SELECT count(DISTINCT cedtra) as numtra, count(DISTINCT docben) as numben
                FROM pines_afiliado
                WHERE codser='{$servicioCupo->getCodser()}'");

                $todosServicios[$ai]['cantidad_trabajadores'] = $model['numtra'] ?? 0;
                $todosServicios[$ai]['cantidad_beneficiarios'] = $model['numben'] ?? 0;
                $ai++;
            }

            return $this->renderObject([
                'success' => true,
                'data' => array_values($todosServicios),
            ]);
        } catch (Throwable $e) {
            Log::error('AdmproductosController@buscarLista - error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->renderObject([
                'success' => false,
                'msj' => 'Error al consultar productos y servicios.',
                'data' => [],
            ]);
        }
    }

    public function nuevo()
    {
        return view('cajas.admproductos.nuevo', [
            'title' => 'Productos y Servicios',
        ]);
    }

    public function guardar(Request $request, $id = '')
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

    public function editar($id = '')
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

    public function changeEstado(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id');
            $estado = $request->input('estado');

            Log::info('AdmproductosController@changeEstado - actualizando estado', [
                'id' => $id,
                'estado' => $estado,
            ]);

            $model = new ServiciosCupos;
            $serviciosCupo = $model->findFirst(" id='{$id}'");
            if ($serviciosCupo === null) {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

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
            Log::warning('AdmproductosController@changeEstado - validación', ['message' => $err->getMessage()]);
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        } catch (Throwable $e) {
            Log::error('AdmproductosController@changeEstado - error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $salida = [
                'success' => false,
                'msj' => 'Error al actualizar el estado del servicio.',
            ];
        }

        return $this->renderObject($salida);
    }

    public function aplicados($codser = '')
    {
        $codser = $this->normalizeCodser($codser);

        if ($codser === '') {
            set_flashdata('error', [
                'msj' => 'El servicio no está disponible.',
                'code' => '505',
            ]);

            return redirect('admproductos/lista');
            exit;
        }

        $servicioCupo = $this->findServicioCupoByCodser($codser);
        if ($servicioCupo === null) {
            Log::warning('AdmproductosController@aplicados - servicio no configurado en cupos', ['codser' => $codser]);
        }

        $pinesAfiliado = PinesAfiliado::where('codser', $codser)->get();

        return view('cajas.admproductos.aplicados', [
            'title' => 'Productos y Servicios',
            'servicio' => $servicioCupo,
            'codser' => $codser,
            'aplicados' => $pinesAfiliado,
        ]);
    }

    public function buscarAfiliadosAplicados(Request $request, $codser = '')
    {
        $this->setResponse('ajax');

        try {
            $codser = $this->normalizeCodser($codser);

            if ($codser === '') {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            Log::info('AdmproductosController@buscarAfiliadosAplicados - iniciando consulta', ['codser' => $codser]);

            $servicioCupo = $this->findServicioCupoByCodser($codser);
            if ($servicioCupo === null) {
                Log::warning('AdmproductosController@buscarAfiliadosAplicados - servicio no configurado en cupos, consultando pines por codser', [
                    'codser' => $codser,
                ]);
            }

            $todosAplicados = PinesAfiliado::where('codser', $codser)
                ->get()
                ->map(fn (PinesAfiliado $pinAfiliado) => $pinAfiliado->getArray())
                ->values()
                ->all();

            Log::info('AdmproductosController@buscarAfiliadosAplicados - aplicados encontrados', [
                'codser' => $codser,
                'count' => count($todosAplicados),
            ]);

            $salida = [
                'success' => true,
                'data' => array_values($todosAplicados),
            ];
        } catch (DebugException $err) {
            Log::warning('AdmproductosController@buscarAfiliadosAplicados - validación', [
                'codser' => $codser,
                'message' => $err->getMessage(),
            ]);
            $salida = [
                'msj' => $err->getMessage(),
                'success' => false,
                'data' => [],
            ];
        } catch (Throwable $e) {
            Log::error('AdmproductosController@buscarAfiliadosAplicados - error', [
                'codser' => $codser,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $salida = [
                'msj' => 'Error al consultar afiliados aplicados.',
                'success' => false,
                'data' => [],
            ];
        }

        return $this->renderObject($salida);
    }

    public function carguePagos($codser = '')
    {
        if ($codser == '') {
            set_flashdata('error', [
                'msj' => 'El servicio no está disponible.',
                'code' => '505',
            ]);

            return redirect('admproductos/lista');
            exit;
        }

        Log::info('AdmproductosController@carguePagos - cargando vista', ['codser' => $codser]);

        $codser = $this->normalizeCodser($codser);
        $servicioCupo = $this->findServicioCupoByCodser($codser);
        if ($servicioCupo === null) {
            Log::warning('AdmproductosController@carguePagos - servicio no configurado en cupos', ['codser' => $codser]);
        }

        $collect = PinesAfiliado::where('codser', $codser)->get();

        return view('cajas.admproductos.cargue_pagos', [
            'title' => 'Productos y Servicios',
            'servicio' => $servicioCupo,
            'codser' => $codser,
            'aplicados' => $collect,
            'hide_header' => true,
        ]);
    }

    public function detalleAplicado(Request $request, $id)
    {
        $this->setResponse('ajax');

        try {
            if ($id == '') {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            Log::info('AdmproductosController@detalleAplicado - iniciando consulta', ['id' => $id]);

            $pineAfiliado = (new PinesAfiliado)->findFirst(" id='{$id}'");
            if ($pineAfiliado === null) {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            $pinAfiliado = $pineAfiliado->getArray();
            $pinAfiliado['beneficiario'] = false;
            $pinAfiliado['trabajador'] = false;
            $pinAfiliado['estado_detalle'] = $pineAfiliado->getEstadoDetalle();

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
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

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
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

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
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

            Log::info('AdmproductosController@detalleAplicado - detalle generado', ['id' => $id]);
        } catch (DebugException $err) {
            Log::warning('AdmproductosController@detalleAplicado - validación', [
                'id' => $id,
                'message' => $err->getMessage(),
            ]);
            $salida = [
                'msj' => $err->getMessage(),
                'success' => false,
                'data' => false,
            ];
        } catch (Throwable $e) {
            Log::error('AdmproductosController@detalleAplicado - error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $salida = [
                'msj' => 'Error al consultar el detalle del aplicado.',
                'success' => false,
                'data' => false,
            ];
        }

        return $this->renderObject($salida);
    }

    public function rechazar(Request $request, $id = '')
    {
        $this->setResponse('ajax');

        try {
            if ($id == '') {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            Log::info('AdmproductosController@rechazar - iniciando rechazo', ['id' => $id]);

            $pineAfiliado = (new PinesAfiliado)->findFirst(" id='{$id}'");
            if ($pineAfiliado === null) {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            $pineAfiliado->setEstado('R');
            $pineAfiliado->save();

            $servicioCupo = (new ServiciosCupos)->findFirst("codser='{$pineAfiliado->getCodser()}'");
            if ($servicioCupo === null) {
                throw new DebugException('Error el servicio no es valido para continuar.', 501);
            }

            $servicioCupo->setCupos($servicioCupo->getCupos() + 1);
            $servicioCupo->save();

            Log::info('AdmproductosController@rechazar - aplicado rechazado', [
                'id' => $id,
                'codser' => $pineAfiliado->getCodser(),
            ]);

            $salida = [
                'success' => true,
                'msj' => 'El registro se rechazo con éxito',
                'data' => $pineAfiliado->getArray(),
            ];
        } catch (DebugException $err) {
            Log::warning('AdmproductosController@rechazar - validación', [
                'id' => $id,
                'message' => $err->getMessage(),
            ]);
            $salida = [
                'msj' => $err->getMessage(),
                'success' => false,
                'data' => false,
            ];
        } catch (Throwable $e) {
            Log::error('AdmproductosController@rechazar - error', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $salida = [
                'msj' => 'Error al rechazar el aplicado.',
                'success' => false,
                'data' => false,
            ];
        }

        return $this->renderObject($salida);
    }

    private function normalizeCodser(string $codser): string
    {
        return trim($codser);
    }

    private function findServicioCupoByCodser(string $codser): ?ServiciosCupos
    {
        $codser = $this->normalizeCodser($codser);
        if ($codser === '') {
            return null;
        }

        $servicio = (new ServiciosCupos)->findFirst(" codser='{$codser}'");
        if ($servicio !== null) {
            return $servicio;
        }

        return ServiciosCupos::where('codser', $codser)->first();
    }
}
