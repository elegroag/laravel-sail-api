<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio01;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio11;
use App\Models\Mercurio31;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Aprueba\ApruebaDatosTrabajador;
use App\Services\CajaServices\UpDatosTrabajadorService;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\Pagination;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApruebaUpTrabajadorController extends ApplicationController
{
    protected $tipopc = '14';

    protected $db;

    protected $user;

    protected $tipo;

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $this->setResponse('ajax');
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

        set_flashdata('filter_datos_empresa', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);
        $response = $pagination->render(new UpDatosTrabajadorService);

        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction($estado = 'P')
    {
        // $this->buscarAction($estado);
    }

    public function indexAction()
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'documento' => 'Documento',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.actualizatra.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba actualización',
            'buttons' => ['F'],
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    public function loadParametrosView()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );

        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

        $_codciu = ParamsTrabajador::getCiudades();
        $_ciunac = $_codciu;
        foreach (ParamsTrabajador::getZonas() as $ai => $valor) {
            if ($ai < 19001 && $ai >= 18001) {
                $_codzon[$ai] = $valor;
            }
        }
        $_tipsal = (new Mercurio31)->getTipsalArray();

        return [
            '_ciunac' => $_ciunac,
            '_tipsal' => $_tipsal,
            '_codciu' => $_codciu,
            '_codzon' => $_codzon,
            '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
            '_sexo' => ParamsTrabajador::getSexos(),
            '_estciv' => ParamsTrabajador::getEstadoCivil(),
            '_cabhog' => ParamsTrabajador::getCabezaHogar(),
            '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
            '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
            '_nivedu' => ParamsTrabajador::getNivelEducativo(),
            '_rural' => ParamsTrabajador::getRural(),
            '_tipcon' => ParamsTrabajador::getTipoContrato(),
            '_vivienda' => ParamsTrabajador::getVivienda(),
            '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
            '_trasin' => ParamsTrabajador::getSindicalizado(),
            '_bancos' => ParamsTrabajador::getBancos(),
            '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
            '_cargo' => ParamsTrabajador::getOcupaciones(),
            '_orisex' => ParamsTrabajador::getOrientacionSexual(),
            '_facvul' => ParamsTrabajador::getVulnerabilidades(),
            '_peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
            '_vendedor' => ParamsTrabajador::getVendedor(),
            '_empleador' => ParamsTrabajador::getEmpleador(),
            '_tippag' => ParamsTrabajador::getTipoPago(),
            '_tipcue' => ParamsTrabajador::getTipoCuenta(),
            '_giro' => ParamsTrabajador::getGiro(),
            '_codgir' => ParamsTrabajador::getCodigoGiro(),
            '_bancos' => ParamsTrabajador::getBancos(),
            'tipo' => 'T',
            'tipopc' => $this->tipopc,
        ];
    }

    public function buscarAction(Request $request, $estado = 'P')
    {
        $this->setResponse('ajax');
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];
        $query_str = ($estado == 'T') ? " estado='{$estado}'" : "usuario='{$usuario}' and estado='{$estado}'";

        $pagination = new Pagination(
            new Srequest([
                'cantidadPaginas' => $cantidad_pagina,
                'pagina' => $pagina,
                'query' => $query_str,
                'estado' => $estado,
            ])
        );

        if (get_flashdata_item('filter_empresa') != false) {
            $query = $pagination->persistencia(get_flashdata_item('filter_params'));
        } else {
            $query = $pagination->filter(
                $request->input('campo'),
                $request->input('condi'),
                $request->input('value')
            );
        }

        set_flashdata('filter_empresa', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new UpDatosTrabajadorService);

        return $this->renderObject($response, false);
    }

    /**
     * inforAction function
     *
     * @return void
     */
    public function inforAction(Request $request)
    {
        try {
            $upServices = new UpDatosTrabajadorService;
            $id = $request->input('id');
            if (! $id) {
                throw new DebugException('Error se requiere del id independiente', 501);
            }

            $mercurio47 = Mercurio47::where("id", $id)->where("tipo_actualizacion", 'T')->first();
            $mercurio33 = Mercurio33::where("actualizacion", $id)->get();
            $dataItems = [];

            foreach ($mercurio33 as $row) {
                $campo = $row->getCampo();
                $dataItems["{$campo}"] = $row->getValor();
            }

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );

            $datos_captura = $ps->toArray();
            $paramsIndependiente = new ParamsTrabajador;
            $paramsIndependiente->setDatosCaptura($datos_captura);

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_trabajador',
                    'params' => $mercurio47->getDocumento(),
                ]
            );
            $sout = $ps->toArray();
            $datosTraSisu = ($sout['success'] == true) ? $sout['data'] : false;

            $datostra = array_merge($datosTraSisu, $mercurio47->getArray(), $dataItems);

            $htmlEmpresa = view('cajas/actualizatra/tmp/consulta', [
                'datostra' => $datostra,
                'dataItems' => $dataItems,
                'mercurio01' => Mercurio01::first(),
                'det_tipo' => Mercurio06::where("tipo", $mercurio47->getTipo())->first()->getDetalle(),
                '_coddoc' => ParamsTrabajador::getTiposDocumentos(),
                '_codciu' => ParamsTrabajador::getCiudades(),
                '_codzon' => ParamsTrabajador::getZonas(),
                '_sexos' => ParamsTrabajador::getSexos(),
                '_estciv' => ParamsTrabajador::getEstadoCivil(),
                '_cabhog' => ParamsTrabajador::getCabezaHogar(),
                '_captra' => ParamsTrabajador::getCapacidadTrabajar(),
                '_tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                '_nivedu' => ParamsTrabajador::getNivelEducativo(),
                '_rural' => ParamsTrabajador::getRural(),
                '_tipcon' => ParamsTrabajador::getTipoContrato(),
                '_vivienda' => ParamsTrabajador::getVivienda(),
                '_tipafi' => ParamsTrabajador::getTipoAfiliado(),
                '_trasin' => ParamsTrabajador::getSindicalizado(),
                '_bancos' => ParamsTrabajador::getBancos(),
            ])->render();

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio47->getDocumento(),
                    ],
                ]
            );
            $out = $ps->toArray();

            if ($out['success']) {
                $empresa_sisuweb = $out['data'];
            } else {
                $empresa_sisuweb = false;
            }
            $response = [
                'success' => true,
                'data' => $mercurio47->getArray(),
                'mercurio11' => Mercurio11::all(),
                'consulta' => $htmlEmpresa,
                'adjuntos' => $upServices->adjuntos($mercurio47),
                'seguimiento' => $upServices->seguimiento($mercurio47),
                'campos_disponibles' => $mercurio47->CamposDisponibles(),
                'empresa_sisuweb' => $empresa_sisuweb,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function apruebaAction(Request $request)
    {
        $this->setResponse('ajax');
        $debuginfo = [];
        try {
            try {
                $user = session()->get('user');
                $acceso = (new Gener42)->count('*', "conditions: permiso='62' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(['success' => false, 'msj' => 'El usuario no dispone de permisos de aprobación'], false);
                }
                $idSolicitud = $request->input('id');
                $apruebaSolicitud = new ApruebaDatosTrabajador;
                $this->db->begin();
                $apruebaSolicitud->findSolicitud($idSolicitud);
                $apruebaSolicitud->findSolicitante();
                $apruebaSolicitud->procesar($_POST);
                $this->db->commit();
                $apruebaSolicitud->enviarMail($request->input('actapr'), $request->input('fecapr'));
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

    public function rechazarAction(Request $request)
    {
        try {
            $id = $request->input('id');
            $nota = $request->input('nota');
            $codest = $request->input('codest');

            $response = $this->db->begin();
            $today = Carbon::now();
            $mercurio33 = Mercurio33::where("id", $id)->first();
            $mercurio33->update([
                'estado' => 'X',
                'motivo' => $nota,
                'codest' => $codest,
                'fecest' => $today->format('Y-m-d H:i:s'),
            ]);

            $mercurio07 = Mercurio07::whereRaw("tipo='{$mercurio33->getTipo()}' and documento = '{$mercurio33->getDocumento()}'")->first();
            $asunto = 'Actualizacion de datos';
            $msj = 'Se rechazo la actualizacion de datos';
            $senderEmail = new SenderEmail(
                new Srequest([
                    'email_emisor' => $mercurio07->getEmail(),
                    'email_clave' => $mercurio07->getClave(),
                    'asunto' => $asunto,
                ])
            );
            $senderEmail->send($mercurio07->getEmail(), $msj);

            $this->db->commit();
            $response = 'Movimiento Realizado Con Exito';
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = 'No se pudo realizar el movimiento';
        }
        return $this->renderObject($response, false);
    }
}
