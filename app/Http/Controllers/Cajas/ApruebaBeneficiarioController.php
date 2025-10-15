<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsBeneficiario;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio31;
use App\Models\Mercurio34;
use App\Services\Aprueba\ApruebaBeneficiario;
use App\Services\CajaServices\BeneficiarioServices;
use App\Services\Srequest;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApruebaBeneficiarioController extends ApplicationController
{
    protected $tipopc = 4;

    protected $db;

    protected $user;

    protected $tipo;

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    /**
     * trabajadorServices variable
     *
     * @var BeneficiarioServices
     */
    protected $beneficiarioServices;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * aplicarFiltroAction function
     *
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                'cantidadPaginas' => $cantidad_pagina,
                'query' => $query_str,
                'estado' => $estado,
            ])
        );
        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_beneficiario', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new BeneficiarioServices);

        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction(Request $request, $estado = 'P')
    {
        $this->buscarAction($request, $estado);
    }

    /**
     * indexAction function
     *
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function indexAction()
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'numdoc' => 'Identificación',
            'priape' => 'Primer apellido',
            'segape' => 'Segundo apellido',
            'prinom' => 'Primer nombre',
            'segnom' => 'Segundo nombre',
            'cedtra' => 'Cedula trabajador',
            'fecsol' => 'Fecha solicitud',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobacionben.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Beneficiario',
            'buttons' => ['F'],
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    /**
     * buscarAction function
     *
     * @changed [2023-12-20]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $estado
     * @return void
     */
    public function buscarAction(Request $request, $estado = 'P')
    {
        $this->setResponse('ajax');
        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input('numero')) ? $request->input('numero') : 10;
        $usuario = parent::getActUser();
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                'cantidadPaginas' => $cantidad_pagina,
                'query' => $query_str,
                'estado' => $estado,
                'pagina' => $pagina,
            ])
        );
        if (get_flashdata_item('filter_beneficiario') != false) {
            $query = $pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }
        set_flashdata('filter_beneficiario', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new BeneficiarioServices);

        return $this->renderObject($response, false);
    }

    /**
     * apruebaAction function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function apruebaAction(Request $request)
    {
        $this->setResponse('ajax');
        $user = session()->get('user');
        $debuginfo = [];
        try {
            try {
                $acceso = (new Gener42)->count('*', "conditions: permiso='92' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject([
                        'success' => false,
                        'msj' => 'El usuario no dispone de permisos de aprobación',
                    ]);
                }

                $aprueba = new ApruebaBeneficiario;
                $this->db->begin();
                $postData = $_POST;
                $idSolicitud = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
                $aprueba->findSolicitud($idSolicitud);
                $aprueba->findSolicitante();
                $aprueba->procesar($postData);
                $this->db->commit();
                $aprueba->enviarMail($request->input('actapr'), $request->input('feccap'));
                $salida = [
                    'success' => true,
                    'msj' => 'El registro se completo con éxito',
                ];
            } catch (DebugException $err) {

                $this->db->rollback();
                $salida = [
                    'success' => false,
                    'msj' => $err->getMessage(),
                ];
            }
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        if ($debuginfo) {
            $salida['info'] = $debuginfo;
        }

        return $this->renderObject($salida, false);
    }

    public function devolverAction(Request $request)
    {
        $this->beneficiarioServices = new BeneficiarioServices;
        $notifyEmailServices = new NotifyEmailServices;

        $this->setResponse('ajax');
        $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
        $nota = sanetizar($request->input('nota'));
        $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
        $array_corregir = $request->input('campos_corregir');

        try {
            $campos_corregir = implode(';', $array_corregir);
            $mercurio34 = Mercurio34::where("id", $id)->first();

            $this->beneficiarioServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
            $notifyEmailServices->emailDevolver(
                $mercurio34,
                $this->beneficiarioServices->msjDevolver($mercurio34, $nota)
            );

            $response = [
                'success' => true,
                'msj' => 'Movimiento realizado con exito',
            ];
        } catch (DebugException $err) {
            $response = $err->getMessage();
        }
        $this->renderObject($response);
    }

    public function devolver($mercurio34, $nota, $codest, $campos_corregir)
    {
        $today = Carbon::now();
        $id = $mercurio34->getId();
        $mercurio34 = new Mercurio34;
        $mercurio34->updateAll(" estado='D', motivo='{$nota}', codest='{$codest}', fecest='" . $today->format('Y-m-d H:i:s') . "'", "conditions: id='{$id}'");

        $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item') + 1;
        $mercurio10 = new Mercurio10;
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem(intval($item) + 1);
        $mercurio10->setEstado('D');
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));

        if (! $mercurio10->save()) {
            $msj = '';
            foreach ($mercurio10->getMessages() as $key => $message) {
                $msj .= $message . '<br/>';
            }
            throw new DebugException('Error ' . $msj, 501);
        }

        Mercurio10::whereRaw("item='{$item}' AND numero='{$id}' AND tipopc='{$this->tipopc}'")->update("campos_corregir='{$campos_corregir}'");
    }

    public function rechazarAction(Request $request)
    {
        $notifyEmailServices = new NotifyEmailServices;
        $this->beneficiarioServices = new BeneficiarioServices;
        $this->setResponse('ajax');
        $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
        $nota = sanetizar($request->input('nota'));
        $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
        try {
            $mercurio34 = Mercurio34::where("id", $id)->first();
            $this->beneficiarioServices->rechazar($mercurio34, $nota, $codest);
            $notifyEmailServices->emailRechazar(
                $mercurio34,
                $this->beneficiarioServices->msjRechazar($mercurio34, $nota)
            );

            $response = [
                'success' => true,
                'msj' => 'Movimiento Realizado Con Exito',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
                'code' => 500,
            ];
        }
        $this->renderObject($response);
    }

    public function rechazar($mercurio34, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio34->getId();
        Mercurio34::where("id", $id)->update([
            "estado" => "X",
            "motivo" => $nota,
            "codest" => $codest,
            "fecest" => $today->format('Y-m-d H:i:s'),
        ]);

        $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item');
        $mercurio10 = new Mercurio10;
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem(intval($item) + 1);
        $mercurio10->setEstado('X');
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));
        $mercurio10->save();

        return true;
    }

    /**
     * inforAction function
     * @changed [2023-12-20]
     * @author elegroag <elegroag@ibero.edu.co
     * @return void
     */
    public function inforAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $request->input('id');

            if (! $id) {
                throw new DebugException('Error la solicitud es requerida para continuar', 501);
            }

            $beneficiarioServices = new BeneficiarioServices;
            $solicitud = Mercurio34::where("id", $id)->first();
            if ($solicitud == false) {
                throw new DebugException('La solicitud de afiliación de beneficiario no es valida.', 501);
            }

            $trabajador_sisu = false;
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador',
                    'params' => ['cedtra' => $solicitud->getCedtra(), 'estado' => 'A'],
                ]
            );

            $rqs = $procesadorComando->toArray();
            if (! empty($rqs)) {
                $trabajador_sisu = ($rqs['success']) ? $rqs['data'] : false;
            }

            $trabajador = new \stdClass;
            if (! $trabajador_sisu) {
                $tr = Mercurio31::whereRaw("cedtra='{$solicitud->getCedtra()}' and estado='A'")->first();
                $trabajador->estado = ($tr) ? $tr->getEstado() : 'I';
            } else {
                $trabajador->estado = $trabajador_sisu['estado'];
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $solicitud->getNumdoc(),
                ]
            );

            $rqs = $procesadorComando->toArray();
            $relacion_multiple = false;

            if ($rqs) {
                if ($rqs['success']) {
                    $sys_beneficiario = $rqs['data'];
                    $this->setParamToView('beneficiario_sisuweb', $sys_beneficiario);
                    $giro = $sys_beneficiario['giro'];
                    $vinculo_trabajador = false;

                    if ($rqs['relaciones']) {
                        $relacion_multiple = $rqs['relaciones'];
                        foreach ($rqs['relaciones'] as $ai => $relacion) {
                            if ($relacion['cedtra'] == $solicitud->getCedtra()) {
                                $vinculo_trabajador = true;
                                break;
                            }
                        }
                    }
                    $estado = ($vinculo_trabajador == false) ? 'I' : $sys_beneficiario['estado'];
                }
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_beneficiarios',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsBeneficiario = new ParamsBeneficiario;
            $paramsBeneficiario->setDatosCaptura($datos_captura);

            $html = view(
                'cajas/aprobacionben/tmp/consulta',
                [
                    'beneficiario' => $solicitud,
                    'detTipo' => Mercurio06::where("tipo", $solicitud->getTipo())->first()->getDetalle(),
                    '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
                    '_codciu' => ParamsBeneficiario::getCiudades(),
                    '_sexo' => ParamsBeneficiario::getSexos(),
                    '_estciv' => ParamsBeneficiario::getEstadoCivil(),
                    '_parent' => ParamsBeneficiario::getParentesco(),
                    '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_giro' => ParamsBeneficiario::getTieneGiro(),
                    '_pago' => ParamsBeneficiario::getPago(),
                    '_ciunac' => ParamsBeneficiario::getCiudades(),
                    '_huerfano' => ParamsBeneficiario::getHuerfano(),
                    '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_calendario' => ParamsBeneficiario::getCalendario(),
                    '_codgir' => ParamsBeneficiario::getCodigoGiro(),
                ]
            )->render();

            $response = [
                'success' => true,
                'data' => $solicitud->getArray(),
                'mercurio11' => Mercurio11::all(),
                'consulta' => $html,
                'adjuntos' => $beneficiarioServices->adjuntos($solicitud),
                'seguimiento' => $beneficiarioServices->seguimiento($solicitud),
                'campos_disponibles' => (new Mercurio34)->CamposDisponibles(),
                'relacion_multiple' => $relacion_multiple,
                'trabajador' => $trabajador,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_beneficiarios',
            ]
        );

        $paramsBeneficiario = new ParamsBeneficiario;
        $paramsBeneficiario->setDatosCaptura($procesadorComando->toArray());

        return [
            '_cedcon' => [],
            '_giro' => ParamsBeneficiario::getTieneGiro(),
            '_pago' => ParamsBeneficiario::getPago(),
            '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
            '_sexo' => ParamsBeneficiario::getSexos(),
            '_estciv' => ParamsBeneficiario::getEstadoCivil(),
            '_ciunac' => ParamsBeneficiario::getCiudades(),
            '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
            '_parent' => ParamsBeneficiario::getParentesco(),
            '_huerfano' => ParamsBeneficiario::getHuerfano(),
            '_tiphij' => ParamsBeneficiario::getTipoHijo(),
            '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
            '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
            '_calendario' => ParamsBeneficiario::getCalendario(),
            '_codgir' => ParamsBeneficiario::getCodigoGiro(),
            'tipo' => '',
        ];
    }

    public function editar_solicitudAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $numdoc = $request->input('numdoc', 'addslaches', 'alpha', 'extraspaces', 'striptags');

            $mercurio34 = (new Mercurio34)->findFirst("id='{$id}' and numdoc='{$numdoc}'");
            if (! $mercurio34) {
                throw new DebugException('El beneficiario no está disponible para notificar por email', 501);
            } else {
                $mercurio07 = (new Mercurio07)->findFirst("documento='{$mercurio34->getDocumento()}' and coddoc='{$mercurio34->getCoddoc()}'");
                if (! $mercurio07) {
                    throw new DebugException('El usuario no está disponible para notificar por email', 501);
                }
                $asignarFuncionario = new AsignarFuncionario;
                $usuario = $asignarFuncionario->asignar($this->tipopc, $mercurio07->getCodciu());

                if (empty($usuario)) {
                    throw new DebugException('No se puede realizar el registro, no hay usuario disponible para la atención de la solicitud, Comuniquese con la Atencion al cliente', 505);
                }
                $data = [
                    'tipdoc' => $this->clp($request, 'tipdoc'),
                    'numdoc' => $this->clp($request, 'numdoc'),
                    'priape' => $this->clp($request, 'priape'),
                    'segape' => $this->clp($request, 'segape'),
                    'prinom' => $this->clp($request, 'prinom'),
                    'segnom' => $this->clp($request, 'segnom'),
                    'fecnac' => $this->clp($request, 'fecnac'),
                    'ciunac' => $this->clp($request, 'ciunac'),
                    'sexo' => $this->clp($request, 'sexo'),
                    'parent' => $this->clp($request, 'parent'),
                    'huerfano' => $this->clp($request, 'huerfano'),
                    'tiphij' => $this->clp($request, 'tiphij'),
                    'nivedu' => $this->clp($request, 'nivedu'),
                    'captra' => $this->clp($request, 'captra'),
                    'tipdis' => $this->clp($request, 'tipdis'),
                    'calendario' => $this->clp($request, 'calendario'),
                    'cedacu' => $this->clp($request, 'cedacu'),
                ];
                $setters = '';
                foreach ($data as $ai => $row) {
                    if (strlen($row) > 0) {
                        $setters .= " $ai='{$row}',";
                    }
                }
                $setters = trim($setters, ',');
                Mercurio34::whereRaw("id='{$id}' AND numdoc='{$numdoc}'")->update($setters);

                $db = DbBase::rawConnect();

                $data = $db->fetchOne("SELECT max(id), mercurio34.* FROM mercurio34 WHERE numdoc='{$numdoc}'");
                $salida = [
                    'msj' => 'Proceso se ha completado con éxito',
                    'success' => true,
                    'data' => $data,
                ];
            }
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * empresa_sisuwebAction function
     * Datos de la empresa en sisuweb, si ya está registrada. pruebas 98588506
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarEnSisuViewAction($id = 0)
    {

        if (! $id) {
            return redirect('aprobacionben/index');
            exit;
        }

        $mercurio34 = Mercurio34::where("id", $id)->first();

        if (! $mercurio34) {
            set_flashdata('error', [
                'msj' => 'El beneficiario no se encuentra registrado.',
                'code' => 201,
            ]);

            return redirect('aprobacionben/index');
            exit;
        }
        $numdoc = $mercurio34->getNumdoc();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_beneficiario',
                'params' => $numdoc,
            ]
        );
        $rqs = $procesadorComando->toArray();

        if (! $rqs['success']) {
            set_flashdata('error', [
                'msj' => 'El beneficiario no se encuentra registrado.',
                'code' => 201,
            ]);

            return redirect('aprobacionben/index');
            exit();
        }

        $relaciones = [];
        if ($rqs['data']) {
            $beneficiario = $rqs['data'];
            $relaciones = $rqs['relaciones'];
        }

        $this->setParamToView('mercurio34', $mercurio34);
        $this->setParamToView('cedtra', $mercurio34->getCedtra());
        $this->setParamToView('relaciones', $relaciones);
        $this->setParamToView('beneficiario', $beneficiario);
        $this->setParamToView('title', "Beneficiario SISU - {$numdoc}");
    }

    public function opcionalAction($estado = 'P')
    {
        $this->setParamToView('hide_header', true);
        $this->setParamToView('title', 'Aprobación Beneficiario');

        $collection = Mercurio34::where("estado", $estado)->where("usuario", parent::getActUser())->orderBy("fecsol", 'ASC')->get();
        $beneficiarioServices = new BeneficiarioServices;
        $data = $beneficiarioServices->dataOptional($collection, $estado);

        $this->setParamToView('beneficiarios', $data);
        $this->setParamToView('buttons', ['F']);
        $this->setParamToView('pagina_con_estado', $estado);
    }

    public function reaprobarAction(Request $request)
    {
        $id = $request->input('id');
        $giro = $request->input('giro');
        $codgir = $request->input('codgir');
        $nota = $request->input('nota');
        $today = Carbon::now();
        $comando = '';
        try {
            try {
                $this->db->begin();

                Mercurio34::where("id", $id)->update("estado='A',fecest='" . $today->format('Y-m-d H:i:s') . "'");
                $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item') + 1;

                $mercurio10 = new Mercurio10;

                $mercurio10->setTipopc($this->tipopc);
                $mercurio10->setNumero($id);
                $mercurio10->setItem($item);
                $mercurio10->setEstado('A');
                $mercurio10->setNota($nota);
                $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));
                $mercurio10->save();

                $beneficiario = Mercurio34::where("id", $id)->first();

                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    [
                        'servicio' => 'ComfacaAfilia',
                        'metodo' => 'actualiza_beneficiario',
                        'params' => [
                            'numdoc' => $beneficiario->getNumdoc(),
                            'modelo' => [
                                'prinom' => $beneficiario->getPrinom(),
                                'segnom' => $beneficiario->getSegnom(),
                                'priape' => $beneficiario->getPriape(),
                                'giro' => $giro,
                                'codgir' => $codgir,
                            ],
                        ],
                    ]
                );

                $comando = $procesadorComando->getLineaComando();

                $result = $procesadorComando->toArray();
                if (! $result) {
                    throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
                } else {

                    $this->db->commit();
                    $response = [
                        'success' => true,
                        'msj' => 'Movimiento realizado con éxito',
                        'result' => $result,
                    ];
                }
            } catch (DebugException $err) {
                throw new DebugException($err->getMessage(), 505);
            }
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => 'No se pudo realizar el movimiento ' . "\n" . $e->getMessage() . "\n " . $e->getLine(),
                'comando' => $comando,
            ];
        }
        $this->renderObject($response);
    }

    public function borrarFiltroAction(Request $request)
    {
        $this->setResponse('ajax');
        set_flashdata('filter_beneficiario', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_trabajador'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }

    /**
     * infoAprobadoViewAction function
     * datos del solicitud aprobada enn sisu
     *
     * @param [type] $id
     * @return void
     */
    public function infoAprobadoViewAction($id)
    {
        $this->tipopc = '1';
        try {
            $mercurio34 = Mercurio34::where("id", $id)->where("estado", 'A')->first();
            if (! $mercurio34) {
                throw new DebugException('Error al buscar la beneficiario', 501);
            }

            $cedtra = $mercurio34->getCedtra();
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_beneficiarios',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsBeneficiario = new ParamsBeneficiario;
            $paramsBeneficiario->setDatosCaptura($datos_captura);

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $mercurio34->getNumdoc(),
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al buscar la beneficiario en Sisuweb', 501);
            }

            $out = $procesadorComando->toArray();
            $beneSisu = $out['data'];

            $beneficiario = new Mercurio34;
            $beneficiario->createAttributes($beneSisu);
            $beneficiario->setTipo('E');
            $beneficiario->setNumdoc($beneSisu['documento']);
            $html = view(
                'cajas/aprobacionben/tmp/consulta',
                [
                    'beneficiario' => $beneficiario,
                    'detTipo' => Mercurio06::where("tipo", $beneficiario->getTipo())->first()->getDetalle(),
                    '_coddoc' => ParamsBeneficiario::getTiposDocumentos(),
                    '_codciu' => ParamsBeneficiario::getCiudades(),
                    '_sexo' => ParamsBeneficiario::getSexos(),
                    '_estciv' => ParamsBeneficiario::getEstadoCivil(),
                    '_parent' => ParamsBeneficiario::getParentesco(),
                    '_captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    '_tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    '_nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    '_giro' => ParamsBeneficiario::getTieneGiro(),
                    '_pago' => ParamsBeneficiario::getPago(),
                    '_ciunac' => ParamsBeneficiario::getCiudades(),
                    '_huerfano' => ParamsBeneficiario::getHuerfano(),
                    '_tiphij' => ParamsBeneficiario::getTipoHijo(),
                    '_calendario' => ParamsBeneficiario::getCalendario(),
                    '_codgir',
                    ParamsBeneficiario::getCodigoGiro(),
                ]
            )->render();

            $code_estados = [];
            $query = Mercurio11::all();
            foreach ($query as $row) {
                $code_estados[$row->getCodest()] = $row->getDetalle();
            }

            $this->setParamToView('code_estados', $code_estados);
            $this->setParamToView('mercurio34', $beneficiario);
            $this->setParamToView('consulta_trabajador', $html);
            $this->setParamToView('hide_header', true);
            $this->setParamToView('idModel', $id);
            $this->setParamToView('cedtra', $cedtra);
            $this->setParamToView('title', "Beneficiario Aprobado {$beneficiario->getNumdoc()}");
        } catch (DebugException $err) {
            set_flashdata('error', [
                'msj' => $err->getMessage(),
                'code' => 201,
            ]);

            return redirect('aprobacionben/index');
            exit;
        }
    }

    /**
     * deshacerAprobado function
     * metodo para deshacer una afilación, dado que se presente algun error por parte de los analistas encargados
     *
     * @param [type] $id
     * @return void
     */
    public function deshacerAction(Request $request)
    {
        $this->beneficiarioServices = $this->services->get('BeneficiarioServices');
        $notifyEmailServices = new NotifyEmailServices;
        $this->setResponse('ajax');
        $action = $request->input('action');
        $codest = $request->input('codest');
        $sendEmail = $request->input('send_email');
        $nota = sanetizar($request->input('nota'));
        $comando = '';

        try {

            $id = $request->input('id');

            $mercurio34 = (new Mercurio34)->findFirst(" id='{$id}' and estado='A' ");
            if (! $mercurio34) {
                throw new DebugException('Los datos del beneficiario no son validos para procesar.', 501);
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_beneficiario',
                    'params' => $mercurio34->getNumdoc(),
                ]
            );

            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al buscar al beneficiario en Sisuweb', 501);
            }

            $out = $procesadorComando->toArray();
            $beneficiarioSisu = $out['data'];

            $procesadorComando->runCli(
                [
                    'servicio' => 'DeshacerAfiliaciones',
                    'metodo' => 'deshacerAprobacionBeneficiario',
                    'params' => [
                        'cedtra' => $mercurio34->getCedtra(),
                        'numdoc' => $mercurio34->getNumdoc(),
                        'tipo_documento' => $mercurio34->getTipdoc(),
                        'nota' => $nota,
                    ],
                ]
            );

            $comando = $procesadorComando->getLineaComando();
            if ($procesadorComando->isJson() == false) {
                throw new DebugException('Error al procesar el deshacer la aprobación en SisuWeb.', 501);
            }

            $resdev = $procesadorComando->toArray();
            if ($resdev['success'] !== true) {
                throw new DebugException($resdev['message'], 501);
            }

            $datos = $resdev['data'];
            if ($datos['noAction']) {
                $salida = [
                    'success' => false,
                    'msj' => 'No se realizo ninguna acción, el estado del beneficiario no es valido para realizar la acción requerida.',
                    'data' => $beneficiarioSisu,
                ];
            } else {
                // procesar
                if ($action == 'D') {
                    $campos_corregir = '';
                    $this->beneficiarioServices->devolver($mercurio34, $nota, $codest, $campos_corregir);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailDevolver($mercurio34, $this->beneficiarioServices->msjDevolver($mercurio34, $nota));
                    }
                }

                if ($action == 'R') {
                    $this->beneficiarioServices->rechazar($mercurio34, $nota, $codest);
                    if ($sendEmail == 'S') {
                        $notifyEmailServices->emailRechazar($mercurio34, $this->beneficiarioServices->msjRechazar($mercurio34, $nota));
                    }
                }

                if ($action == 'I') {
                    $mercurio34->setEstado('I');
                    $mercurio34->setFecest(Carbon::now()->format('Y-m-d'));
                    $mercurio34->save();
                }

                $salida = [
                    'data' => $beneficiarioSisu,
                    'success' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? true : false,
                    'msj' => ($datos['isDelete'] || $datos['isDeleteTrayecto']) ? 'Se completo el proceso con éxito.' : 'No se realizo el cambio requerido, se debe comunicar al área de soporte de las TICS.',
                    'isDeleteTrayecto' => $datos['isDeleteTrayecto'],
                    'noAction' => $datos['noAction'],
                    'isDelete' => $datos['isDelete'],
                ];
            }
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => 'Error no se pudo realizar el movimiento, ' . $err->getMessage(),
                'comando' => $comando,
                'file' => $err->getFile(),
                'line' => $err->getLine(),
                'isDeleteTrayecto' => false,
                'noAction' => false,
                'isDelete' => false,
            ];
        }

        return $this->renderObject($salida);
    }
}
