<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio06;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio31;
use App\Models\Mercurio39;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\CajaServices\MadresComuniServices;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\NotifyEmailServices;
use App\Services\Utils\Pagination;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApruebaComunitariaController extends ApplicationController
{
    protected $tipopc = 11;

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
     * pagination variable
     *
     * @var Pagination
     */
    protected $pagination;

    /**
     * madreComuniServices variable
     *
     * @var MadreComuniServices
     */
    protected $madreComuniServices;

    /**
     * apruebaSolicitud variable
     *
     * @var ApruebaSolicitud
     */
    protected $apruebaSolicitud;

    public function __construct()
    {
        $this->pagination = new Pagination;
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $this->setResponse('ajax');
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];

        $this->pagination->setters(
            new Srequest(
                [
                    "cantidadPaginas: {$cantidad_pagina}",
                    "query: usuario='{$usuario}' and estado='{$estado}'",
                    "estado: {$estado}",
                ]
            )
        );

        $query = $this->pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_madres', $query, true);

        set_flashdata('filter_params', $this->pagination->filters, true);

        $response = $this->pagination->render(
            new MadresComuniServices
        );

        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction(Request $request, $estado = 'P')
    {
        return $this->buscarAction($request, $estado);
    }

    public function indexAction()
    {
        $this->setParamToView('hide_header', true);
        $campo_field = [
            'cedtra' => 'Cedula',
            'prinom' => 'Nombre',
            'priape' => 'Apellido',
            'fecini' => 'Fecha inicio',
            'fecsol' => 'Fecha solicitud',
        ];

        $params = $this->loadParametrosView();

        return view('cajas.aprobacioncom.index', [
            ...$params,
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Madres Comunitarias',
            'mercurio11' => Mercurio11::get(),
        ]);
    }

    public function buscarAction(Request $request, $estado = 'P')
    {
        $this->setResponse('ajax');
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];
        $query = "usuario='{$usuario}' and estado='{$estado}'";

        $this->pagination->setters(
            new Srequest(
                [
                    "cantidadPaginas: $cantidad_pagina",
                    "pagina: {$pagina}",
                    "query: {$query}",
                    "estado: {$estado}",
                ]
            )
        );

        if (
            get_flashdata_item('filter_madres') != false
        ) {
            $query = $this->pagination->persistencia(get_flashdata_item('filter_params'));
        }

        set_flashdata('filter_madres', $query, true);
        set_flashdata('filter_params', $this->pagination->filters, true);

        $response = $this->pagination->render(
            new MadresComuniServices
        );

        return $this->renderObject($response, false);
    }

    /**
     * inforAction function
     * mostrar la ficha de afiliación de la empresa
     *
     * @return void
     */
    public function inforAction($id = 0)
    {
        $madreComuniServices = new MadresComuniServices;
        if (! $id) {
            return redirect('aprobacioncom/index');
            exit;
        }

        $mercurio39 = Mercurio39::where("id", $id)->first();
        if ($mercurio39->getEstado() == 'A') {
            set_flashdata('success', [
                'msj' => "La empresa {$mercurio39->getNit()}, ya se encuentra aprobada su afiliación. Y no requiere de más acciones.",
                'code' => 200,
            ]);

            return redirect('aprobacioncom/index');
            exit;
        }

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );

        $datos_captura = $procesadorComando->toArray();
        $paramsEmpresa = new ParamsEmpresa;
        $paramsEmpresa->setDatosCaptura($datos_captura);

        $mercurio01 = Mercurio01::first();
        $det_tipo = Mercurio06::where("tipo", $mercurio39->getTipo())->first()->getDetalle();

        $this->setParamToView('adjuntos', $madreComuniServices->adjuntos($mercurio39));
        $this->setParamToView('seguimiento', $madreComuniServices->seguimiento($mercurio39));

        $htmlEmpresa = view('cajas/aprobacioncom/tmp/consulta', [
            'mercurio39' => $mercurio39,
            'mercurio01' => $mercurio01,
            'det_tipo' => $det_tipo,
            '_coddoc' => ParamsEmpresa::getTipoDocumentos(),
            '_calemp' => ParamsEmpresa::getCalidadEmpresa(),
            '_codciu' => ParamsEmpresa::getCiudades(),
            '_codzon' => ParamsEmpresa::getZonas(),
            '_codact' => ParamsEmpresa::getActividades(),
            '_tipsoc' => ParamsEmpresa::getTipoSociedades(),
        ])->render();

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $mercurio39->getCedtra(),
                ],
            ]
        );
        $out = $procesadorComando->toArray();

        if ($out['success']) {
            $empresa_sisuweb = $out['data'];
        } else {
            $empresa_sisuweb = false;
        }

        $params = $this->loadParametrosView($datos_captura);
        $response = [
            'empresa_sisuweb' => $empresa_sisuweb,
            'consulta_empresa' => $htmlEmpresa,
            'mercurio11' => Mercurio11::all(),
            'params' => $params,
            'mercurio39' => $mercurio39,
            'title' => "Solicitud Madre Comunitaria - {$mercurio39->getCedtra()} - {$mercurio39->getEstadoDetalle()}",
        ];
        return $this->renderObject($response, false);
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
        $paramsEmpresa = new ParamsTrabajador;
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());

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
            '_trasin' => ParamsTrabajador::getSindicalizado(),
            '_vivienda' => ParamsTrabajador::getVivienda(),
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

    /**
     * aprobar function
     * Aprobación de empresa
     *
     * @return void
     */
    public function apruebaAction(Request $request)
    {
        $this->setResponse('ajax');
        $user = session()->get('user');
        $acceso = $this->Gener42->count("permiso='62' AND usuario='{$user['usuario']}'");
        if ($acceso == 0) {
            return $this->renderObject(['success' => false, 'msj' => 'El usuario no dispone de permisos de aprobación'], false);
        }

        $this->apruebaSolicitud = $this->services->get('ApruebaSolicitud', true);
        try {
            $postData = $request->all();
            $idSolicitud = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $calemp = 'M';
            $solicitud = $this->apruebaSolicitud->main(
                $calemp,
                $idSolicitud,
                $postData
            );

            $this->db->begin();
            $solicitud->enviarMail($request->input('actapr'), $request->input('feccap'));
            $salida = [
                'success' => true,
                'msj' => 'El registro se completo con éxito',
            ];

            $this->db->commit();
        } catch (DebugException $e) {
            $this->db->rollBack();
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * devolverAction function
     *
     * @return void
     */
    public function devolverAction(Request $request)
    {
        $this->setResponse('ajax');
        $this->madreComuniServices = new MadresComuniServices;
        $notifyEmailServices = new NotifyEmailServices;
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = sanetizar($request->input('nota'));
            $array_corregir = $request->input('campos_corregir');
            $campos_corregir = implode(';', $array_corregir);

            $mercurio39 = Mercurio39::where("id", $id)->first();
            if ($mercurio39->getEstado() == 'D') {
                throw new DebugException('El registro ya se encuentra devuelto, no se requiere de repetir la acción.', 201);
            }

            $today = Carbon::now();
            Mercurio39::where("id", $id)->update([
                "estado" => "D",
                "motivo" => $nota,
                "codest" => $codest,
                "fecest" => $today->format('Y-m-d H:i:s'),
            ]);

            $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item');
            $mercurio10 = new Mercurio10;

            $notifyEmailServices->emailDevolver(
                $mercurio39,
                $this->madreComuniServices->msjDevolver($mercurio39, $nota)
            );

            $salida = [
                'success' => true,
                'msj' => 'El proceso se ha completado con éxito',
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * rechazarAction function
     *
     * @return void
     */
    public function rechazarAction(Request $request)
    {
        $this->setResponse('ajax');
        $notifyEmailServices = new NotifyEmailServices;
        $this->madreComuniServices = new MadresComuniServices;
        try {
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = sanetizar($request->input('nota'));
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');

            $mercurio39 = Mercurio39::where("id", $id)->first();

            if ($mercurio39->getEstado() == 'X') {
                throw new DebugException('El registro ya se encuentra rechazado, no se requiere de repetir la acción.', 201);
            }

            $this->madreComuniServices->rechazar($mercurio39, $nota, $codest);

            $notifyEmailServices->emailRechazar($mercurio39, $this->madreComuniServices->msjRechazar($mercurio39, $nota));

            $salida = [
                'success' => true,
                'msj' => 'El proceso se ha completado con éxito',
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function borrarFiltroAction()
    {
        $this->setResponse('ajax');
        set_flashdata('filter_madres', false, true);
        set_flashdata('filter_params', false, true);

        return $this->renderObject([
            'success' => true,
            'query' => get_flashdata_item('filter_madres'),
            'filter' => get_flashdata_item('filter_params'),
        ]);
    }
}
